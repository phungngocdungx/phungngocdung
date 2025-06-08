<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\MailAccount;
use Webklex\PHPIMAP\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Webklex\IMAP\Facades\Client as ClientFacade;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;

class FetchMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account;
    public $timeout = 720;
    public $tries = 2;

    public function __construct(MailAccount $account)
    {
        $this->account = $account;
    }

    protected function jobIdIfAvailable(): string
    {
        return $this->job ? $this->job->getJobId() : 'N/A';
    }

    protected function processSubject(Message $message): string
    {
        $jobId = $this->jobIdIfAvailable();
        $messageIdForLog = $message->getMessageId() ?? 'N/A_SubjectProcessing';
        $rawSubjectFromLibrary = (string)$message->getSubject();
        $subjectToSave = $rawSubjectFromLibrary;

        if (str_contains($rawSubjectFromLibrary, '=?') && str_contains($rawSubjectFromLibrary, '?=')) {
            Log::info("[Job ID: {$jobId}] Subject for MessageID {$messageIdForLog} appears MIME encoded. Raw: '{$rawSubjectFromLibrary}'. Attempting mb_decode_mimeheader.");
            $decodedSubjectAttempt = mb_decode_mimeheader($rawSubjectFromLibrary);
            if (!empty($decodedSubjectAttempt) && $decodedSubjectAttempt !== $rawSubjectFromLibrary) {
                $subjectToSave = $decodedSubjectAttempt;
                Log::info("[Job ID: {$jobId}] Decoded subject for MessageID {$messageIdForLog}: '{$subjectToSave}'");
            } else {
                Log::warning("[Job ID: {$jobId}] mb_decode_mimeheader for MessageID {$messageIdForLog} did not change subject or returned empty. Using raw: '{$rawSubjectFromLibrary}'");
            }
        }
        return empty($subjectToSave) ? '(Không có tiêu đề)' : $subjectToSave;
    }

    protected function processBodyHtml(Message $message): ?string
    {
        $jobId = $this->jobIdIfAvailable(); // Lấy jobId để dùng trong log
        $messageIdForLog = $message->getMessageId() ?? 'N/A_HtmlProcessing';
        $htmlBody = $message->getHTMLBody();

        if (is_null($htmlBody) || trim($htmlBody) === '') {
            Log::info("[Job ID: {$jobId}] getHTMLBody() returned empty/null for MessageID {$messageIdForLog}. Attempting to find HTML part manually.");
            $parts = $message->getParts();
            $foundHtmlPartContent = null;

            foreach ($parts as $key => $part) {
                if (!is_object($part)) {
                    Log::warning("[Job ID: {$jobId}] Item at key '{$key}' in message parts for MessageID {$messageIdForLog} is not an object. Type: " . gettype($part) . ". Skipping this item. Content: " . print_r($part, true));
                    continue;
                }

                /** @var \Webklex\PHPIMAP\Part $part */
                if (strtoupper($part->subtype) === 'HTML' && !empty($part->content)) {
                    $sizeDisplay = 'N/A';
                    $dispositionDisplay = 'N/A';

                    try {
                        if (property_exists($part, 'disposition') && $part->disposition !== null) {
                            $dispositionDisplay = (string)$part->disposition;
                        }

                        if (property_exists($part, 'size')) {
                            $currentSize = $part->size;
                            if ($currentSize !== null) {
                                if (is_scalar($currentSize)) {
                                    $sizeDisplay = (string)$currentSize;
                                } else {
                                    $type = gettype($currentSize);
                                    $sizeDisplay = "[Type: " . $type . "]";
                                    Log::warning("[Job ID: {$jobId}] Part 'size' is not scalar for MessageID {$messageIdForLog}. Type: {$type}. Part subtype: {$part->subtype}, Disposition: {$dispositionDisplay}.");
                                }
                            } else {
                                Log::info("[Job ID: {$jobId}] Part 'size' property is null for MessageID {$messageIdForLog}. Part subtype: {$part->subtype}, Disposition: {$dispositionDisplay}.");
                            }
                        } else {
                            Log::warning("[Job ID: {$jobId}] Part 'size' property does not exist for MessageID {$messageIdForLog}. Part subtype: {$part->subtype}, Disposition: {$dispositionDisplay}.");
                        }
                    } catch (\Throwable $e) {
                        Log::error("[Job ID: {$jobId}] Exception while accessing part properties (size/disposition) for MessageID {$messageIdForLog}: " . $e->getMessage() . ". Part subtype: {$part->subtype}.");
                    }

                    if (empty($part->disposition) || strtoupper($dispositionDisplay) !== 'ATTACHMENT') {
                        Log::info("[Job ID: {$jobId}] Found a non-attachment HTML part manually for MessageID {$messageIdForLog}. Size: {$sizeDisplay} bytes. Disposition: {$dispositionDisplay}. Using its content.");
                        $foundHtmlPartContent = $part->content;
                        break;
                    } else {
                        Log::info("[Job ID: {$jobId}] Found an HTML part for MessageID {$messageIdForLog}, but it's an attachment. Disposition: {$dispositionDisplay}, Size: {$sizeDisplay}. Skipping as primary HTML body.");
                    }
                }
            }

            if (!empty($foundHtmlPartContent)) {
                Log::info("[Job ID: {$jobId}] Using manually found HTML part content for MessageID {$messageIdForLog}.");
                $htmlBody = $foundHtmlPartContent;
            } else {
                Log::warning("[Job ID: {$jobId}] Manual search for HTML part did not yield usable content for MessageID {$messageIdForLog}. HTML body will remain null/empty.");
            }
        }

        if (!is_null($htmlBody) && trim($htmlBody) !== '') {
            if (!mb_check_encoding($htmlBody, 'UTF-8')) {
                Log::warning("[Job ID: {$jobId}] HTML body for MessageID {$messageIdForLog} might not be valid UTF-8 before entity decoding. First 100 chars (control chars replaced): " . substr(preg_replace('/[\x00-\x1F\x7F]/', '?', $htmlBody), 0, 100));
            }
            return html_entity_decode($htmlBody, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        Log::info("[Job ID: {$jobId}] No HTML body content found or processed for MessageID {$messageIdForLog}.");
        return null;
    }

    protected function processBodyText(Message $message, ?string $fromAddress): ?string
    {
        $jobId = $this->jobIdIfAvailable();
        $messageIdForLog = $message->getMessageId() ?? 'N/A_TextProcessing';
        $rawTextBody = $message->getTextBody();

        if (empty($rawTextBody)) {
            return null;
        }

        $cleanedTextBody = $rawTextBody;
        $cleanedTextBody = preg_replace("/\r\n|\r/", "\n", $cleanedTextBody);
        $cleanedTextBody = trim($cleanedTextBody);

        $isFacebookEmail = false;
        if ($fromAddress) {
            $fromAddressLower = strtolower($fromAddress);
            if (str_contains($fromAddressLower, 'facebook.com') || str_contains($fromAddressLower, 'fb.com') || str_contains($fromAddressLower, 'meta.com')) {
                $isFacebookEmail = true;
            }
        }

        if ($isFacebookEmail) {
            Log::info("[Job ID: {$jobId}] Applying Facebook specific text cleanup for MessageID {$messageIdForLog} from {$fromAddress}.");

            $lines = explode("\n", $cleanedTextBody);
            $outputLines = [];
            $contentStarted = false;
            $skipFooter = false;

            $dividerPattern = '/^={10,}\s*$/';
            $urlHeavyLinePattern = '/^https?:\/\/\S{20,}/i';
            $facebookUnsubscribePatterns = [
                '/thư này đã được gửi đến/i',
                '/vui lòng nhấp vào liên kết bên dưới để hủy đăng ký/i',
                '/meta platforms, inc., attention: community support/i',
                '/vui lòng không chuyển tiếp email này/i',
                '/truy cập vào liên kết bên dưới để tìm hiểu thêm/i',
            ];
            $facebookNotificationPatterns = [
                '/^xem lại lần đăng nhập/i',
                '/^quản lý cảnh báo/i',
                '/^để tăng cường bảo mật tài khoản của bạn/i'
            ];
            $mainContentMarkers = [
                '/^xin chào \S+,/i',
                '/chúng tôi nhận thấy một lần đăng nhập/i',
            ];

            foreach ($lines as $line) {
                $trimmedLine = trim($line);
                if ($skipFooter) continue;

                foreach ($facebookUnsubscribePatterns as $pattern) {
                    if (preg_match($pattern, $trimmedLine)) {
                        $skipFooter = true;
                        break;
                    }
                }
                if ($skipFooter) continue;
                if (preg_match($dividerPattern, $trimmedLine)) continue;

                if (!$contentStarted) {
                    $isBoilerplateNotification = false;
                    foreach ($facebookNotificationPatterns as $pattern) {
                        if (preg_match($pattern, $trimmedLine)) {
                            $isBoilerplateNotification = true;
                            break;
                        }
                    }
                    if ($isBoilerplateNotification) continue;
                    if (preg_match($urlHeavyLinePattern, $trimmedLine) && strlen($trimmedLine) > 40) continue;

                    foreach ($mainContentMarkers as $pattern) {
                        if (preg_match($pattern, $trimmedLine)) {
                            $contentStarted = true;
                            break;
                        }
                    }
                }

                if ($contentStarted || (!empty($trimmedLine) && !$skipFooter)) {
                    if (!$contentStarted && !empty($trimmedLine)) {
                        $isLikelyContent = true;
                        if (preg_match($urlHeavyLinePattern, $trimmedLine) && strlen($trimmedLine) > 60) $isLikelyContent = false;
                        if ($isLikelyContent) $contentStarted = true;
                    }
                    if ($contentStarted) $outputLines[] = $line;
                }
            }

            $cleanedTextBody = implode("\n", $outputLines);
            $cleanedTextBody = trim(preg_replace("/\n{3,}/", "\n\n", $cleanedTextBody));

            if (empty($cleanedTextBody) && !empty($rawTextBody)) {
                Log::warning("[Job ID: {$jobId}] Facebook text cleanup resulted in empty body for MessageID {$messageIdForLog}. Reverting to newline-normalized raw text.");
                return trim(preg_replace("/\n{3,}/", "\n\n", preg_replace("/\r\n|\r/", "\n", $rawTextBody)));
            }
            Log::info("[Job ID: {$jobId}] Facebook text body cleaned up for MessageID {$messageIdForLog}. Length before: " . strlen($rawTextBody) . ", after: " . strlen($cleanedTextBody));
        } else {
            $cleanedTextBody = trim(preg_replace("/\n{3,}/", "\n\n", $cleanedTextBody));
        }
        return $cleanedTextBody;
    }

    public function handle(): void
    {
        $jobId = $this->jobIdIfAvailable();
        Log::info("[Job ID: {$jobId}] 🚀 Bắt đầu FetchMailJob cho tài khoản: {$this->account->email} (ID: {$this->account->id})");

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
            Log::info("[Job ID: {$jobId}] ✅ Kết nối IMAP thành công cho: {$this->account->email}");

            $folder = $client->getFolder('INBOX');

            // --- BẮT ĐẦU LOGIC ĐỒNG BỘ XÓA (XÓA NHỮNG EMAIL KHÔNG CÒN TRÊN SERVER) ---
            Log::info("[Job ID: {$jobId}] Bắt đầu đồng bộ xóa (những email không còn trên server) cho tài khoản: {$this->account->email}.");
            $serverMessagesCollection = $folder->query()->all()->get(); // Lấy tất cả Message objects từ server
            $serverMessageIds = [];
            foreach ($serverMessagesCollection as $msg) {
                $msgId = $msg->getMessageId();
                if ($msgId) {
                    $serverMessageIds[] = (string) $msgId;
                }
            }
            Log::info("[Job ID: {$jobId}] Tìm thấy " . count($serverMessageIds) . " Message-ID trên server.");
            
            $localMessageIds = Email::where('mail_account_id', $this->account->id)
                                ->pluck('message_id')->toArray();
            Log::info("[Job ID: {$jobId}] Tìm thấy " . count($localMessageIds) . " Message-ID trong database local.");

            $messageIdsToDeleteLocally = array_diff($localMessageIds, $serverMessageIds);
            if (!empty($messageIdsToDeleteLocally)) {
                Log::info("[Job ID: {$jobId}] Chuẩn bị xóa " . count($messageIdsToDeleteLocally) . " email khỏi local DB (vì không còn trên server).");
                $chunksToDelete = array_chunk($messageIdsToDeleteLocally, 500);
                $totalDeletedCount = 0;
                foreach ($chunksToDelete as $chunk) {
                    $deletedCount = Email::where('mail_account_id', $this->account->id)
                                       ->whereIn('message_id', $chunk)
                                       ->delete();
                    $totalDeletedCount += $deletedCount;
                }
                Log::info("[Job ID: {$jobId}] Đã xóa thành công {$totalDeletedCount} email khỏi local DB (vì không còn trên server).");
            } else {
                Log::info("[Job ID: {$jobId}] Không có email nào cần xóa khỏi local DB (vì không còn trên server hoặc đã đồng bộ).");
            }
            // --- KẾT THÚC LOGIC ĐỒNG BỘ XÓA ---


            // --- BẮT ĐẦU LOGIC LẤY VÀ CẬP NHẬT 2 EMAIL MỚI NHẤT ---
            Log::info("[Job ID: {$jobId}] Đã có " . $serverMessagesCollection->count() . " tổng số email từ server (dùng để sắp xếp lấy 2 email mới nhất).");

            $sortedMessages = $serverMessagesCollection->sortByDesc(function (Message $message) use ($jobId) {
                /** @var \Webklex\PHPIMAP\Message $message */
                try {
                    $dateAttribute = $message->date;
                    if ($dateAttribute instanceof \Webklex\PHPIMAP\Attribute) {
                        $dateValue = $dateAttribute->first();
                        if ($dateValue instanceof \Carbon\Carbon) {
                            return $dateValue->getTimestamp();
                        }
                    }
                    $directDate = $message->getDate(); // Thử getDate() nếu $message->date không cho Carbon
                    if ($directDate instanceof \Carbon\Carbon) {
                        return $directDate->getTimestamp();
                    }
                    Log::warning("[Job ID: {$jobId}] Không thể lấy Carbon date để sắp xếp cho MessageID: " . ($message->getMessageId() ?? 'N/A_Sort') . ". Dùng giá trị 0.");
                    return 0;
                } catch (\Exception $e) {
                    Log::warning("[Job ID: {$jobId}] Lỗi khi xử lý ngày để sắp xếp cho email MessageID: " . ($message->getMessageId() ?? 'N/A_SortExc') . ". Lỗi: " . $e->getMessage());
                    return 0;
                }
            });

            $messagesToProcess = $sortedMessages->take(2); // Lấy 2 email mới nhất
            Log::info("[Job ID: {$jobId}] Chuẩn bị xử lý nội dung cho tối đa " . $messagesToProcess->count() . " email mới nhất sau khi sắp xếp.");

            $processedAndKeptMessageIds = []; // Lưu ID của 2 email mới nhất được xử lý

            foreach ($messagesToProcess as $message) {
                /** @var \Webklex\PHPIMAP\Message $message */
                $messageId = (string)$message->getMessageId();
                if (empty($messageId)) {
                    Log::warning("[Job ID: {$jobId}] Bỏ qua email không có MessageID trong quá trình xử lý nội dung.");
                    continue;
                }

                // Lấy thông tin ngày tháng từ message, đảm bảo là Carbon nếu có thể
                $dateFromIMAP = null;
                $dateAttrForField = $message->date;
                if ($dateAttrForField instanceof \Webklex\PHPIMAP\Attribute && $dateAttrForField->first() instanceof \Carbon\Carbon) {
                    $dateFromIMAP = $dateAttrForField->first();
                } elseif ($message->getDate() instanceof \Carbon\Carbon) {
                     $dateFromIMAP = $message->getDate();
                }

                $fromAddressFromIMAP = null;
                $fromNameFromIMAP = null;
                $fromHeader = $message->getFrom();
                if ($fromHeader->count() > 0) {
                    $firstFrom = $fromHeader[0];
                    $fromAddressFromIMAP = $firstFrom->mail ?? ($firstFrom->mailbox && $firstFrom->host ? $firstFrom->mailbox . '@' . $firstFrom->host : null);
                    if ($firstFrom->personal) {
                        $fromNameFromIMAP = mb_decode_mimeheader((string)$firstFrom->personal);
                    }
                }
                if(empty($fromAddressFromIMAP)) $fromAddressFromIMAP = 'unknown@example.com';

                $subjectToSave = $this->processSubject($message);
                $htmlBodyToSave = $this->processBodyHtml($message);
                $textBodyToSave = $this->processBodyText($message, $fromAddressFromIMAP);

                Email::updateOrCreate(
                    ['message_id' => $messageId, 'mail_account_id' => $this->account->id],
                    [
                        'subject'     => $subjectToSave,
                        'date'        => $dateFromIMAP, // Lưu ý: $dateFromIMAP là Carbon hoặc null
                        'from'        => $fromAddressFromIMAP,
                        'from_name'   => $fromNameFromIMAP,
                        'body_html'   => $htmlBodyToSave,
                        'body_text'   => $textBodyToSave,
                    ]
                );
                $processedAndKeptMessageIds[] = $messageId; // Thêm vào danh sách giữ lại
            }
            Log::info("[Job ID: {$jobId}] Đã xử lý (updateOrCreate) nội dung cho " . count($processedAndKeptMessageIds) . " email. IDs: " . (!empty($processedAndKeptMessageIds) ? implode(', ', $processedAndKeptMessageIds) : 'Không có'));
            // --- KẾT THÚC LOGIC LẤY VÀ CẬP NHẬT 2 EMAIL MỚI NHẤT ---


            // --- BẮT ĐẦU LOGIC XÓA EMAIL CŨ HƠN 2 EMAIL MỚI NHẤT KHỎI LOCAL DB ---
            if (!empty($processedAndKeptMessageIds)) {
                // Xóa tất cả email của tài khoản này trong DB mà message_id KHÔNG nằm trong danh sách 2 email vừa xử lý
                $deletedOlderCount = Email::where('mail_account_id', $this->account->id)
                                     ->whereNotIn('message_id', $processedAndKeptMessageIds)
                                     ->delete();
                if ($deletedOlderCount > 0) {
                    Log::info("[Job ID: {$jobId}] Đã xóa {$deletedOlderCount} email cũ hơn khỏi local DB cho tài khoản {$this->account->id}, chỉ giữ lại tối đa " . count($processedAndKeptMessageIds) . " email mới nhất.");
                } else {
                    Log::info("[Job ID: {$jobId}] Không có email cũ hơn nào cần xóa thêm cho tài khoản {$this->account->id} (ngoài những email đã được đồng bộ xóa nếu không còn trên server).");
                }
            } else {
                // Trường hợp không xử lý được email nào ở bước trên (ví dụ: server không có email, hoặc cả 2 email đều lỗi không lấy được MessageID)
                // Cân nhắc: Nếu $serverMessageIds cũng rỗng (server không có email nào), thì logic đồng bộ xóa ở trên có thể đã xóa hết local email nếu có.
                // Nếu $serverMessageIds không rỗng nhưng $processedAndKeptMessageIds lại rỗng, có thể có vấn đề khi lấy message_id hoặc xử lý top 2.
                // Không thực hiện xóa `whereNotIn` với mảng rỗng để tránh xóa nhầm toàn bộ email của tài khoản nếu có lỗi ở bước lấy `processedAndKeptMessageIds`.
                Log::warning("[Job ID: {$jobId}] Không có email nào được xác định là 'mới nhất để giữ lại' trong lần chạy này cho tài khoản {$this->account->id}. Sẽ không thực hiện xóa email cũ hơn dựa trên tiêu chí này.");
            }
            // --- KẾT THÚC LOGIC XÓA EMAIL CŨ HƠN 2 EMAIL MỚI NHẤT ---


            Log::info("[Job ID: {$jobId}] 💾 (Job) Đã hoàn tất FetchMailJob cho tài khoản: {$this->account->email}");
            $client->disconnect();

        } catch (ConnectionFailedException $e) {
            $jobIdForError = $this->jobIdIfAvailable();
            Log::error("[Job ID: {$jobIdForError}] ❌ Lỗi kết nối IMAP cho tài khoản {$this->account->email}: " . $e->getMessage());
            $this->release(rand(60, 120));
        } catch (\Exception $e) {
            $jobIdForError = $this->jobIdIfAvailable();
            Log::error("[Job ID: {$jobIdForError}] ❌ Lỗi nghiêm trọng FetchMailJob cho tài khoản {$this->account->email}: " . $e->getMessage() . ". File: " . $e->getFile() . " Line: " . $e->getLine() . ". Trace: " . substr($e->getTraceAsString(), 0, 2000));
        }
    }
}