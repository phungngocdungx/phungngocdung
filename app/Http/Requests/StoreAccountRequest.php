<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return Auth::check();
        return true; // Tạm thời cho test
    }

    public function rules(): array
    {
        return [
            'platform_id' => 'required|exists:platforms,id',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:1',
            'note' => 'nullable|string|max:1000',
            'family_member_id' => 'required|exists:family_members,id', // << THÊM VALIDATION NÀY
        ];
    }

    public function messages(): array
    {
        return [
            'platform_id.required' => 'Vui lòng chọn nền tảng.',
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'family_member_id.required' => 'Vui lòng chọn người dùng để gán tài khoản.', // << THÊM THÔNG BÁO LỖI
            'family_member_id.exists' => 'Người dùng được chọn không hợp lệ.',
        ];
    }
    
    /**
     * Đặt tên error bag để lỗi validation của form này không ảnh hưởng form khác
     * (nếu bạn có nhiều form trên cùng 1 trang sau này)
     */
    public function errorBag(): string
    {
        return 'storeAccount'; // Lỗi sẽ nằm trong $errors->storeAccount->...
    }
}
