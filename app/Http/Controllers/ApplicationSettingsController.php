<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Application_settings; // Giả sử bạn đã tạo model này để tương tác với bảng application_settings

class ApplicationSettingsController extends Controller
{
    /**
     * Có thể bạn sẽ thêm middleware 'auth' ở đây hoặc trong route
     * cho các phương thức quản lý setting.
     * Riêng verifyGlobalPin sẽ được gọi từ user đã đăng nhập.
     */
    // public function __construct()
    // {
    //     // $this->middleware('auth')->except([...]); // Ví dụ
    // }

    /**
     * Verify the submitted global PIN for viewing passwords.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyGlobalPin(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [ // Sử dụng Facade Validator
            'global_pin' => 'required|string|digits:8', // Yêu cầu 8 chữ số
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã PIN phải là 8 chữ số.',
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        $submittedPin = $request->input('global_pin');
        
        // Lấy mã PIN đã mã hóa từ CSDL và giải mã nó
        // Giả sử bạn lưu mã PIN toàn cục với key là 'global_view_pin'
        $storedDecryptedPin = Application_settings::getDecryptedValue('global_view_pin');

        if ($storedDecryptedPin === null) {
            // Trường hợp mã PIN toàn cục chưa được thiết lập trong CSDL
            // \Log::error("Mã PIN toàn cục 'global_view_pin' chưa được thiết lập trong Application_settings.");
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: Mã PIN bảo mật chưa được cấu hình.'
            ], 500); // Lỗi server
        }

        if ($submittedPin === $storedDecryptedPin) {
            // Mã PIN chính xác
            // Tùy chọn: Lưu trạng thái đã xác thực PIN vào session
            session(['global_pin_verified_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Xác thực Mã PIN thành công!']);
        } else {
            // Mã PIN sai
            return response()->json(['success' => false, 'message' => 'Mã PIN không đúng.'], 401); // Unauthorized hoặc 422
        }
    }
    /**
     * Verify the submitted second global PIN for viewing sensitive passwords.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyGlobalPin2(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [ // Sử dụng Facade Validator
            'global_pin2' => 'required|string|digits:8', // Yêu cầu 8 chữ số
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã PIN phải là 8 chữ số ok.',
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        $submittedPin = $request->input('global_pin2');
        
        // Lấy mã PIN đã mã hóa từ CSDL và giải mã nó
        // Giả sử bạn lưu mã PIN toàn cục với key là 'global_view_pin'
        $storedDecryptedPin = Application_settings::getDecryptedValue('global_view_pin');

        if ($storedDecryptedPin === null) {
            // Trường hợp mã PIN toàn cục chưa được thiết lập trong CSDL
            // \Log::error("Mã PIN toàn cục 'global_view_pin' chưa được thiết lập trong Application_settings.");
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: Mã PIN bảo mật chưa được cấu hình.'
            ], 500); // Lỗi server
        }

        if ($submittedPin === $storedDecryptedPin) {
            // Mã PIN chính xác
            // Tùy chọn: Lưu trạng thái đã xác thực PIN vào session
            session(['global_pin_verified_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Xác thực Mã PIN thành công!']);
        } else {
            // Mã PIN sai
            return response()->json(['success' => false, 'message' => 'Mã PIN không đúng.'], 401); // Unauthorized hoặc 422
        }
    }

    // Bạn có thể thêm các phương thức khác vào controller này sau
    // ví dụ: index() để hiển thị form cài đặt, update() để lưu cài đặt...
    // public function index()
    // {
    //     // Lấy các settings và hiển thị form
    // }

    // public function update(Request $request)
    // {
    //     // Validate và lưu các settings, ví dụ như cập nhật 'global_view_pin'
    //     // $newPin = $request->input('new_global_pin');
    //     // if ($newPin) {
    //     //     ApplicationSetting::setEncryptedValue('global_view_pin', $newPin);
    //     // }
    //     // return redirect()->back()->with('success', 'Cài đặt đã được cập nhật.');
    // }
}
