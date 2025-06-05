<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Platform;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;

class AccountController extends Controller
{
    // Quản lý tài khoản của Family Members( Không phải của người dùng đang đăng nhập)
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy TẤT CẢ các tài khoản, cùng với thông tin Platform và FamilyMember liên quan
        $accounts = Account::with(['platform', 'familyMembers']) // Eager load cả hai relationship
            // Để sắp xếp theo tên platform, chúng ta cần join
            // và đảm bảo select đúng các cột của bảng 'accounts'
            ->select('accounts.*') // Quan trọng khi dùng join để tránh xung đột tên cột
            ->join('platforms', 'accounts.platform_id', '=', 'platforms.id')
            ->orderBy('platforms.name', 'asc') // Sắp xếp theo tên platform
            ->orderByDesc('accounts.updated_at') // Sau đó theo ngày cập nhật tài khoản
            ->paginate(15); // Hoặc ->get()
        $platforms = Platform::orderBy('name')->get();
        // Giờ chúng ta không truyền một $familyMember cụ thể nữa,
        // vì view sẽ hiển thị thông tin thành viên cho từng tài khoản.
        // Ví dụ, nếu đang test không login:
        $familyMemberIdToDisplayForTable = 1;
        $displayingFamilyMemberForTable = FamilyMember::find($familyMemberIdToDisplayForTable);
        $accountsData = collect();
        if ($displayingFamilyMemberForTable) {
            $accountsData = $displayingFamilyMemberForTable->accounts()
                ->with('platform')
                ->join('platforms', 'accounts.platform_id', '=', 'platforms.id')
                ->orderBy('platforms.name', 'asc')
                ->orderByDesc('accounts.updated_at')
                ->select('accounts.*')
                ->paginate(15);
        } else {
            session()->flash('status', "Không tìm thấy Family Member với ID: {$familyMemberIdToDisplayForTable} để hiển thị tài khoản.");
        }

        $platforms = Platform::orderBy('name')->get();
        $allFamilyMembers = FamilyMember::orderBy('name')->get(); // << THÊM DÒNG NÀY
        // dd($accounts);
        return view('pages.members', compact('accounts', 'platforms', 'allFamilyMembers', 'displayingFamilyMemberForTable', 'accountsData', 'familyMemberIdToDisplayForTable'));
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

    public function store(StoreAccountRequest $request) // Form Request sẽ validate cả family_member_id
    {
        $validatedData = $request->validated();

        // Lấy FamilyMember dựa trên family_member_id từ form đã validate
        $assignToFamilyMember = FamilyMember::find($validatedData['family_member_id']);

        if (!$assignToFamilyMember) {
            // Dù StoreAccountRequest đã check exists, đây là một lớp phòng vệ nữa
            return redirect()->back()
                ->withInput()
                ->withErrors(['family_member_id' => 'Người dùng được chọn không hợp lệ hoặc không tìm thấy.'], 'storeAccount');
        }

        // CẢNH BÁO MÃ HÓA: Vẫn là vấn đề dùng APP_KEY. Cần thay bằng khóa riêng của người dùng.
        try {
            $account = new Account();
            $account->platform_id = $validatedData['platform_id'];
            $account->encrypted_username = Crypt::encryptString($validatedData['username']);
            $account->encrypted_password = Crypt::encryptString($validatedData['password']);
            if (isset($validatedData['note'])) {
                $account->encrypted_note = Crypt::encryptString($validatedData['note']);
            } else {
                $account->encrypted_note = null;
            }
            // Không cần gán family_member_id trực tiếp vào $account nếu bạn dùng bảng trung gian.
            $account->save();

            // Liên kết account này với family member đã chọn
            $account->familyMembers()->attach($assignToFamilyMember->id);

            return redirect()->route('accounts.index')->with('success', 'Tài khoản đã được thêm thành công cho ' . $assignToFamilyMember->name . '!');
        } catch (\Exception $e) {
            // \Log::error("Lỗi khi tạo tài khoản: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Đã có lỗi xảy ra khi thêm tài khoản. Vui lòng thử lại.'], 'storeAccount');
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
}
