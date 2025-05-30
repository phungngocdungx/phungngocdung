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
        Log::info("🚀 Bắt đầu FetchMailJob cho tài khoản: {$this->account->email}");
        try {
            $client = ClientFacade::make([
                'host'          => $this->account->imap_host,
                'port'          => $this->account->imap_port,
                'encryption'    => $this->account->imap_encryption,
                'validate_cert' => true,
                'username'      => $this->account->email,
                'password'      => $this->account->app_password, // Access decrypted password
                'protocol'      => 'imap',
            ]);

            $client->connect();
            Log::info("✅ Kết nối IMAP thành công cho: {$this->account->email}");

            $folder = $client->getFolder('INBOX');
            $messages = $folder->query()->all()->limit(50)->get();

            foreach ($messages as $message) {
                $messageId = $message->getMessageId();
                if (!$messageId) continue;

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
                    Log::info("Tiêu đề cho MessageID {$messageId} có vẻ được mã hóa MIME. Raw: '{$rawSubjectFromLibrary}'. Đang thử mb_decode_mimeheader.");
                    $decodedSubject = mb_decode_mimeheader($rawSubjectFromLibrary);
                    // Kiểm tra xem mb_decode_mimeheader có trả về chuỗi rỗng hoặc lỗi không
                    if (!empty($decodedSubject) && $decodedSubject !== $rawSubjectFromLibrary) {
                        // Chỉ sử dụng kết quả giải mã nếu nó khác với bản gốc và không rỗng
                        $subjectToSave = $decodedSubject;
                        Log::info("Tiêu đề đã giải mã cho MessageID {$messageId} (sau mb_decode_mimeheader): '{$subjectToSave}'");
                    } else {
                        Log::warning("mb_decode_mimeheader cho MessageID {$messageId} trả về chuỗi rỗng hoặc không thay đổi. Sử dụng raw: '{$rawSubjectFromLibrary}'");
                        // Nếu giải mã không thành công hoặc không thay đổi, giữ lại bản gốc (thư viện có thể đã xử lý)
                        // Hoặc nếu rawSubjectFromLibrary đã là UTF-8 thì mb_decode_mimeheader có thể không thay đổi nó.
                    }
                } else {
                    Log::info("Tiêu đề cho MessageID {$messageId} có vẻ không được mã hóa MIME. Sử dụng raw: '{$subjectToSave}'");
                }

                if (empty($subjectToSave)) { // Đảm bảo subject không bao giờ rỗng
                    $subjectToSave = '(Không có tiêu đề)';
                }
                // ------------------------

                Email::updateOrCreate(
                    ['message_id' => $messageId, 'mail_account_id' => $this->account->id],
                    [
                        'subject' => $subjectToSave,
                        'date'    => $dateFromIMAP,
                        'from'    => $fromAddressFromIMAP,
                        'from_name' => $fromNameFromIMAP,
                        'body_html' => $htmlBodyFromIMAP,
                        'body_text' => $textBodyFromIMAP,
                    ]
                );
            }
            Log::info("💾 (Job) Đã hoàn tất xử lý mail cho {$this->account->email}.");
            $client->disconnect();

        } catch (\Exception $e) {
            Log::error("❌ Lỗi FetchMailJob cho tài khoản {$this->account->email}: " . $e->getMessage());
        }
    }
}
