<?php

namespace App\Console\Commands;

use App\Models\Email;
use App\Models\MailAccount;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client as ClientFacade;

class FetchEmails extends Command
{
    protected $signature = 'email:fetch';
    protected $description = 'Fetch emails from all Gmail accounts via IMAP';

    public function handle()
    {
        $accounts = MailAccount::all();

        foreach ($accounts as $account) {
            $this->info("🔍 Đang lấy mail của: {$account->email}");

            $client = ClientFacade::make([
                'host'          => $account->imap_host,
                'port'          => $account->imap_port,
                'encryption'    => $account->imap_encryption,
                'validate_cert' => true,
                'username'      => $account->email,
                'password'      => $account->app_password,
                'protocol'      => 'imap',
            ]);

            try {
                $client->connect();
                $folder = $client->getFolder('INBOX');

                // Chỉ lấy email chưa có trong DB
                $messages = $folder->query()->all()->limit(50)->get(); // Lấy 50 thư gần nhất

                foreach ($messages as $message) {
                    $messageId = $message->getMessageId();

                    if (!$messageId) continue; // Bỏ qua nếu không có ID

                    Email::updateOrCreate(
                        [
                            'message_id' => $messageId,
                        ],
                        [
                            'mail_account_id' => $account->id,
                            'subject'         => $message->getSubject() ?? '(Không có tiêu đề)',
                            'date'            => $message->getDate(),
                            'from'            => $message->getFrom()[0]->mail ?? 'unknown',
                            'body'            => $message->getHTMLBody() ?? $message->getTextBody() ?? '(Không có nội dung)',
                        ]
                    );

                    $this->info("✅ Đã lưu mail: " . ($message->getSubject() ?? '(Không có tiêu đề)'));
                }
            } catch (\Exception $e) {
                $this->error("❌ Lỗi với tài khoản {$account->email}: " . $e->getMessage());
            }
        }
    }
}
