<?php

namespace App\Http\Controllers\Web\Accounts;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admins = User::role('admin')->get();

        // dd($admins);
        return view('apps.account.admin.index', compact('admins'));
    }

    public function edit($id)
    {
        try {
            // Tải eager loading các mối quan hệ userProfile và userDetail
            $user = User::with('userProfile', 'userDetail')->find($id);

            if (!$user) {
                return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
            }

            // Trả về dữ liệu người dùng, bao gồm thông tin hồ sơ và chi tiết người dùng
            return response()->json($user);
        } catch (Throwable $e) {
            Log::error('Lỗi khi tìm nạp dữ liệu người dùng: ' . $e->getMessage());
            return response()->json(['message' => 'Đã xảy ra lỗi khi tìm nạp dữ liệu.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
            }

            // --- Xác thực và cập nhật bảng users ---
            $userValidationRules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
            ];
            $validatedUserData = $request->validate($userValidationRules); // Sử dụng biến để chứa dữ liệu đã xác thực

            $user->name = $validatedUserData['name'];
            $user->email = $validatedUserData['email'];
            if (!empty($validatedUserData['password'])) { // Kiểm tra nếu mật khẩu có giá trị để cập nhật
                $user->password = Hash::make($validatedUserData['password']);
            }
            $user->save();

            // --- Cập nhật hoặc tạo bảng users_profiles ---
            $userProfile = $user->userProfile()->firstOrNew(['user_id' => $user->id]);
            $profileValidationRules = [
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'ward' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'birthday' => 'nullable|date',
                'gender' => 'nullable|in:nam,nu,khac',
                'facebook_url' => 'nullable|url|max:255',
                'zalo' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'job_title' => 'nullable|string|max:255',
                'avatar' => 'nullable|string|max:255',
            ];
            // Chỉ xác thực và lấy dữ liệu có trong request
            $validatedProfileData = $request->validate($profileValidationRules);

            // Chỉ cập nhật các trường nếu chúng tồn tại trong request (người dùng đã gửi chúng)
            $userProfile->fill($validatedProfileData); // Sử dụng fill với dữ liệu đã xác thực
            $userProfile->save();

            // --- Cập nhật hoặc tạo bảng user_details ---
            $userDetail = $user->userDetail()->firstOrNew(['user_id' => $user->id]);
            $detailValidationRules = [
                'id_number' => 'nullable|string|max:255',
                'id_issued_date' => 'nullable|date',
                'id_issued_place' => 'nullable|string|max:255',
                'marital_status' => 'nullable|string|max:255',
                'nationality' => 'nullable|string|max:255',
                'instagram_url' => 'nullable|url|max:255',
                'linkedin_url' => 'nullable|url|max:255',
                'tiktok_url' => 'nullable|string|max:255',
                'company_name' => 'nullable|string|max:255',
                'company_address' => 'nullable|string|max:255',
                'working_status' => 'nullable|string|max:255',
                'shipping_note' => 'nullable|string',
                'preferred_payment' => 'nullable|string|max:255',
                'points' => 'nullable|integer',
                'slug' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
                'last_login_at' => 'nullable|date', // Đảm bảo định dạng datetime-local phù hợp
                'device_info' => 'nullable|string',
            ];
            $validatedDetailData = $request->validate($detailValidationRules); // Sử dụng biến để chứa dữ liệu đã xác thực

            // Chỉ cập nhật các trường nếu chúng tồn tại trong request
            $userDetail->fill($validatedDetailData); // Sử dụng fill với dữ liệu đã xác thực
            $userDetail->save();

            return response()->json(['message' => 'Thông tin người dùng đã được cập nhật thành công'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Lỗi xác thực khi cập nhật người dùng: ' . json_encode($e->errors()));
            return response()->json(['message' => 'Lỗi xác thực', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            Log::error('Lỗi chung khi cập nhật người dùng: ' . $e->getMessage());
            return response()->json(['message' => 'Đã xảy ra lỗi khi cập nhật dữ liệu.'], 500);
        }
    }
}
