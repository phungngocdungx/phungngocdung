<?php

namespace App\Http\Controllers;

use App\Models\MailAccount;
use Webklex\PHPIMAP\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Webklex\IMAP\Facades\Client as ClientFacade;

class MailController extends Controller
{
    // public function index()
    // {
    //     // Lấy danh sách email từ database
    //     $emails = MailAccount::all();

    //     // Trả về view với biến emails
    //     return view('emails.index', compact('emails'));
    // }

    // Hiển thị các folder của tài khoản để chọn
    // public function folders($id)
    // {
    //     $account = MailAccount::findOrFail($id);

    //     $client = ClientFacade::make([
    //         'host'          => $account->imap_host,
    //         'port'          => $account->imap_port,
    //         'encryption'    => $account->imap_encryption,
    //         'validate_cert' => true,
    //         'username'      => $account->email,
    //         'password'      => $account->app_password,
    //         'protocol'      => 'imap'
    //     ]);

    //     try {
    //         $client->connect();
    //         $folders = $client->getFolders();
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Lỗi kết nối: ' . $e->getMessage());
    //     }

    //     return view('mail.folders', compact('account', 'folders'));
    // }

    // Hiển thị mail trong folder được chọn, mặc định INBOX
    // public function showEmails(Request $request, $id)
    // {
    //     $account = MailAccount::findOrFail($id);

    //     $folderPath = $request->input('folder', 'INBOX'); // folder được chọn, mặc định INBOX

    //     $client = ClientFacade::make([
    //         'host'          => $account->imap_host,
    //         'port'          => $account->imap_port,
    //         'encryption'    => $account->imap_encryption,
    //         'validate_cert' => true,
    //         'username'      => $account->email,
    //         'password'      => $account->app_password,
    //         'protocol'      => 'imap'
    //     ]);

    //     try {
    //         $client->connect();
    //         $folder = $client->getFolder($folderPath);
    //         $messages = $folder->query()->limit(20)->get();
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Lỗi khi lấy mail: ' . $e->getMessage());
    //     }

    //     return view('mail.emails', compact('account', 'messages', 'folderPath'));
    // }
}
