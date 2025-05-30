<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EmailSyncStatusController extends Controller
{
    public function getStatus(Request $request)
    {
        // Đếm số lượng FetchMailJob đang chờ hoặc đã được lấy (reserved_at)
        // và chưa quá 10 phút (để tránh job bị kẹt quá lâu)
        $tenMinutesAgo = now()->subMinutes(10)->getTimestamp();

        $pendingJobCount = DB::table('jobs')
            ->where('payload', 'like', '%"displayName":"App\\\\Jobs\\\\FetchMailJob"%')
            ->where(function ($query) use ($tenMinutesAgo) {
                $query->whereNull('reserved_at') // Đang chờ trong queue
                    ->orWhere('reserved_at', '>', $tenMinutesAgo); // Đã được lấy bởi worker gần đây
            })
            ->count();

        // Log::info("Email sync status check: {$pendingJobCount} pending FetchMailJob(s)."); // Bật nếu cần debug

        if ($pendingJobCount > 0) {
            return response()->json(['status' => 'pending_refresh']);
        } else {
            return response()->json(['status' => 'completed_refresh']);
        }
    }
}
