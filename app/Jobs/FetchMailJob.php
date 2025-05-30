<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\MailAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client as ClientFacade;

class FetchMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account;
    public $timeout = 600; 
    public $tries = 2;     

    public function __construct(MailAccount $account)
    {
        $this->account = $account;
    }

    public function handle(): void
    {
        Log::info("🚀 Bắt đầu FetchMailJob (có đồng bộ xóa, giải mã subject thông minh) cho tài khoản: {$this->account->email}");
        try {
            $client = ClientFacade::make([
                'host'          => $this->account->imap_host,
                'port'          => $this->account->imap_port,
                'encryption'    => $this->account->imap_encryption,
                'validate_cert' => true,
                'username'      => $this->account->email,
                'password'      => $this->account->app_password,
                'protocol'      => 'imap',
            ]);

            $client->connect();
            Log::info("✅ Kết nối IMAP thành công cho: {$this->account->email}");

            $folder = $client->getFolder('INBOX');

            Log::info("⏳ Bắt đầu lấy danh sách tất cả Message-ID từ server cho: {$this->account->email}");
            $allServerMessagesCollection = $folder->query()->all()->get();
            $serverMessageIds = $allServerMessagesCollection->map(function($msg) {
                try {
                    return $msg->getMessageId();
                } catch (\Exception $e) {
                    Log::warning("⚠️ Không thể lấy Message-ID cho một thư trên server: " . $e->getMessage());
                    return null;
                }
            })->filter()->unique()->toArray();
            Log::info("✅ Đã lấy được " . count($serverMessageIds) . " Message-ID hợp lệ từ server.");

            $localMessageIds = Email::where('mail_account_id', $this->account->id)
                                      ->pluck('message_id')
                                      ->toArray();
            Log::info("🔍 Tìm thấy " . count($localMessageIds) . " Message-ID trong database cục bộ cho tài khoản này.");

            $messageIdsToDeleteLocally = array_diff($localMessageIds, $serverMessageIds);

            if (!empty($messageIdsToDeleteLocally)) {
                Log::info("🗑️ Sẽ xóa " . count($messageIdsToDeleteLocally) . " email khỏi DB cục bộ vì không còn trên server.");
                Email::where('mail_account_id', $this->account->id)
                     ->whereIn('message_id', $messageIdsToDeleteLocally)
                     ->delete();
                Log::info("✅ Đã xóa các email không còn trên server khỏi database cục bộ.");
            } else {
                Log::info("👍 Không có email nào trong DB cục bộ cần xóa.");
            }

            Log::info("🔄 Bắt đầu xử lý (updateOrCreate) cho " . $allServerMessagesCollection->count() . " thư lấy được từ server.");

            $sortedMessagesToProcess = $allServerMessagesCollection->filter(function($message){
                return $message->getMessageId() !== null;
            })->sortByDesc(function ($message) {
                try {
                    return $message->getDate();
                } catch (\Exception $e) {
                    return now()->subYears(100);
                }
            });

            foreach ($sortedMessagesToProcess as $message) {
                $messageId = $message->getMessageId();

                $dateFromIMAP = $message->getDate();
                $fromAddressFromIMAP = isset($message->getFrom()[0]) ? $message->getFrom()[0]->mail : 'unknown@example.com';
                $fromNameFromIMAP = isset($message->getFrom()[0]) && $message->getFrom()[0]->personal ? mb_decode_mimeheader((string)$message->getFrom()[0]->personal) : null;
                $htmlBodyFromIMAP = $message->getHTMLBody();
                $textBodyFromIMAP = $message->getTextBody();

                // ---- XỬ LÝ SUBJECT THÔNG MINH ----
                $rawSubjectFromLibrary = (string)$message->getSubject();
                $subjectToSave = $rawSubjectFromLibrary; // Mặc định lấy giá trị gốc

                // Kiểm tra xem subject có vẻ là MIME encoded không
                // Một cách kiểm tra đơn giản là xem nó có chứa "=?" và "?=" không
                if (str_contains($rawSubjectFromLibrary, '=?') && str_contains($rawSubjectFromLibrary, '?=')) {
                    Log::info("Subject for MessageID {$messageId} appears MIME encoded. Raw: '{$rawSubjectFromLibrary}'. Attempting mb_decode_mimeheader.");
                    $decodedSubject = mb_decode_mimeheader($rawSubjectFromLibrary);
                    // Kiểm tra xem mb_decode_mimeheader có trả về chuỗi rỗng hoặc lỗi không
                    if (!empty($decodedSubject) && $decodedSubject !== $rawSubjectFromLibrary) {
                         // Chỉ sử dụng kết quả giải mã nếu nó khác với bản gốc và không rỗng
                        $subjectToSave = $decodedSubject;
                        Log::info("Decoded Subject for MessageID {$messageId} (after mb_decode_mimeheader): '{$subjectToSave}'");
                    } else {
                        Log::warning("mb_decode_mimeheader for MessageID {$messageId} resulted in empty or same string. Using raw: '{$rawSubjectFromLibrary}'");
                        // Nếu giải mã không thành công hoặc không thay đổi, giữ lại bản gốc (thư viện có thể đã xử lý)
                        // Hoặc nếu rawSubjectFromLibrary đã là UTF-8 thì mb_decode_mimeheader có thể không thay đổi nó.
                    }
                } else {
                    Log::info("Subject for MessageID {$messageId} does not appear MIME encoded. Using raw: '{$subjectToSave}'");
                }
                
                if (empty($subjectToSave)) { // Đảm bảo subject không bao giờ rỗng
                    $subjectToSave = '(Không có tiêu đề)';
                }
                // ------------------------

                Email::updateOrCreate(
                    [
                        'message_id' => $messageId,
                        'mail_account_id' => $this->account->id,
                    ],
                    [
                        'subject'         => $subjectToSave,
                        'date'            => $dateFromIMAP,
                        'from'            => $fromAddressFromIMAP,
                        'from_name'       => $fromNameFromIMAP,
                        'body_html'       => $htmlBodyFromIMAP,
                        'body_text'       => $textBodyFromIMAP,
                    ]
                );
            }
            Log::info("💾 (Job) Đã hoàn tất xử lý (updateOrCreate) " . $sortedMessagesToProcess->count() . " mail cho {$this->account->email}.");

            $client->disconnect();
            Log::info("✅ Hoàn thành FetchMailJob cho tài khoản: {$this->account->email}");

        } catch (\Exception $e) {
            Log::error("❌ Lỗi FetchMailJob cho tài khoản {$this->account->email}: " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
        }
    }
}
