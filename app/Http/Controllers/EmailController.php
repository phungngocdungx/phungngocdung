<?php

namespace App\Http\Controllers;

use App\Models\MailAccount;
use App\Models\Email;
use App\Jobs\FetchMailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

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
            $error = "Vui lòng chọn một tài khoản để xem email.";
        } else {
            $error = "❌ Chưa có tài khoản email nào được cấu hình.";
        }
        return view('apps.emails.index', compact('accounts', 'emails', 'selectedAccountId', 'selectedAccount', 'error'));
    }

    public function fetchAllEmails(Request $request)
    {
        Log::info('--- fetchAllEmails method called ---');
        $accounts = MailAccount::all();
        Log::info("Found " . $accounts->count() . " accounts to process for 'fetchAllEmails'.");

        if ($accounts->isEmpty()) {
            Log::info("No accounts found for 'fetchAllEmails'. No jobs will be dispatched.");
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Không có tài khoản nào để lấy email.', 'jobs_dispatched_count' => 0, 'jobs_dispatched' => false], 404);
            }
            return back()->with('error', '❌ Không có tài khoản nào để lấy email.');
        }

        $jobsDispatchedCount = 0;
        foreach ($accounts as $account) {
            Log::info("Attempting to dispatch FetchMailJob for account ID: {$account->id}, Email: {$account->email} (in fetchAllEmails)");
            try {
                FetchMailJob::dispatch($account);
                $jobsDispatchedCount++;
                Log::info("Successfully dispatched FetchMailJob for account ID: {$account->id}");
            } catch (\Exception $e) {
                Log::error("Error dispatching FetchMailJob for account ID: {$account->id} (in fetchAllEmails). Error: " . $e->getMessage());
            }
        }
        Log::info("--- Total jobs dispatched by fetchAllEmails: {$jobsDispatchedCount} ---");

        if ($jobsDispatchedCount > 0 && config('queue.default') !== 'sync') {
            try {
                Log::info("Executing 'queue:work --stop-when-empty' from fetchAllEmails...");
                Artisan::call('queue:work', ['--stop-when-empty' => true, '--queue' => 'default']);
                Log::info("'queue:work --stop-when-empty' command executed successfully from fetchAllEmails.");
            } catch (\Exception $e) {
                Log::error("Error executing 'queue:work' from fetchAllEmails: " . $e->getMessage());
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $jobsDispatchedCount > 0 ? 'Đã gửi yêu cầu làm mới cho ' . $jobsDispatchedCount . ' tài khoản. Email sẽ sớm được cập nhật.' : 'Không có job nào được đưa vào hàng đợi.',
                'jobs_dispatched_count' => $jobsDispatchedCount,
                'jobs_dispatched' => $jobsDispatchedCount > 0,
                'queue_started' => ($jobsDispatchedCount > 0 && config('queue.default') !== 'sync')
            ]);
        }
        return back()->with('status', '📬 Đã đưa ' . $jobsDispatchedCount . ' tài khoản vào hàng đợi xử lý. Email sẽ sớm được cập nhật.');
    }

    /**
     * Fetch emails for a specific account ID.
     * This will dispatch a single job for the specified account.
     */
    public function fetchEmailsForSpecificAccount(Request $request, $accountId)
    {
        Log::info("--- fetchEmailsForSpecificAccount method called for account ID: {$accountId} ---");
        $account = MailAccount::find($accountId);

        if (!$account) {
            Log::warning("Fetch request for non-existent account ID: {$accountId}");
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tài khoản không tồn tại.', 'job_dispatched' => false], 404);
            }
            return back()->with('error', '❌ Tài khoản không tồn tại.');
        }

        try {
            FetchMailJob::dispatch($account);
            Log::info("Successfully dispatched FetchMailJob for specific account ID: {$account->id}, Email: {$account->email}");

            if (config('queue.default') !== 'sync') {
                 try {
                    Log::info("Executing 'queue:work --once' from fetchEmailsForSpecificAccount...");
                    Artisan::call('queue:work', ['--once' => true, '--queue' => 'default']); // Xử lý 1 job rồi dừng
                    Log::info("'queue:work --once' command executed successfully from fetchEmailsForSpecificAccount.");
                } catch (\Exception $e) {
                    Log::error("Error executing 'queue:work --once' from fetchEmailsForSpecificAccount: " . $e->getMessage());
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Đã gửi yêu cầu làm mới cho tài khoản ' . $account->email . '. Email sẽ sớm được cập nhật.',
                    'job_dispatched' => true, // Key là 'job_dispatched' (singular)
                    'account_id' => $account->id
                ]);
            }
            // Quan trọng: Nếu không phải JSON request, khi redirect back() kèm theo session,
            // trang index cần có logic để hiển thị session status/error này.
            return back()->with('status', '📬 Đã đưa tài khoản ' . $account->email . ' vào hàng đợi xử lý. Email sẽ sớm được cập nhật.');

        } catch (\Exception $e) {
            Log::error("Error dispatching FetchMailJob for specific account ID: {$account->id}. Error: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Lỗi khi gửi yêu cầu làm mới.', 'error' => $e->getMessage(), 'job_dispatched' => false], 500);
            }
            return back()->with('error', 'Lỗi khi gửi yêu cầu làm mới: ' . $e->getMessage());
        }
    }

    public function create()
    {
        // Đảm bảo view 'apps.email.create' tồn tại nếu bạn dùng tên này
        return view('apps.email.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:mail_accounts,email',
            'app_password' => 'required|min:6',
            'imap_host' => 'required',
            'imap_port' => 'required|numeric',
            'imap_encryption' => 'required|in:ssl,tls', // Xem xét thêm 'none' nếu cho phép
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        MailAccount::create($request->all());

        // Chuyển hướng về trang danh sách email, sử dụng tên route đã định nghĩa
        return redirect()->route('emails.index')
                         ->with('success', 'Tài khoản email đã được thêm thành công!');
    }

    /**
     * Hiển thị biểu mẫu để chỉnh sửa tài khoản email cụ thể.
     * Laravel sẽ tự động inject MailAccount dựa vào ID trong route.
     * @param  \App\Models\MailAccount  $id // Tên biến khớp với {id} trong route
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MailAccount $id)
    {
        $emailAccount = $id; // Đặt lại tên biến cho rõ ràng

        $request->validate([
            'email' => 'required|email|unique:mail_accounts,email,' . $emailAccount->id,
            'app_password' => 'nullable|min:6', // app_password có thể trống nếu không muốn thay đổi
        ]);

        $emailAccount->email = $request->email;

        if ($request->filled('app_password')) { // Chỉ cập nhật nếu app_password được nhập
            $emailAccount->app_password = $request->app_password;
        }

        // Nếu bạn có các trường IMAP khác trong form và muốn cập nhật:
        // $emailAccount->imap_host = $request->input('imap_host', $emailAccount->imap_host);
        // $emailAccount->imap_port = $request->input('imap_port', $emailAccount->imap_port);
        // $emailAccount->imap_encryption = $request->input('imap_encryption', $emailAccount->imap_encryption);

        $emailAccount->save();

        // --- ĐÂY LÀ PHẦN SỬA LỖI QUAN TRỌNG ---
        // Kiểm tra nếu yêu cầu là AJAX (được gửi từ fetch API của trình duyệt)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Tài khoản email đã được cập nhật thành công!',
                'status' => 'success',
                'account' => $emailAccount // Tùy chọn: trả về dữ liệu tài khoản đã cập nhật
            ], 200); // Mã trạng thái 200 OK
        }

        // Nếu không phải là AJAX request, thì thực hiện redirect như bình thường
        return redirect()->route('emails.index')
            ->with('status', 'Tài khoản email đã được cập nhật thành công.');
    }


    public function getMailAccountForEdit(MailAccount $id)
    {
        // $id đã là đối tượng MailAccount nhờ Route Model Binding
        $account = $id;

        if (!$account) {
            return response()->json(['message' => 'Tài khoản không tồn tại.', 'error' => 'not_found'], 404);
        }

        // Trả về dữ liệu tài khoản dưới dạng JSON
        // LƯU Ý: Không bao giờ trả về mật khẩu ứng dụng đã giải mã công khai.
        // Nếu bạn muốn hiển thị mật khẩu dưới dạng placeholder (ví dụ: ********),
        // bạn không nên gửi mật khẩu thật. User sẽ nhập lại mật khẩu mới nếu muốn thay đổi.
        return response()->json([
            'id' => $account->id,
            'email' => $account->email,
            'imap_host' => $account->imap_host,
            'imap_port' => $account->imap_port,
            'imap_encryption' => $account->imap_encryption,
            // KHÔNG BAO GỒM 'app_password' TRỰC TIẾP VÌ LÝ DO BẢO MẬT.
            // Sẽ chỉ có trường trống trong form cho người dùng nhập mật khẩu mới.
        ]);
    }
    
}