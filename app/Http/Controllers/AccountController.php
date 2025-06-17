<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Platform;
use App\Models\MailAccount;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreAccountRequest;
use App\Models\AccountSocialnetworkDetail;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Contracts\Encryption\DecryptException;

class AccountController extends Controller
{
    // Quản lý tài khoản của Family Members( Không phải của người dùng đang đăng nhập)
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy thông tin người dùng đang đăng nhập
        $user = Auth::user();

        // ID của nền tảng TikTok (giả sử là 6)
        $tiktokPlatformId = 6;

        // Lấy danh sách các tài khoản KHÔNG PHẢI TikTok
        // Sắp xếp theo tên nền tảng, sau đó theo ngày cập nhật
        $otherAccounts = Account::with(['platform', 'familyMembers'])
            ->where('platform_id', '!=', $tiktokPlatformId)
            ->select('accounts.*') // Quan trọng khi dùng join
            ->join('platforms', 'accounts.platform_id', '=', 'platforms.id')
            ->orderBy('platforms.name', 'asc')
            ->orderByDesc('accounts.updated_at')
            ->get(); // Lấy tất cả, nếu muốn phân trang sẽ xử lý phức tạp hơn với tab

        // Lấy danh sách các tài khoản LÀ TikTok, cùng với thông tin chi tiết
        // Sắp xếp theo ngày cập nhật gần nhất
        // --- Xử lý cho Tab TikTok với logic phân quyền MỚI ---

        // 1. Xây dựng câu truy vấn CƠ SỞ cho tài khoản TikTok
        $tiktokQuery = Account::with(['platform', 'familyMembers', 'socialnetworkDetail.mailAccount'])
            ->where('platform_id', $tiktokPlatformId);

        // 2. ÁP DỤNG ĐIỀU KIỆN LỌC DỰA TRÊN VAI TRÒ
        // =========================================================
        // THAY ĐỔI CHÍNH Ở ĐÂY
        // Giả sử tên vai trò trong database của bạn là 'manager'
        // if ($user && $user->hasRole('manage')) {
        //     // dd('Đã vào trong block IF của manager. Sẽ bắt đầu lọc...'); 
        //     // =========================================================
        //     // Thêm điều kiện: chỉ lấy các account có socialnetworkDetail với status là 'active'
        //     $tiktokQuery->whereHas('socialnetworkDetail', function ($query) {
        //         $query->where('status', 'active');
        //     });
        // }
        // Nếu người dùng là 'admin' hoặc vai trò khác, điều kiện if ở trên sẽ sai,
        // và không có bộ lọc nào được áp dụng, do đó Admin sẽ thấy tất cả.
        // dd($user->roles);
        // 3. Thực thi câu truy vấn
        $tiktokAccounts = $tiktokQuery->orderBy('id', 'asc')->get();

        // Lấy các dữ liệu phụ cần cho các modal (Thêm, Sửa)
        $platforms = Platform::orderBy('name')->get();
        $allFamilyMembers = FamilyMember::orderBy('name')->get();
        $mailAccounts = MailAccount::all();
        // dd($otherAccounts);
        return view('pages.members', compact(
            'otherAccounts',
            'tiktokAccounts',
            'platforms',
            'allFamilyMembers',
            'mailAccounts'
            // Các biến khác nếu cần
        ));
    }


    /**
     * Display the specified resource.
     */
    public function show(Account $account) // Route Model Binding vẫn hoạt động
    {
        // Lấy thông tin người dùng đang đăng nhập
        $user = Auth::user();

        // ID của nền tảng TikTok (giả sử là 6)
        $tiktokPlatformId = 6;

        // Lấy danh sách các tài khoản KHÔNG PHẢI TikTok
        // Sắp xếp theo tên nền tảng, sau đó theo ngày cập nhật
        $otherAccounts = Account::with(['platform', 'familyMembers'])
            ->where('platform_id', '!=', $tiktokPlatformId)
            ->select('accounts.*') // Quan trọng khi dùng join
            ->join('platforms', 'accounts.platform_id', '=', 'platforms.id')
            ->orderBy('platforms.name', 'asc')
            ->orderByDesc('accounts.updated_at')
            ->get(); // Lấy tất cả, nếu muốn phân trang sẽ xử lý phức tạp hơn với tab

        // Lấy danh sách các tài khoản LÀ TikTok, cùng với thông tin chi tiết
        // Sắp xếp theo ngày cập nhật gần nhất
        // --- Xử lý cho Tab TikTok với logic phân quyền MỚI ---

        // 1. Xây dựng câu truy vấn CƠ SỞ cho tài khoản TikTok
        $tiktokQuery = Account::with(['platform', 'familyMembers', 'socialnetworkDetail.mailAccount'])
            ->where('platform_id', $tiktokPlatformId);

        // 2. ÁP DỤNG ĐIỀU KIỆN LỌC DỰA TRÊN VAI TRÒ
        // =========================================================
        // THAY ĐỔI CHÍNH Ở ĐÂY
        // Giả sử tên vai trò trong database của bạn là 'manager'
        // if ($user && $user->hasRole('manage')) {
        //     // dd('Đã vào trong block IF của manager. Sẽ bắt đầu lọc...'); 
        //     // =========================================================
        //     // Thêm điều kiện: chỉ lấy các account có socialnetworkDetail với status là 'active'
        //     $tiktokQuery->whereHas('socialnetworkDetail', function ($query) {
        //         $query->where('status', 'active');
        //     });
        // }
        // Nếu người dùng là 'admin' hoặc vai trò khác, điều kiện if ở trên sẽ sai,
        // và không có bộ lọc nào được áp dụng, do đó Admin sẽ thấy tất cả.
        // dd($user->roles);
        // 3. Thực thi câu truy vấn
        $tiktokAccounts = $tiktokQuery->orderBy('id', 'asc')->get();

        // Lấy các dữ liệu phụ cần cho các modal (Thêm, Sửa)
        $platforms = Platform::orderBy('name')->get();
        $allFamilyMembers = FamilyMember::orderBy('name')->get();
        $mailAccounts = MailAccount::all();
        // dd($otherAccounts);
        return view('pages.show', compact(
            'otherAccounts',
            'tiktokAccounts',
            'platforms',
            'allFamilyMembers',
            'mailAccounts'
            // Các biến khác nếu cần
        ));
    }

    // Trong app/Http/Controllers/AccountController.php
    // ... (use statements và các phương thức khác) ...

    public function store(StoreAccountRequest $request)
    {
        // dd($request);
        $validatedData = $request->validated();

        $assignToFamilyMember = FamilyMember::find($validatedData['family_member_id']);

        if (!$assignToFamilyMember) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['family_member_id' => 'Người dùng được chọn không hợp lệ hoặc không tìm thấy.'], 'storeAccount');
        }

        try {
            $account = new Account();
            $account->platform_id = $validatedData['platform_id'];
            $account->username = $validatedData['username']; // Mutator vẫn hoạt động cho username
            $account->password = $validatedData['password']; // Mutator vẫn hoạt động cho password
            $account->note = $validatedData['note'] ?? null; // Mutator vẫn hoạt động cho note

            // Xử lý encrypted_password_2 cho VNeID (thủ công)
            if ($validatedData['platform_id'] == 3 && isset($validatedData['encrypted_password_2'])) {
                $account->encrypted_password_2 = Crypt::encryptString($validatedData['encrypted_password_2']);
            } else {
                $account->encrypted_password_2 = null; // Đảm bảo là null nếu không phải VNeID
            }

            $account->save();

            $account->familyMembers()->attach($assignToFamilyMember->id);

            // Logic để tạo AccountSocialnetworkDetail (chỉ khi là TikTok)
            if ($validatedData['platform_id'] == 6) {
                $socialNetworkDetail = new AccountSocialnetworkDetail();
                $socialNetworkDetail->account_id = $account->id;

                // Luôn reset các trường không liên quan để tránh dữ liệu cũ
                $socialNetworkDetail->mail_account_id = null;
                $socialNetworkDetail->tiktok_user_id = null;
                $socialNetworkDetail->follower_count = 0;
                $socialNetworkDetail->last_login_ip = null;
                $socialNetworkDetail->status = 'active'; // Reset status về mặc định

                if ($validatedData['platform_id'] == 6) { // TikTok
                    $socialNetworkDetail->mail_account_id = $validatedData['mail_account_id'] ?? null;
                    $socialNetworkDetail->tiktok_user_id = $validatedData['tiktok_user_id'];
                    $socialNetworkDetail->follower_count = $validatedData['follower_count'] ?? 0;
                    $socialNetworkDetail->status = $validatedData['status'] ?? 'active';
                } elseif ($validatedData['platform_id'] == 3) { // VNeID
                    // VNeID không có các trường này trong social_network_details theo yêu cầu mới.
                    $socialNetworkDetail->status = 'active'; // Đảm bảo có giá trị vì cột NOT NULL
                }

                $socialNetworkDetail->save();
            }

            return redirect()->route('accounts.index')->with('success', 'Tài khoản đã được thêm thành công cho ' . $assignToFamilyMember->name . '!');
        } catch (\Exception $e) {
            Log::error("Lỗi khi tạo tài khoản: " . $e->getMessage());
            if (isset($account->id)) {
                $account->delete();
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Đã có lỗi xảy ra khi thêm tài khoản. Vui lòng thử lại.'], 'storeAccount');
        }
    }

    public function storeAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:platforms,name',
            'description' => 'nullable|string|max:1000',
            'logo_path' => 'nullable|sometimes|url|max:2048',
        ], [
            'name.required' => 'Tên nền tảng không được để trống.',
            'name.unique' => 'Tên nền tảng này đã tồn tại.',
            'logo_path.url' => 'Đường dẫn logo phải là một URL hợp lệ.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.', 'errors' => $validator->errors()], 422);
        }

        try {
            $platform = Platform::create($validator->validated());
            return response()->json([
                'success' => true,
                'platform' => $platform, // Trả về platform vừa tạo
                'message' => 'Thêm nền tảng thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo platform AJAX: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi server khi thêm nền tảng.'], 500);
        }
    }

    public function edit(Account $account) // Laravel Route Model Binding
    {
        try {
            $account->load(['platform', 'familyMembers', 'socialnetworkDetail']); // Tải eager load socialnetworkDetail

            $accountData = [
                'id' => $account->id,
                'platform_id' => $account->platform_id,
                'username' => $account->username, // Accessor sẽ giải mã
                'password' => $account->password, // Accessor sẽ giải mã (chỉ để đọc trong data-actual-password, không dùng cho input)
                'note' => $account->note,         // Accessor sẽ giải mã
                'encrypted_password_2' => null, // Mặc định là null, sẽ được gán giá trị đã giải mã nếu là VNeID
            ];

            // Nếu là VNeID, giải mã encrypted_password_2
            if ($account->platform_id == 3 && $account->encrypted_password_2) {
                try {
                    $accountData['encrypted_password_2'] = Crypt::decryptString($account->encrypted_password_2);
                } catch (DecryptException $e) {
                    $accountData['encrypted_password_2'] = '[Không thể giải mã]';
                    Log::warning("Lỗi giải mã encrypted_password_2 cho Account ID {$account->id} trong edit: " . $e->getMessage());
                }
            }

            $platformData = null;
            if ($account->platform) {
                $platformData = [
                    'id' => $account->platform->id,
                    'name' => $account->platform->name,
                    'description' => $account->platform->description,
                    'logo_path' => $account->platform->logo_path,
                ];
            }

            $familyMemberData = null;
            if ($account->familyMembers->isNotEmpty()) {
                $firstFamilyMember = $account->familyMembers->first();
                $familyMemberData = [
                    'id' => $firstFamilyMember->id,
                    'name' => $firstFamilyMember->name,
                    'email' => $firstFamilyMember->email,
                    // Không gửi master_password_hash về client
                ];
            }

            // Lấy tất cả platforms, family members, và mail accounts để điền dropdowns
            $allPlatforms = Platform::orderBy('name')->get(['id', 'name']);
            $allFamilyMembers = FamilyMember::all();
            $mailAccounts = MailAccount::all();

            // Lấy socialnetworkDetail chỉ khi nó là TikTok
            $socialnetworkDetail = null;
            if ($account->platform_id == 6 && $account->socialnetworkDetail) {
                $socialnetworkDetail = $account->socialnetworkDetail;
            }

            return response()->json([
                'success' => true,
                'account' => $accountData,
                'platform' => $platformData,
                'family_member' => $familyMemberData,
                'all_platforms' => $allPlatforms,
                'all_family_members' => $allFamilyMembers,
                'mail_accounts' => $mailAccounts, // Pass mail accounts for TikTok
                'socialnetwork_detail' => $socialnetworkDetail, // Pass social network detail for TikTok
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Account not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi trong AccountController@edit: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra khi tải dữ liệu tài khoản.'], 500);
        }
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        $validatedData = $request->validated(); // FormRequest đã validate

        try {
            // TAB 1: Cập nhật Account Info
            $account->platform_id = $validatedData['platform_id'];
            $account->username = $validatedData['username']; // Mutator vẫn hoạt động cho username

            // Cập nhật mật khẩu chính nếu có
            if (!empty($validatedData['password'])) {
                $account->password = $validatedData['password']; // Mutator vẫn hoạt động cho password
            }

            // Xử lý encrypted_password_2 cho VNeID (thủ công, vì không dùng mutator tự động cho cột này)
            if ($validatedData['platform_id'] == 3) {
                 if (isset($validatedData['encrypted_password_2'])) {
                    $account->encrypted_password_2 = Crypt::encryptString($validatedData['encrypted_password_2']);
                 } else {
                    $account->encrypted_password_2 = null; // Nếu trường không có hoặc rỗng khi là VNeID
                 }
            } else {
                 $account->encrypted_password_2 = null; // Clear if platform is changed from VNeID
            }

            $account->note = $validatedData['note'] ?? null; // Mutator vẫn hoạt động cho note
            $account->save(); // Lưu Account trước để đảm bảo account_id tồn tại cho socialnetworkDetail

            // TAB 2: Cập nhật Platform Info (Giữ nguyên logic này)
            if (isset($validatedData['platform_original_id'])) {
                $platformToUpdate = Platform::find($validatedData['platform_original_id']);
                if ($platformToUpdate) {
                    $platformToUpdate->name = $validatedData['platform_name'];
                    $platformToUpdate->description = $validatedData['platform_description'] ?? $platformToUpdate->description;
                    $platformToUpdate->logo_path = $validatedData['platform_logo_path'] ?? $platformToUpdate->logo_path;
                    $platformToUpdate->save();
                }
            }

            // TAB 3: Cập nhật Family Member Info (Giữ nguyên logic này)
            if (isset($validatedData['family_member_id_for_tab3']) && !empty($validatedData['family_member_id_for_tab3'])) {
                $familyMemberToUpdate = FamilyMember::find($validatedData['family_member_id_for_tab3']);
                if ($familyMemberToUpdate) {
                    $familyMemberToUpdate->name = $validatedData['family_member_name'];
                    if (array_key_exists('family_member_email', $validatedData)) {
                        $familyMemberToUpdate->email = $validatedData['family_member_email'];
                    }

                    if (!empty($validatedData['family_member_master_password'])) {
                        if ($validatedData['family_member_master_password'] === ($validatedData['family_member_master_password_confirmation'] ?? null)) {
                            $familyMemberToUpdate->master_password_hash = Hash::make($validatedData['family_member_master_password']);
                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => 'Lỗi validation.',
                                'errors' => ['family_member_master_password_confirmation' => ['Xác nhận mật khẩu chủ không khớp.']]
                            ], 422);
                        }
                    }
                    $familyMemberToUpdate->save();
                }
            }

            // Logic để xử lý AccountSocialnetworkDetail (CHỈ KHI LÀ TIKTOK)
            if ($validatedData['platform_id'] == 6) { // CHỈ LÀ ID 6 (TikTok)
                $socialNetworkDetail = AccountSocialnetworkDetail::firstOrNew(['account_id' => $account->id]);

                // Clear các trường không liên quan để tránh dữ liệu cũ từ nền tảng khác
                $socialNetworkDetail->mail_account_id = null;
                $socialNetworkDetail->tiktok_user_id = null;
                $socialNetworkDetail->follower_count = 0;
                $socialNetworkDetail->last_login_ip = null;
                $socialNetworkDetail->status = 'active'; // Đặt lại về mặc định trước khi gán

                // Gán dữ liệu TikTok
                $socialNetworkDetail->mail_account_id = $validatedData['mail_account_id'] ?? null;
                $socialNetworkDetail->tiktok_user_id = $validatedData['tiktok_user_id'];
                $socialNetworkDetail->follower_count = $validatedData['follower_count'] ?? 0;
                $socialNetworkDetail->status = $validatedData['status'] ?? 'active';

                $socialNetworkDetail->save();
            } else {
                // Nếu nền tảng không phải TikTok (bao gồm VNeID và các nền tảng khác), xóa social network detail hiện có
                if ($account->socialnetworkDetail) {
                    $account->socialnetworkDetail->delete();
                }
            }

            return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
        } catch (\Exception $e) {
            Log::error('Lỗi trong AccountController@update: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
    
    public function showAccTT(Request $request)
    {
        // Lấy thông tin người dùng đang đăng nhập
        $user = Auth::user();

        // ID của nền tảng TikTok (giả sử là 6)
        $tiktokPlatformId = 6;

        // Lấy danh sách các tài khoản KHÔNG PHẢI TikTok
        // Sắp xếp theo tên nền tảng, sau đó theo ngày cập nhật
        $otherAccounts = Account::with(['platform', 'familyMembers'])
            ->where('platform_id', '!=', $tiktokPlatformId)
            ->select('accounts.*') // Quan trọng khi dùng join
            ->join('platforms', 'accounts.platform_id', '=', 'platforms.id')
            ->orderBy('platforms.name', 'asc')
            ->orderByDesc('accounts.updated_at')
            ->get(); // Lấy tất cả, nếu muốn phân trang sẽ xử lý phức tạp hơn với tab

        // Lấy danh sách các tài khoản LÀ TikTok, cùng với thông tin chi tiết
        // Sắp xếp theo ngày cập nhật gần nhất
        // --- Xử lý cho Tab TikTok với logic phân quyền MỚI ---

        // 1. Xây dựng câu truy vấn CƠ SỞ cho tài khoản TikTok, sắp xếp theo id tăng dần
        // (để đảm bảo tính nhất quán với các tài khoản khác)
        $tiktokQuery = Account::with(['platform', 'familyMembers', 'socialnetworkDetail.mailAccount'])
            ->where('platform_id', $tiktokPlatformId);

        // 2. ÁP DỤNG ĐIỀU KIỆN LỌC DỰA TRÊN VAI TRÒ
        // =========================================================
        // THAY ĐỔI CHÍNH Ở ĐÂY
        // Giả sử tên vai trò trong database của bạn là 'manager'
        // if ($user && $user->hasRole('manage')) {
        //     // dd('Đã vào trong block IF của manager. Sẽ bắt đầu lọc...'); 
        //     // =========================================================
        //     // Thêm điều kiện: chỉ lấy các account có socialnetworkDetail với status là 'active'
        //     $tiktokQuery->whereHas('socialnetworkDetail', function ($query) {
        //         $query->where('status', 'active');
        //     });
        // }
        // Nếu người dùng là 'admin' hoặc vai trò khác, điều kiện if ở trên sẽ sai,
        // và không có bộ lọc nào được áp dụng, do đó Admin sẽ thấy tất cả.
        // dd($user->roles);
        // 3. Thực thi câu truy vấn
        $tiktokAccounts = $tiktokQuery->orderBy('id', 'desc')->get();

        // Lấy các dữ liệu phụ cần cho các modal (Thêm, Sửa)
        $platforms = Platform::orderBy('name')->get();
        $allFamilyMembers = FamilyMember::orderBy('name')->get();
        $mailAccounts = MailAccount::all();
        // dd($tiktokAccounts);
        return view('apps.account.tiktok.index', compact('otherAccounts', 'tiktokAccounts', 'platforms', 'allFamilyMembers', 'mailAccounts'));
    }
}
