<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Tab 1 (Account)
            'platform_id' => 'required|exists:platforms,id',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:1|confirmed', // min:1 để test, nên là min:6 hoặc min:8
            'note' => 'nullable|string|max:1000',

            // Tab 2 (Platform)
            'platform_original_id' => 'required|exists:platforms,id', // ID của platform đang sửa
            'platform_name' => 'required|string|max:255',
            'platform_description' => 'nullable|string|max:1000',
            'platform_logo_path' => 'nullable|sometimes|url|max:2048',

            // Tab 3 (Family Member) - các trường này chỉ validate nếu family_member_id_for_tab3 được gửi
            'family_member_id_for_tab3' => 'required|exists:family_members,id',
            'family_member_name' => 'required_with:family_member_id_for_tab3|string|max:255',
            'family_member_email' => 'nullable|email|max:255|unique:family_members,email,' . $this->input('family_member_id_for_tab3'), // unique, bỏ qua chính user này
            'family_member_master_password' => 'nullable|string|min:6|confirmed',
            // 'family_member_master_password_confirmation' đã được xử lý bởi 'confirmed' rule
        ];
    }

    public function messages(): array // Thêm messages cho các trường mới
    {
        return [
            // ... (các messages cũ)
            'family_member_id_for_tab3.required' => 'Thiếu ID người dùng để cập nhật.',
            'family_member_name.required_with' => 'Tên người dùng không được để trống.',
            'family_member_email.email' => 'Email không đúng định dạng.',
            'family_member_email.unique' => 'Email này đã được sử dụng bởi người dùng khác.',
            'family_member_master_password.min' => 'Mật khẩu chủ mới phải có ít nhất :min ký tự.',
            'family_member_master_password.confirmed' => 'Xác nhận mật khẩu chủ mới không khớp.',
        ];
    }
}
