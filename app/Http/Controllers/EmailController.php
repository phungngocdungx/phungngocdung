<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Jobs\FetchMailJob;
use App\Models\MailAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Controller; // Không cần nếu đã extends Controller ở trên
// use Webklex\IMAP\Facades\Client as ClientFacade; // Không dùng trực tiếp ở đây nữa

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $accounts = MailAccount::all();
        $error = session('error'); 

        $defaultAccountId = $accounts->isNotEmpty() ? $accounts->first()->id : null;
        $selectedAccountId = $request->input('account_id', $defaultAccountId);

        $emails = collect();
        $selectedAccount = null;

        if ($selectedAccountId) {
            $selectedAccount = MailAccount::find($selectedAccountId);
            if ($selectedAccount) {
                $emails = Email::where('mail_account_id', $selectedAccountId)
                               ->orderBy('date', 'desc')
                               ->take(20) 
                               ->get();
            } else {
                $error = "❌ Không tìm thấy tài khoản có ID: {$selectedAccountId}.";
            }
        } elseif ($accounts->isNotEmpty()) {
            // $error = "Vui lòng chọn một tài khoản để xem email.";
        } else {
            $error = "❌ Chưa có tài khoản email nào được cấu hình.";
        }
        return view('emails.index', compact('accounts', 'emails', 'selectedAccountId', 'selectedAccount', 'error'));
    }

    public function fetchAllEmails(Request $request) // Thêm Request $request
    {
        Log::info('--- fetchAllEmails method called ---');
        $accounts = MailAccount::all();
        Log::info("Found " . $accounts->count() . " accounts to process.");

        if ($accounts->isEmpty()) {
            Log::info("No accounts found. No jobs will be dispatched.");
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Không có tài khoản nào để lấy email.', 'jobs_dispatched' => false], 404);
            }
            return back()->with('error', '❌ Không có tài khoản nào để lấy email.');
        }

        foreach ($accounts as $account) {
            Log::info("Attempting to dispatch FetchMailJob for account ID: {$account->id}, Email: {$account->email}");
            try {
                FetchMailJob::dispatch($account);
                Log::info("Successfully dispatched FetchMailJob for account ID: {$account->id}");
            } catch (\Exception $e) {
                Log::error("Error dispatching FetchMailJob for account ID: {$account->id}. Error: " . $e->getMessage());
                 if ($request->expectsJson()) {
                    return response()->json(['message' => 'Lỗi khi gửi yêu cầu làm mới.', 'error' => $e->getMessage(), 'jobs_dispatched' => false], 500);
                }
                return back()->with('error', 'Lỗi khi gửi yêu cầu làm mới: ' . $e->getMessage());
            }
        }
        Log::info('--- fetchAllEmails method finished ---');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã gửi yêu cầu làm mới. Email sẽ sớm được cập nhật.', 'jobs_dispatched' => true]);
        }
        return back()->with('status', '📬 Đã đưa tất cả tài khoản vào hàng đợi xử lý. Email sẽ sớm được cập nhật.');
    }
}
