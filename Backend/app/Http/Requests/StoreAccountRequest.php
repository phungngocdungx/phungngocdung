<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform_id' => [
                'required',
                'exists:platforms,id',
            ],
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255',
            'note' => 'nullable|string|max:1000',
            'family_member_id' => 'required|exists:family_members,id',

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
            'encrypted_password_2' => [ // Đã đổi tên trường
                'nullable',
                'string',
                'min:4',
                'max:255',
                Rule::requiredIf(function () {
                    return $this->input('platform_id') == 3; // Bắt buộc nếu platform_id là 3 (VNeID)
                }),
            ],
            // last_login_ip không còn ở đây theo yêu cầu mới cho VNeID
            'last_login_ip' => 'nullable|string|max:45',
        ];
    }

    public function messages(): array
    {
        return [
            'platform_id.required' => 'Nền tảng là bắt buộc.',
            'platform_id.exists' => 'Nền tảng được chọn không hợp lệ.',
            'username.required' => 'Tên đăng nhập là bắt buộc.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'family_member_id.required' => 'Người dùng là bắt buộc.',
            'family_member_id.exists' => 'Người dùng được chọn không hợp lệ.',

            'status.required' => 'Trạng thái tài khoản là bắt buộc cho tài khoản TikTok.',
            'status.in' => 'Trạng thái không hợp lệ. Vui lòng chọn Active, Locked, hoặc Banned.',

            'mail_account_id.required' => 'Email liên kết là bắt buộc cho tài khoản TikTok.',
            'mail_account_id.exists' => 'Email liên kết được chọn không hợp lệ.',
            'tiktok_user_id.required' => 'ID Người dùng TikTok là bắt buộc.',
            'follower_count.required' => 'Số lượng người theo dõi là bắt buộc cho tài khoản TikTok.',
            'follower_count.integer' => 'Số lượng người theo dõi phải là số nguyên.',
            'follower_count.min' => 'Số lượng người theo dõi không thể là số âm.',

            // VNeID-specific messages
            'encrypted_password_2.required' => 'Mật khẩu cấp 2 là bắt buộc cho tài khoản VNeID.', // Đã đổi tên trường
            'encrypted_password_2.min' => 'Mật khẩu cấp 2 phải có ít nhất :min ký tự.', // Đã đổi tên trường
        ];
    }
}
