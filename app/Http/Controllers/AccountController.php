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
        if ($user && $user->hasRole('manage')) {
            // dd('Đã vào trong block IF của manager. Sẽ bắt đầu lọc...'); 
            // =========================================================
            // Thêm điều kiện: chỉ lấy các account có socialnetworkDetail với status là 'active'
            $tiktokQuery->whereHas('socialnetworkDetail', function ($query) {
                $query->where('status', 'active');
            });
        }
        // Nếu người dùng là 'admin' hoặc vai trò khác, điều kiện if ở trên sẽ sai,
        // và không có bộ lọc nào được áp dụng, do đó Admin sẽ thấy tất cả.
        // dd($user->roles);
        // 3. Thực thi câu truy vấn
        $tiktokAccounts = $tiktokQuery->orderByDesc('updated_at')->get();

        // Lấy các dữ liệu phụ cần cho các modal (Thêm, Sửa)
        $platforms = Platform::orderBy('name')->get();
        $allFamilyMembers = FamilyMember::orderBy('name')->get();
        $mailAccounts = MailAccount::all();
        // dd($tiktokAccounts);
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
        // LƯU Ý: BỎ QUA PHẦN KIỂM TRA QUYỀN TRONG GIAI ĐOẠN NÀY
        // Khi tích hợp đăng nhập, bạn cần thêm logic kiểm tra xem người dùng có quyền xem $account này không
        // Ví dụ:
        // $isOwner = $account->familyMembers()->where('family_member_id', Auth::id())->exists();
        // if (!$isOwner && Auth::user()) { // Thêm Auth::user() để kiểm tra nếu có người đăng nhập
        //     abort(403, 'Bạn không có quyền xem tài khoản này.');
        // }

        // Nhờ có Accessors trong Model Account,
        // $account->username, $account->password, $account->note sẽ tự động giải mã
        return view('accounts.show', compact('account'));
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
        $account->load(['platform', 'familyMembers']);

        $accountData = [
            'id' => $account->id,
            'platform_id' => $account->platform_id,
            'username' => $account->username, // Accessor sẽ giải mã
            'note' => $account->note,         // Accessor sẽ giải mã
        ];

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
            // Lấy family member đầu tiên liên kết với account này để sửa
            // Trong thực tế, bạn có thể cần logic phức tạp hơn nếu 1 account thuộc nhiều người
            // và bạn muốn cho phép sửa thông tin của một người cụ thể trong số đó.
            $firstFamilyMember = $account->familyMembers->first();
            $familyMemberData = [
                'id' => $firstFamilyMember->id,
                'name' => $firstFamilyMember->name,
                'email' => $firstFamilyMember->email,
                // Không gửi master_password_hash về client
            ];
        }

        $allPlatforms = Platform::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'account' => $accountData,
            'platform' => $platformData,
            'family_member' => $familyMemberData,
            'all_platforms' => $allPlatforms,
        ]);
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        $validatedData = $request->validated(); // FormRequest đã validate

        try {
            // TAB 1: Cập nhật Account Info
            $account->platform_id = $validatedData['platform_id'];
            $account->encrypted_username = Crypt::encryptString($validatedData['username']);
            if (!empty($validatedData['password'])) {
                $account->encrypted_password = Crypt::encryptString($validatedData['password']);
            }
            if (array_key_exists('note', $validatedData)) {
                $account->encrypted_note = !empty($validatedData['note']) ? Crypt::encryptString($validatedData['note']) : null;
            }
            $account->save();

            // TAB 2: Cập nhật Platform Info
            // CẢNH BÁO: Sửa thông tin platform sẽ ảnh hưởng đến tất cả user dùng chung platform này
            if (isset($validatedData['platform_original_id'])) {
                $platformToUpdate = Platform::find($validatedData['platform_original_id']);
                if ($platformToUpdate) {
                    $platformToUpdate->name = $validatedData['platform_name'];
                    $platformToUpdate->description = $validatedData['platform_description'] ?? $platformToUpdate->description;
                    $platformToUpdate->logo_path = $validatedData['platform_logo_path'] ?? $platformToUpdate->logo_path;
                    $platformToUpdate->save();
                }
            }

            // TAB 3: Cập nhật Family Member Info
            // CẢNH BÁO: Việc cho phép sửa thông tin FamilyMember ở đây cần cân nhắc kỹ về quyền và bảo mật
            if (isset($validatedData['family_member_id_for_tab3']) && !empty($validatedData['family_member_id_for_tab3'])) {
                $familyMemberToUpdate = FamilyMember::find($validatedData['family_member_id_for_tab3']);
                if ($familyMemberToUpdate) {
                    $familyMemberToUpdate->name = $validatedData['family_member_name'];
                    if (array_key_exists('family_member_email', $validatedData)) { // Email có thể là null
                        $familyMemberToUpdate->email = $validatedData['family_member_email'];
                    }

                    // Cập nhật mật khẩu chủ - CẦN CƠ CHẾ BẢO MẬT NGHIÊM NGẶT HƠN
                    // Ví dụ: Yêu cầu nhập mật khẩu chủ cũ, hoặc xác thực 2 yếu tố.
                    // Hiện tại chỉ hash nếu có mật khẩu mới.
                    if (!empty($validatedData['family_member_master_password'])) {
                        if ($validatedData['family_member_master_password'] === $validatedData['family_member_master_password_confirmation']) {
                            $familyMemberToUpdate->master_password_hash = Hash::make($validatedData['family_member_master_password']);
                        } else {
                            // Trả về lỗi validation nếu mật khẩu không khớp (FormRequest nên làm việc này tốt hơn)
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

            return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
        } catch (\Exception $e) {
            Log::error('Lỗi trong AccountController@update: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function showAccTT(Request $request)
    {
        // Lấy ra tất tài khoản có phatform_id = 6
        $accounts = Account::where('platform_id', 6)
            ->with(['platform', 'familyMembers'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        // dd($accounts);
        return view('apps.account.tiktok.index', compact('accounts'));
    }
}
