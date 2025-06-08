<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID tài khoản đang được cập nhật từ route
        $accountId = $this->route('account'); // Nếu route là accounts/{account}, 'account' là tên tham số
        // Hoặc $this->route('id') nếu route là accounts/{id}

        // Lấy ID của family member đang sửa để loại trừ nó khỏi unique email check
        $familyMemberId = $this->input('family_member_id_for_tab3');

        return [
            // Tab 1 (Account)
            'platform_id' => 'required|exists:platforms,id',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed', // min:6 hoặc min:8
            'note' => 'nullable|string|max:1000',

            // Tab 2 (Platform) - Các trường này chỉ cần validate nếu chúng được gửi
            // Nếu bạn chỉ cho phép sửa tên/mô tả/logo của platform hiện tại
            'platform_original_id' => 'required|exists:platforms,id',
            'platform_name' => 'required|string|max:255',
            'platform_description' => 'nullable|string|max:1000',
            'platform_logo_path' => 'nullable|sometimes|url|max:2048',

            // Tab 3 (Family Member)
            'family_member_id_for_tab3' => 'required|exists:family_members,id',
            'family_member_name' => 'required_with:family_member_id_for_tab3|string|max:255',
            'family_member_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('family_members', 'email')->ignore($familyMemberId),
            ],
            'family_member_master_password' => 'nullable|string|min:6|confirmed',

            // Các trường chi tiết mạng xã hội/ứng dụng (cho TikTok và VNeID)
            'status' => [
                'nullable',
                Rule::in(['active', 'locked', 'banned']),
                Rule::requiredIf(function () {
                    return $this->input('platform_id') == 6; // Status chỉ bắt buộc cho TikTok
                }),
            ],

            // TikTok-specific fields
            'mail_account_id' => [
                'nullable',
                Rule::requiredIf(function () {
                    return $this->input('platform_id') == 6;
                }),
                'exists:mail_accounts,id',
            ],
            'tiktok_user_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(function () {
                    return $this->input('platform_id') == 6;
                }),
            ],
            'follower_count' => [
                'nullable',
                'integer',
                'min:0',
                Rule::requiredIf(function () {
                    return $this->input('platform_id') == 6;
                }),
            ],

            // VNeID-specific fields (encrypted_password_2)
            'encrypted_password_2' => [
                'nullable',
                'string',
                'min:4',
                'max:255',
                Rule::requiredIf(function () {
                    return $this->input('platform_id') == 3;
                }),
            ],
            'last_login_ip' => 'nullable|string|max:45', // Giữ lại nullable
        ];
    }

    public function messages(): array
    {
        return [
            'platform_id.required' => 'Nền tảng là bắt buộc.',
            'platform_id.exists' => 'Nền tảng được chọn không hợp lệ.',
            'username.required' => 'Tên đăng nhập là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',

            'platform_original_id.required' => 'Thiếu ID nền tảng gốc.',
            'platform_original_id.exists' => 'ID nền tảng gốc không hợp lệ.',
            'platform_name.required' => 'Tên nền tảng không được để trống.',
            'platform_logo_path.url' => 'Đường dẫn logo phải là một URL hợp lệ.',

            'family_member_id_for_tab3.required' => 'Thiếu ID người dùng để cập nhật.',
            'family_member_id_for_tab3.exists' => 'ID người dùng không hợp lệ.',
            'family_member_name.required_with' => 'Tên người dùng là bắt buộc.',
            'family_member_email.email' => 'Email không đúng định dạng.',
            'family_member_email.unique' => 'Email này đã được sử dụng bởi người dùng khác.',
            'family_member_master_password.min' => 'Mật khẩu chủ mới phải có ít nhất :min ký tự.',
            'family_member_master_password.confirmed' => 'Xác nhận mật khẩu chủ mới không khớp.',

            'status.required' => 'Trạng thái tài khoản là bắt buộc cho tài khoản TikTok.',
            'status.in' => 'Trạng thái không hợp lệ. Vui lòng chọn Active, Locked, hoặc Banned.',

            'mail_account_id.required' => 'Email liên kết là bắt buộc cho tài khoản TikTok.',
            'mail_account_id.exists' => 'Email liên kết được chọn không hợp lệ.',
            'tiktok_user_id.required' => 'ID Người dùng TikTok là bắt buộc.',
            'follower_count.required' => 'Số lượng người theo dõi là bắt buộc cho tài khoản TikTok.',
            'follower_count.integer' => 'Số lượng người theo dõi phải là số nguyên.',
            'follower_count.min' => 'Số lượng người theo dõi không thể là số âm.',

            'encrypted_password_2.required' => 'Mật khẩu cấp 2 là bắt buộc cho tài khoản VNeID.',
            'encrypted_password_2.min' => 'Mật khẩu cấp 2 phải có ít nhất :min ký tự.',
        ];
    }
}
