<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailAccount;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;
use Webklex\IMAP\Facades\Client as ClientFacade;

class CheckMailAccounts extends Command
{
    protected $signature = 'mail:check';
    protected $description = 'Kiểm tra kết nối IMAP của tất cả tài khoản Gmail trong DB';

    public function handle()
    {
        $accounts = MailAccount::all();

        foreach ($accounts as $account) {
            $this->info("🔍 Đang kiểm tra: {$account->email}");

            $client = ClientFacade::make([
                'host'          => $account->imap_host,
                'port'          => $account->imap_port,
                'encryption'    => $account->imap_encryption,
                'validate_cert' => true,
                'username'      => $account->email,
                'password'      => $account->app_password,
                'protocol'      => 'imap'
            ]);

            try {
                $client->connect();
                $inbox = $client->getFolder('INBOX');
                $messages = $inbox->query()->all()->get();

                foreach ($messages as $message) {
                    $this->info($message->getSubject());
                }
            } catch (\Exception $e) {
                $this->error("❌ Lỗi với {$account->email}: " . $e->getMessage());
                $this->line(str_repeat('-', 50));
            }
        }
    }
}
