@extends('layouts.app')
@section('title', 'Tik Tok Accounts Management')
@section('content')

    <head>
        {{-- Các thẻ meta, title, link css khác --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Các script hoặc link khác --}}
    </head>
    <div id="pageMessages" style="position: fixed; top: 80px; right: 20px; z-index: 1056; width: auto; max-width: 400px;">
        {{-- Thông báo sẽ được JS chèn vào đây --}}
    </div>
    {{-- Thông báo cho Login Google, Fb --}}
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <script>
        // JS cho phần thông báo Login Google FB
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                }) // 5 giây
            })
            toastList.forEach(toast => toast.show())
        });
    </script>
    <div class="content">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#!">Home</a></li>
                <li class="breadcrumb-item active">Hệ Thống Quản Lý Tài Khoản Tik Tok</li>
            </ol>
        </nav>
        <h2 class="text-bold text-body-emphasis mb-5">Tik Tok</h2>
        <div id="members"
            data-list='{"valueNames":["customer","email","mobile_number","city","last_active","joined"],"page":10,"pagination":true}'>
            <div class="row align-items-center justify-content-between g-3 mb-4">
                <div class="col col-auto">
                    <div class="search-box">
                        <form class="position-relative"><input class="form-control search-input search" type="search"
                                placeholder="Tìm kiếm" aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
            </div>
            {{-- =============================================== --}}
            {{-- NAV TABS - ĐIỀU HƯỚNG GIỮA CÁC TAB --}}
            {{-- =============================================== --}}
            <ul class="nav nav-tabs" id="accountTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <h4>Mật khẩu tất cả tài khoản: Dung2708@</h4>
                </li>
            </ul>

            {{-- =============================================== --}}
            {{-- TAB CONTENT - NỘI DUNG CỦA TỪNG TAB --}}
            {{-- =============================================== --}}
            <div class="tab-content" id="accountTabsContent">
                {{-- =============================================== --}}
                {{-- TAB PANE 2: TÀI KHOẢN TIKTOK --}}
                {{-- Tái sử dụng cấu trúc HTML gốc cho tab này --}}
                {{-- =============================================== --}}
                <div class="tab-pane fade show active" id="tiktok-accounts-pane" role="tabpanel"
                    aria-labelledby="tiktok-accounts-tab" tabindex="0">
                    <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                        data-list='{"valueNames":["tiktok_user","email","followers","status"],"page":10,"pagination":true}'>
                        <div class="table-responsive scrollbar ms-n1 ps-1">
                            <table class="table table-sm fs-9 mb-0">
                                <thead>
                                    <tr>
                                        <th class="white-space-nowrap fs-9 align-middle ps-0">
                                            <div class="form-check mb-0 fs-8"><input class="form-check-input"
                                                    id="checkbox-bulk-tiktok-select" type="checkbox"
                                                    data-bulk-select='{"body":"tiktok-members-table-body"}' /></div>
                                        </th>
                                        <th class="sort align-middle" scope="col" data-sort="email">EMAIL</th>
                                        <th class="sort align-middle" scope="col" data-sort="email">LẤY MÃ</th>
                                        <th class="sort align-middle" scope="col" data-sort="email">ID Tik Tok</th>
                                        <th class="sort align-middle" scope="col" data-sort="followers">FOLLOWERS</th>
                                        <th class="sort align-middle" scope="col" data-sort="status">TRẠNG THÁI</th>
                                        <th class="sort align-middle" scope="col" data-sort="password"></th>
                                    </tr>
                                </thead>
                                <tbody class="list" id="tiktok-members-table-body">
                                    {{-- Sử dụng biến $tiktokAccounts từ Controller --}}
                                    @forelse ($tiktokAccounts as $account)
                                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                            <td class="fs-9 align-middle ps-0 py-3">
                                                <div class="form-check mb-0 fs-8"><input class="form-check-input"
                                                        type="checkbox" /></div>
                                            </td>
                                            <td class="tiktok_user align-middle white-space-nowrap fw-semibold">
                                                {{ $account->socialnetworkDetail->mailAccount->email ?? '(chưa có)' }}
                                            </td>

                                            <td class="tiktok_user align-middle white-space-nowrap fw-semibold">
                                                @php
                                                    $isInactive =
                                                        isset($account->socialnetworkDetail) &&
                                                        $account->socialnetworkDetail->status !== 'active';
                                                @endphp

                                                <button class="btn btn-success"
                                                    {{ $isInactive ? 'style=pointer-events:none;opacity:0.6;' : '' }}
                                                    {{ $isInactive ? '' : "onclick=window.location.href='" . url('/emails?account_id=' . $account->socialnetworkDetail->mail_account_id) . "'" }}>
                                                    <i class="fas fa-envelope-open-text fa-fw me-2"></i>Lấy mã
                                                </button>
                                            </td>
                                            <td class="tiktok_user align-middle white-space-nowrap fw-semibold">
                                                <a href="https://www.tiktok.com/{{ $account->socialnetworkDetail->tiktok_user_id ?? '(chưa có)' }}"
                                                    style="color: #9FA6BC; text-decoration: none">{{ $account->socialnetworkDetail->tiktok_user_id ?? '(chưa có)' }}</a>
                                            </td>
                                            <td class="followers align-middle white-space-nowrap ">
                                                {{ number_format($account->socialnetworkDetail->follower_count ?? 0) . ' người theo dõi' }}
                                            </td>
                                            <td class="status align-middle white-space-nowrap">
                                                @php
                                                    $status = $account->socialnetworkDetail->status ?? 'active';
                                                    $badgeClass =
                                                        $status === 'active'
                                                            ? 'success'
                                                            : ($status === 'locked'
                                                                ? 'danger'
                                                                : 'warning');
                                                @endphp
                                                <span
                                                    class="badge badge-phoenix fs-10 badge-phoenix-{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                            </td>
                                            <td class="align-middle white-space-nowrap">
                                                {{ $account->username }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">Không có tài khoản TikTok nào.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                            <div class="col-auto d-flex">
                                <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body"
                                    data-list-info="data-list-info"></p>
                            </div>
                            <div class="col-auto d-flex">
                                <button class="page-link" data-list-pagination="prev"><span
                                        class="fas fa-chevron-left"></span></button>
                                <ul class="mb-0 pagination"></ul>
                                <button class="page-link pe-0" data-list-pagination="next"><span
                                        class="fas fa-chevron-right"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Phân trang --}}
                {{-- <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                    <div class="col-auto d-flex">
                        <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p>
                        <a class="fw-semibold" href="#!" data-list-view="*">View all<span
                                class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a
                            class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span
                                class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                    </div>
                    <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span
                                class="fas fa-chevron-left"></span></button>
                        <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span
                                class="fas fa-chevron-right"></span></button>
                    </div>
                </div> --}}
            </div>
        </div>
        @include('partials.footer')
    </div>

    </div>
    {{-- Popup xác thực Mã PIN --}}
    <div class="modal fade" id="verifyPinModal" tabindex="-1" aria-labelledby="verifyPinModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyPinModalLabel">Xác thực Mã PIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="verifyPinForm">
                        <div class="mb-3">
                            <label for="globalPinInput" class="form-label">Nhập Mã PIN (8 chữ số):</label>
                            <input type="password" class="form-control" id="globalPinInput" name="global_pin"
                                maxlength="8" pattern="\d{8}" inputmode="numeric" required autocomplete="off">
                            <div class="invalid-feedback" id="pinErrorMessage" style="display: none;">Mã PIN
                                không đúng hoặc có lỗi.</div>
                        </div>
                        {{-- Trường ẩn để lưu trữ thông tin về account nào đang được yêu cầu --}}
                        <input type="hidden" id="targetPasswordSpanId">
                        <input type="hidden" id="targetPasswordButtonId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="submitPinButton">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Popup thêm tài khoản mới --}}
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountModalLabel">Thêm Tài Khoản Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('accounts.store') }}" method="POST" id="addAccountForm">
                    @csrf
                    <div class="modal-body">
                        {{-- Hiển thị lỗi validation nếu có từ submit form chính --}}
                        @if (isset($errors) && $errors->storeAccount && $errors->storeAccount->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->storeAccount->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- PHẦN CHỌN PLATFORM VÀ THÊM PLATFORM MỚI --}}
                        <div class="mb-3">
                            <label for="add_platform_id" class="form-label">Nền tảng <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select @error('platform_id', 'storeAccount') is-invalid @enderror"
                                    id="add_platform_id" name="platform_id" required>
                                    <option value="" selected disabled>-- Chọn nền tảng --</option>
                                    @if (isset($platforms) && $platforms->count() > 0)
                                        @foreach ($platforms as $platform)
                                            <option value="{{ $platform->id }}"
                                                {{ old('platform_id') == $platform->id ? 'selected' : '' }}>
                                                {{ $platform->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <button class="btn btn-outline-success" type="button" id="btnToggleNewPlatformForm"
                                    title="Tạo nền tảng mới">
                                    <i class="fas fa-plus"></i> Mới
                                </button>
                            </div>
                            @error('platform_id', 'storeAccount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                {{-- Hiển thị lỗi cho platform_id --}}
                            @enderror
                        </div>

                        {{-- Form thêm Platform mới (ban đầu ẩn) --}}
                        <div id="newPlatformFormContainer" class="p-3 border rounded mb-3 shadow-sm bg-light"
                            style="display:none;">
                            <h6 class="mb-3">Tạo Nền tảng mới</h6>
                            <div id="ajaxNewPlatformErrors" class="alert alert-danger" style="display:none;">
                            </div>
                            <div id="ajaxNewPlatformSuccess" class="alert alert-success" style="display:none;"></div>

                            <div class="mb-2">
                                <label for="ajax_new_platform_name" class="form-label form-label-sm">Tên Nền
                                    tảng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="ajax_new_platform_name">
                            </div>
                            <div class="mb-2">
                                <label for="ajax_new_platform_description" class="form-label form-label-sm">Mô
                                    tả</label>
                                <textarea class="form-control form-control-sm" id="ajax_new_platform_description" rows="1"></textarea>
                            </div>
                            <div class="mb-3"> {{-- Tăng mb để có không gian cho nút --}}
                                <label for="ajax_new_platform_logo_path" class="form-label form-label-sm">Logo
                                    URL</label>
                                <input type="url" class="form-control form-control-sm"
                                    id="ajax_new_platform_logo_path" placeholder="https://example.com/logo.png">
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-success btn-sm me-2" id="btnSaveNewPlatformAjax">
                                    <i class="fas fa-save me-1"></i> Lưu Nền tảng
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    id="btnCancelNewPlatform">Hủy</button>
                            </div>
                        </div>
                        {{-- KẾT THÚC PHẦN THÊM PLATFORM MỚI --}}


                        {{-- ... (các trường Username, Password, Note, FamilyMember (nếu có) như bạn đã làm) ... --}}
                        <div class="mb-3">
                            <label for="add_username" class="form-label">Tên đăng nhập (Username/Email) <span
                                    class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control @error('username', 'storeAccount') is-invalid @enderror"
                                id="add_username" name="username" value="{{ old('username') }}" required
                                autocomplete="off">
                            @error('username', 'storeAccount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="add_password" class="form-label">Mật khẩu <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control @error('password', 'storeAccount') is-invalid @enderror"
                                    id="add_password" name="password" required autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleAddPassword"><i
                                        class="fas fa-eye"></i></button>
                            </div>
                            @error('password', 'storeAccount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- START: Fields for platforms with social network details (TikTok, VNeID, etc.) --}}
                        <div id="platform_details_fields" style="display: none;">
                            <h6 class="mb-3 mt-4" id="platform_details_title">Thông tin chi tiết</h6>

                            {{-- Fields for TikTok (specific to TikTok) --}}
                            <div id="tiktok_specific_fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="add_mail_account_id" class="form-label">Email liên kết (nếu có)</label>
                                    <select
                                        class="form-select @error('mail_account_id', 'storeAccount') is-invalid @enderror"
                                        id="add_mail_account_id" name="mail_account_id">
                                        <option value="" selected>-- Chọn email --</option>
                                        @if (isset($mailAccounts) && $mailAccounts->count() > 0)
                                            @foreach ($mailAccounts as $mailAccount)
                                                <option value="{{ $mailAccount->id }}"
                                                    {{ old('mail_account_id') == $mailAccount->id ? 'selected' : '' }}>
                                                    {{ $mailAccount->email }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('mail_account_id', 'storeAccount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="add_tiktok_user_id" class="form-label">ID Người dùng TikTok</label>
                                    <input type="text"
                                        class="form-control @error('tiktok_user_id', 'storeAccount') is-invalid @enderror"
                                        id="add_tiktok_user_id" name="tiktok_user_id"
                                        value="{{ old('tiktok_user_id') }}">
                                    @error('tiktok_user_id', 'storeAccount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="add_follower_count" class="form-label">Số lượng người theo dõi</label>
                                    <input type="number"
                                        class="form-control @error('follower_count', 'storeAccount') is-invalid @enderror"
                                        id="add_follower_count" name="follower_count"
                                        value="{{ old('follower_count', 0) }}" min="0">
                                    @error('follower_count', 'storeAccount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Trạng thái tài khoản cho TikTok (đã di chuyển vào đây) --}}
                                <div class="mb-3">
                                    <label for="add_status" class="form-label">Trạng thái tài khoản</label>
                                    <select class="form-select @error('status', 'storeAccount') is-invalid @enderror"
                                        id="add_status" name="status">
                                        <option value="active"
                                            {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="locked" {{ old('status') == 'locked' ? 'selected' : '' }}>Locked
                                        </option>
                                        <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Banned
                                        </option>
                                    </select>
                                    @error('status', 'storeAccount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> {{-- End tiktok_specific_fields --}}

                            {{-- Fields for VNeID (specific to VNeID - chỉ có encrypted_password_2) --}}
                            <div id="vneid_specific_fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="encrypted_password_2" class="form-label">Mật khẩu cấp 2 (VNeID)</label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control @error('encrypted_password_2', 'storeAccount') is-invalid @enderror"
                                            id="encrypted_password_2" name="encrypted_password_2"
                                            value="{{ old('encrypted_password_2') }}" autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="toggleAddPassword2"><i class="fas fa-eye"></i></button>
                                    </div>
                                    @error('encrypted_password_2', 'storeAccount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> {{-- End vneid_specific_fields --}}

                        </div> {{-- END: platform_details_fields --}}

                        @if (isset($allFamilyMembers) && $allFamilyMembers->count() > 0)
                            <div class="mb-3">
                                <label for="add_family_member_id_for_add_form" class="form-label">Gán cho
                                    Người dùng <span class="text-danger">*</span></label>
                                <select class="form-select @error('family_member_id', 'storeAccount') is-invalid @enderror"
                                    id="add_family_member_id_for_add_form" name="family_member_id" required>
                                    <option value="" selected disabled>-- Chọn người dùng --</option>
                                    @foreach ($allFamilyMembers as $member)
                                        <option value="{{ $member->id }}"
                                            {{ old('family_member_id') == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }} ({{ $member->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('family_member_id', 'storeAccount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="add_note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="add_note" name="note" rows="2">{{ old('note') }}</textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu Tài Khoản</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Popup chỉnh sửa tài khoản --}}
    <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAccountModalLabel">Chỉnh Sửa Mục Tài Khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAccountForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_account_id" name="account_id">
                    <input type="hidden" id="edit_platform_original_id" name="platform_original_id">
                    <input type="hidden" id="edit_family_member_id_for_tab3" name="family_member_id_for_tab3">

                    <div class="modal-body">
                        {{-- Nav tabs --}}
                        <ul class="nav nav-tabs" id="editAccountTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="edit-account-info-tab" data-bs-toggle="tab"
                                    data-bs-target="#editAccountInfo" type="button" role="tab"
                                    aria-controls="editAccountInfo" aria-selected="true">Thông
                                    tin Tài khoản</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="edit-platform-info-tab" data-bs-toggle="tab"
                                    data-bs-target="#editPlatformInfo" type="button" role="tab"
                                    aria-controls="editPlatformInfo" aria-selected="false">Thông tin Nền
                                    tảng</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="edit-family-member-tab" data-bs-toggle="tab"
                                    data-bs-target="#editFamilyMemberInfo" type="button" role="tab"
                                    aria-controls="editFamilyMemberInfo" aria-selected="false">Thông tin Người
                                    dùng</button>
                            </li>
                        </ul>

                        {{-- Tab content --}}
                        <div class="tab-content mt-3" id="editAccountTabContent">
                            {{-- TAB 1: Thông tin Tài Khoản --}}
                            <div class="tab-pane fade show active" id="editAccountInfo" role="tabpanel"
                                aria-labelledby="edit-account-info-tab">
                                <div class="text-danger mb-2" id="editAccountFormErrorsTab1" style="display:none;"></div>
                                <div class="mb-3">
                                    <label for="edit_platform_id" class="form-label">Nền tảng <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_platform_id" name="platform_id" required>
                                        <option value="">-- Chọn nền tảng --</option>
                                        @if (isset($platforms) && $platforms->count() > 0)
                                            @foreach ($platforms as $platform)
                                                <option value="{{ $platform->id }}">{{ $platform->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_username" class="form-label">Tên đăng nhập
                                        (Username/Email) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_username" name="username"
                                        required autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_password" class="form-label">Mật khẩu mới (Để trống nếu
                                        không đổi)</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="edit_password" name="password"
                                            autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="toggleEditPasswordVisibility"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_password_confirmation" class="form-label">Xác nhận mật
                                        khẩu mới</label>
                                    <input type="password" class="form-control" id="edit_password_confirmation"
                                        name="password_confirmation" autocomplete="new-password">
                                </div>

                                {{-- START: Fields for platforms with social network details in EDIT MODAL --}}
                                <div id="editSocialNetworkDetailsFields" style="display: none;">
                                    <h6 class="mb-3 mt-4" id="editPlatformDetailsTitle">Thông tin chi tiết</h6>

                                    {{-- Fields for TikTok (specific to TikTok) --}}
                                    <div id="editTiktokSpecificFields" style="display: none;">
                                        <div class="mb-3">
                                            <label for="edit_mail_account_id" class="form-label">Email liên kết (nếu
                                                có)</label>
                                            <select class="form-select" id="edit_mail_account_id" name="mail_account_id">
                                                <option value="" selected>-- Chọn email --</option>
                                                @if (isset($mailAccounts) && $mailAccounts->count() > 0)
                                                    @foreach ($mailAccounts as $mailAccount)
                                                        <option value="{{ $mailAccount->id }}">
                                                            {{ $mailAccount->email }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_tiktok_user_id" class="form-label">ID Người dùng
                                                TikTok</label>
                                            <input type="text" class="form-control" id="edit_tiktok_user_id"
                                                name="tiktok_user_id">
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_follower_count" class="form-label">Số lượng người theo
                                                dõi</label>
                                            <input type="number" class="form-control" id="edit_follower_count"
                                                name="follower_count" min="0">
                                        </div>
                                        {{-- Trạng thái tài khoản cho TikTok --}}
                                        <div class="mb-3">
                                            <label for="edit_account_status" class="form-label">Trạng thái tài
                                                khoản</label>
                                            <select class="form-select" id="edit_account_status" name="status">
                                                <option value="active">Active</option>
                                                <option value="locked">Locked</option>
                                                <option value="banned">Banned</option>
                                            </select>
                                        </div>
                                    </div> {{-- End editTiktokSpecificFields --}}

                                    {{-- Fields for VNeID (specific to VNeID - chỉ có encrypted_password_2) --}}
                                    <div id="editVneidSpecificFields" style="display: none;">
                                        <div class="mb-3">
                                            <label for="edit_encrypted_password_2" class="form-label">Mật khẩu cấp 2
                                                (VNeID)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control"
                                                    id="edit_encrypted_password_2" name="encrypted_password_2"
                                                    autocomplete="new-password">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    id="toggleEditVneidPassword2"><i class="fas fa-eye"></i></button>
                                                {{-- Đã đổi ID --}}
                                            </div>
                                        </div>
                                    </div> {{-- End editVneidSpecificFields --}}

                                </div> {{-- END: editSocialNetworkDetailsFields --}}

                                <div class="mb-3">
                                    <label for="edit_note" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="edit_note" name="note" rows="3"></textarea>
                                </div>
                            </div>

                            {{-- TAB 2: Thông tin Nền tảng --}}
                            <div class="tab-pane fade" id="editPlatformInfo" role="tabpanel"
                                aria-labelledby="edit-platform-info-tab">
                                <div class="text-danger mb-2" id="editPlatformFormErrorsTab2" style="display:none;">
                                </div>
                                <div class="alert alert-warning mt-2">Lưu ý: Thay đổi ở đây sẽ ảnh hưởng đến
                                    thông tin nền tảng dùng chung.</div>
                                <div class="mb-3">
                                    <label for="edit_platform_name" class="form-label">Tên Nền tảng <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_platform_name"
                                        name="platform_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_platform_description" class="form-label">Mô tả Nền
                                        tảng</label>
                                    <textarea class="form-control" id="edit_platform_description" name="platform_description" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_platform_logo_path" class="form-label">Đường dẫn Logo
                                        (URL)</label>
                                    <input type="url" class="form-control" id="edit_platform_logo_path"
                                        name="platform_logo_path" placeholder="https://example.com/logo.png">
                                </div>
                            </div>

                            {{-- TAB 3: Sửa thông tin Family Member --}}
                            <div class="tab-pane fade" id="editFamilyMemberInfo" role="tabpanel"
                                aria-labelledby="edit-family-member-tab">
                                <div class="text-danger mb-2" id="editFamilyMemberFormErrorsTab3" style="display:none;">
                                </div>
                                <div class="alert alert-info mt-2">Chỉnh sửa thông tin người dùng được liên kết
                                    với tài khoản này.</div>
                                <div class="mb-3">
                                    <label for="edit_fm_name" class="form-label">Tên Người dùng <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_fm_name"
                                        name="family_member_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_fm_email" class="form-label">Email Người dùng</label>
                                    <input type="email" class="form-control" id="edit_fm_email"
                                        name="family_member_email">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_fm_master_password" class="form-label">Mật khẩu chủ mới
                                        (Để trống nếu không đổi)</label>
                                    <input type="password" class="form-control" id="edit_fm_master_password"
                                        name="family_member_master_password" autocomplete="new-password">
                                    <small class="form-text text-warning">Rất cẩn thận khi thay đổi mật khẩu
                                        chủ!</small>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_fm_master_password_confirmation" class="form-label">Xác
                                        nhận mật khẩu chủ mới</label>
                                    <input type="password" class="form-control" id="edit_fm_master_password_confirmation"
                                        name="family_member_master_password_confirmation" autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" id="saveEditAccountButton">Lưu Tất Cả
                            Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- KHAI BÁO BIẾN VÀ KHỞI TẠO CHUNG ---
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            let csrfToken = null;
            if (csrfTokenMeta) {
                csrfToken = csrfTokenMeta.getAttribute('content');
            } else {
                console.error('Lỗi nghiêm trọng: Thẻ meta CSRF token không tìm thấy!');
                // Có thể hiển thị thông báo lỗi cố định trên trang nếu CSRF token bị thiếu.
            }

            // Helper function để hiển thị thông báo (bạn có thể thay thế bằng thư viện Toast sau này)
            function showUIMessage(message, type = 'info') {
                // Ví dụ đơn giản: tìm một div #pageMessages và hiển thị
                // Bạn cần tạo <div id="pageMessages"></div> ở đâu đó trong layout của bạn
                const messageArea = document.getElementById('pageMessages'); // Cần tạo div này trong HTML của bạn
                if (messageArea) {
                    messageArea.innerHTML =
                        `<div class="alert alert-${type === 'success' ? 'success' : 'danger'}" role="alert">${message}</div>`;
                    messageArea.style.display = 'block';
                    setTimeout(() => {
                        messageArea.style.display = 'none';
                        messageArea.innerHTML = '';
                    }, 5000); // Tự ẩn sau 5 giây
                } else {
                    console.log(`Message (${type}): ${message}`); // Fallback to console if no message area
                }
            }


            // =================================================================================
            // KHỐI 1: XỬ LÝ CHO MODAL XÁC THỰC PIN (verifyPinModal)
            // =================================================================================
            const verifyPinModalElement = document.getElementById('verifyPinModal');
            if (verifyPinModalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const verifyPinModal = new bootstrap.Modal(verifyPinModalElement);
                const globalPinInput = document.getElementById('globalPinInput');
                const pinErrorMessage = document.getElementById('pinErrorMessage');
                const submitPinButton = document.getElementById('submitPinButton');

                if (globalPinInput && pinErrorMessage && submitPinButton) {
                    let currentTargetSpanForPin = null;
                    let currentTargetButtonForPin = null;
                    let currentActualPasswordForPin = '';

                    document.querySelectorAll('.btn-request-pin').forEach(button => {
                        button.addEventListener('click', function() {
                            const spanId = this.dataset.spanId;
                            currentTargetSpanForPin = document.getElementById(spanId);
                            currentTargetButtonForPin = this; // 'this' là nút con mắt được click

                            if (currentTargetSpanForPin && typeof currentTargetSpanForPin.dataset
                                .actualPassword !== 'undefined') {
                                currentActualPasswordForPin = currentTargetSpanForPin.dataset
                                    .actualPassword;
                            } else {
                                console.error(
                                    'PIN Modal: Span mật khẩu mục tiêu không tìm thấy hoặc thiếu data-actual-password. Span ID mong đợi:',
                                    spanId);
                                return;
                            }
                            globalPinInput.value = '';
                            pinErrorMessage.textContent = '';
                            pinErrorMessage.style.display = 'none';
                            globalPinInput.classList.remove('is-invalid');
                            submitPinButton.disabled = false;
                            submitPinButton.innerHTML = 'Xác nhận';
                            // Modal được mở bằng data-bs-toggle="modal" data-bs-target="#verifyPinModal"
                        });
                    });

                    submitPinButton.addEventListener('click', function() {
                        const pin = globalPinInput.value.trim();
                        pinErrorMessage.style.display = 'none';
                        globalPinInput.classList.remove('is-invalid');

                        if (pin.length !== 8 || !/^\d+$/.test(pin)) {
                            pinErrorMessage.textContent = 'Mã PIN phải là 8 chữ số.';
                            pinErrorMessage.style.display = 'block';
                            globalPinInput.classList.add('is-invalid');
                            return;
                        }

                        this.disabled = true;
                        this.innerHTML =
                            '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';
                        if (!csrfToken) {
                            console.error('CSRF Token missing!');
                            this.disabled = false;
                            this.innerHTML = 'Xác nhận';
                            return;
                        }

                        fetch('{{ route('accounts.globalpin.verify') }}', {
                                /* ... (như code trước) ... */
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    global_pin: pin
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(errData => Promise.reject(errData || {
                                        message: `Lỗi HTTP ${response.status}`
                                    }));
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    verifyPinModal.hide();
                                    if (currentTargetSpanForPin && currentTargetButtonForPin &&
                                        typeof currentActualPasswordForPin !== 'undefined') {
                                        const icon = currentTargetButtonForPin.querySelector('i.fas');
                                        currentTargetSpanForPin.textContent =
                                            currentActualPasswordForPin;
                                        if (icon) {
                                            icon.classList.remove('fa-eye');
                                            icon.classList.add('fa-eye-slash');
                                        }
                                        currentTargetButtonForPin.setAttribute('title', 'Ẩn mật khẩu');
                                    }
                                } else {
                                    pinErrorMessage.textContent = data.message || 'Mã PIN không đúng.';
                                    pinErrorMessage.style.display = 'block';
                                    globalPinInput.classList.add('is-invalid');
                                }
                            })
                            .catch(error => {
                                console.error('Lỗi PIN Verify:', error);
                                pinErrorMessage.textContent = error.message || 'Lỗi kết nối.';
                                pinErrorMessage.style.display = 'block';
                                globalPinInput.classList.add('is-invalid');
                            })
                            .finally(() => {
                                if (submitPinButton) {
                                    submitPinButton.disabled = false;
                                    submitPinButton.innerHTML = 'Xác nhận';
                                }
                            });
                    });
                    verifyPinModalElement.addEventListener('hidden.bs.modal', function() {
                        /* ... Reset modal PIN ... */
                        if (globalPinInput) globalPinInput.value = '';
                        if (pinErrorMessage) {
                            pinErrorMessage.textContent = '';
                            pinErrorMessage.style.display = 'none';
                        }
                        if (globalPinInput) globalPinInput.classList.remove('is-invalid');
                    });
                } else {
                    console.error('JS Error: Các phần tử của modal PIN không đủ.');
                }
            } else {
                console.warn('JS Warning: Modal #verifyPinModal hoặc Bootstrap JS chưa sẵn sàng.');
            }


            // =================================================================================
            // KHỐI XỬ LÝ CHO MODAL SỬA TÀI KHOẢN (editAccountModal)
            // =================================================================================
            const editAccountModalElement = document.getElementById('editAccountModal');
            if (editAccountModalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const editAccountModal = new bootstrap.Modal(editAccountModalElement);
                const editAccountForm = document.getElementById('editAccountForm');
                const editAccountIdField = document.getElementById('edit_account_id');
                const editPlatformOriginalIdField = document.getElementById('edit_platform_original_id');
                const editFamilyMemberIdForTab3Field = document.getElementById('edit_family_member_id_for_tab3');

                // Divs hiển thị lỗi cho từng tab
                const errorDivs = {
                    tab1: document.getElementById('editAccountFormErrorsTab1'),
                    tab2: document.getElementById('editPlatformFormErrorsTab2'),
                    tab3: document.getElementById('editFamilyMemberFormErrorsTab3')
                };

                // Nút hiện/ẩn mật khẩu trong modal SỬA (Tab 1)
                const toggleEditPasswordButton = document.getElementById('toggleEditPasswordVisibility');
                const editPasswordField = document.getElementById('edit_password');
                if (toggleEditPasswordButton && editPasswordField) {
                    toggleEditPasswordButton.addEventListener('click', function() {
                        const type = editPasswordField.getAttribute('type') === 'password' ? 'text' :
                            'password';
                        editPasswordField.setAttribute('type', type);
                        const icon = this.querySelector('i.fas');
                        if (icon) {
                            icon.classList.toggle('fa-eye');
                            icon.classList.toggle('fa-eye-slash');
                        }
                    });
                }

                // Các phần tử cho logic hiển thị động của modal EDIT (Lấy an toàn)
                const editPlatformSelect = document.getElementById('edit_platform_id');
                const editSocialNetworkDetailsFields = document.getElementById('editSocialNetworkDetailsFields');
                const editTiktokSpecificFields = document.getElementById('editTiktokSpecificFields');
                const editVneidSpecificFields = document.getElementById('editVneidSpecificFields');
                const editPlatformDetailsTitle = document.getElementById('editPlatformDetailsTitle');
                const editAccountStatusField = document.getElementById('edit_account_status');
                const editEncryptedPassword2Field = document.getElementById(
                    'edit_encrypted_password_2'); // ID đã được đổi
                const editLastLoginIpField = document.getElementById('edit_last_login_ip');

                // Nút hiện/ẩn mật khẩu cấp 2 trong modal SỬA (cho VNeID) - Đảm bảo ID khớp với HTML
                const toggleEditVneidPassword2Button = document.getElementById(
                    'toggleEditVneidPassword2'); // ID đã được đổi
                if (toggleEditVneidPassword2Button && editEncryptedPassword2Field) {
                    toggleEditVneidPassword2Button.addEventListener('click', function() {
                        const type = editEncryptedPassword2Field.getAttribute('type') === 'password' ?
                            'text' : 'password';
                        editEncryptedPassword2Field.setAttribute('type', type);
                        const icon = this.querySelector('i.fas');
                        if (icon) {
                            icon.classList.toggle('fa-eye');
                            icon.classList.toggle('fa-eye-slash');
                        }
                    });
                }

                // Hàm để bật/tắt hiển thị các trường chi tiết cụ thể cho từng nền tảng trong modal SỬA
                function togglePlatformSpecificFieldsForEditModal() {
                    // Kiểm tra sự tồn tại của editPlatformSelect trước khi truy cập .value
                    const selectedPlatformId = editPlatformSelect ? editPlatformSelect.value : null;

                    // Ẩn tất cả các khối chi tiết và tiêu đề trước (kiểm tra sự tồn tại trước khi thao tác)
                    if (editSocialNetworkDetailsFields) editSocialNetworkDetailsFields.style.display = 'none';
                    if (editTiktokSpecificFields) editTiktokSpecificFields.style.display = 'none';
                    if (editVneidSpecificFields) editVneidSpecificFields.style.display = 'none';

                    // Reset các giá trị của các trường cụ thể khi chúng bị ẩn (kiểm tra null trước)
                    const editMailAccountIdInput = document.getElementById('edit_mail_account_id');
                    const editTiktokUserIdInput = document.getElementById('edit_tiktok_user_id');
                    const editFollowerCountInput = document.getElementById('edit_follower_count');

                    if (editMailAccountIdInput) editMailAccountIdInput.value = '';
                    if (editTiktokUserIdInput) editTiktokUserIdInput.value = '';
                    if (editFollowerCountInput) editFollowerCountInput.value = '0';
                    if (editEncryptedPassword2Field) editEncryptedPassword2Field.value =
                        ''; // Reset encrypted_password_2
                    if (editLastLoginIpField) editLastLoginIpField.value = ''; // Reset last_login_ip
                    if (editAccountStatusField) editAccountStatusField.value = 'active'; // Reset status

                    // Hiển thị các khối chi tiết và trường cụ thể dựa trên nền tảng đã chọn
                    if (selectedPlatformId === '6') { // TikTok
                        if (editSocialNetworkDetailsFields) editSocialNetworkDetailsFields.style.display = 'block';
                        if (editPlatformDetailsTitle) editPlatformDetailsTitle.textContent =
                            'Thông tin chi tiết TikTok';
                        if (editTiktokSpecificFields) editTiktokSpecificFields.style.display = 'block';
                    } else if (selectedPlatformId === '3') { // VNeID
                        if (editSocialNetworkDetailsFields) editSocialNetworkDetailsFields.style.display = 'block';
                        if (editPlatformDetailsTitle) editPlatformDetailsTitle.textContent =
                            'Thông tin chi tiết VNeID';
                        if (editVneidSpecificFields) editVneidSpecificFields.style.display = 'block';
                    }
                }

                // Lắng nghe sự kiện click trên các nút "Sửa" trong dropdown
                document.querySelectorAll('.btn-edit-account').forEach(editButton => {
                    editButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        const accountId = this.dataset.accountId;
                        if (!accountId) {
                            console.error('Không tìm thấy account ID cho nút chỉnh sửa.');
                            return;
                        }

                        // Reset các thông báo lỗi
                        for (const tabKey in errorDivs) {
                            if (errorDivs[tabKey]) {
                                errorDivs[tabKey].innerHTML = '';
                                errorDivs[tabKey].style.display = 'none';
                            }
                        }
                        if (editAccountForm) editAccountForm.reset();

                        fetch(`/accounts/${accountId}/edit`)
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => Promise.reject(err || {
                                        message: 'Lỗi tải dữ liệu.'
                                    }));
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success && data.account && data.platform && data
                                    .all_platforms && data.family_member) {
                                    // TAB 1: Account Info
                                    const platformSelect = document.getElementById(
                                        'edit_platform_id');
                                    platformSelect.innerHTML =
                                        '<option value="">-- Chọn nền tảng --</option>';
                                    data.all_platforms.forEach(p => platformSelect.options.add(
                                        new Option(p.name, p.id)));
                                    platformSelect.value = data.account.platform_id;
                                    document.getElementById('edit_username').value = data
                                        .account.username;
                                    document.getElementById('edit_password').value =
                                        ''; // Giữ rỗng vì là "mật khẩu mới"
                                    document.getElementById('edit_password_confirmation')
                                        .value = '';
                                    document.getElementById('edit_note').value = data.account
                                        .note || '';

                                    // TAB 2: Platform Info
                                    if (editPlatformOriginalIdField) editPlatformOriginalIdField
                                        .value = data.platform.id;
                                    document.getElementById('edit_platform_name').value = data
                                        .platform.name;
                                    document.getElementById('edit_platform_description').value =
                                        data.platform.description || '';
                                    document.getElementById('edit_platform_logo_path').value =
                                        data.platform.logo_path || '';

                                    // TAB 3: Family Member Info
                                    if (editFamilyMemberIdForTab3Field)
                                        editFamilyMemberIdForTab3Field.value = data
                                        .family_member.id;
                                    document.getElementById('edit_fm_name').value = data
                                        .family_member.name;
                                    document.getElementById('edit_fm_email').value = data
                                        .family_member.email || '';
                                    document.getElementById('edit_fm_master_password').value =
                                        ''; // Giữ rỗng
                                    document.getElementById(
                                        'edit_fm_master_password_confirmation').value = '';

                                    // Gọi hàm để hiển thị/ẩn các trường chi tiết sau khi dữ liệu được tải
                                    togglePlatformSpecificFieldsForEditModal
                                        (); // Gọi hàm này để ẩn tất cả và reset

                                    // Sau khi hàm toggle đã ẩn và reset, hãy đặt giá trị từ dữ liệu đã fetch
                                    if (data.socialnetwork_detail) {
                                        if (editAccountStatusField) editAccountStatusField
                                            .value = data.socialnetwork_detail.status ||
                                            'active';

                                        if (data.platform.id === 6) { // TikTok
                                            document.getElementById('edit_mail_account_id')
                                                .value = data.socialnetwork_detail
                                                .mail_account_id || '';
                                            document.getElementById('edit_tiktok_user_id')
                                                .value = data.socialnetwork_detail
                                                .tiktok_user_id || '';
                                            document.getElementById('edit_follower_count')
                                                .value = data.socialnetwork_detail
                                                .follower_count || '0';
                                        }
                                    }

                                    // Gán giá trị encrypted_password_2 nếu là VNeID (không phụ thuộc socialnetworkDetail)
                                    if (data.platform.id === 3) {
                                        if (editEncryptedPassword2Field)
                                            editEncryptedPassword2Field.value = data.account
                                            .encrypted_password_2 || '';
                                    }


                                    // Event listener for platform change in edit modal (chỉ gắn một lần)
                                    // Đảm bảo không gắn nhiều listener
                                    editPlatformSelect.removeEventListener('change',
                                        togglePlatformSpecificFieldsForEditModal);
                                    editPlatformSelect.addEventListener('change',
                                        togglePlatformSpecificFieldsForEditModal);

                                    if (editAccountForm) editAccountForm.action =
                                        `/accounts/${data.account.id}`;
                                    if (editAccountIdField) editAccountIdField.value = data
                                        .account.id;
                                    editAccountModal.show();
                                } else {
                                    showUIMessage('Không thể tải đầy đủ dữ liệu.', 'danger');
                                }
                            })
                            .catch(error => {
                                console.error('Lỗi Fetch Edit Data:', error);
                                showUIMessage('Lỗi tải dữ liệu: ' + error.message, 'danger');
                            });
                    });
                });

                // Xử lý submit form chính (editAccountForm)
                if (editAccountForm) {
                    editAccountForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const saveButton = document.getElementById('saveEditAccountButton');
                        saveButton.disabled = true;
                        saveButton.innerHTML =
                            '<span class="spinner-border spinner-border-sm"></span> Đang lưu...';
                        for (const tabKey in errorDivs) {
                            if (errorDivs[tabKey]) {
                                errorDivs[tabKey].innerHTML = '';
                                errorDivs[tabKey].style.display = 'none';
                            }
                        }

                        const formData = new FormData(this);
                        formData.append('_method', 'PUT');
                        if (!csrfToken) {
                            saveButton.disabled = false;
                            saveButton.innerHTML = 'Lưu Tất Cả Thay Đổi';
                            return;
                        }

                        fetch(this.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(response => {
                                if (response.status === 422) {
                                    return response.json().then(err => Promise.reject({
                                        validationErrors: err.errors
                                    }));
                                }
                                if (!response.ok) {
                                    return response.text().then(text => Promise.reject({
                                        message: text || `Lỗi server: ${response.status}`
                                    }));
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    editAccountModal.hide();
                                    showUIMessage(data.message || 'Cập nhật thành công!', 'success');
                                    setTimeout(() => window.location.reload(), 1500);
                                } else {
                                    if (errorDivs.tab1) {
                                        errorDivs.tab1.textContent = data.message || 'Có lỗi.';
                                        errorDivs.tab1.style.display = 'block';
                                    } else {
                                        showUIMessage(data.message || 'Có lỗi.', 'danger');
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Lỗi Submit Form Sửa Chính:', error);
                                let primaryErrorDisplay = errorDivs.tab1 || null;

                                if (error.validationErrors) {
                                    let errorHtml = '<strong>Vui lòng sửa các lỗi sau:</strong><ul>';
                                    for (const field in error.validationErrors) {
                                        if (field.startsWith('family_member_')) primaryErrorDisplay =
                                            errorDivs.tab3 || errorDivs.tab1;
                                        else if (field.startsWith('platform_name') || field.startsWith(
                                                'platform_description') || field.startsWith(
                                                'platform_logo_path')) primaryErrorDisplay = errorDivs
                                            .tab2 || errorDivs.tab1;
                                        else if (field.startsWith('mail_account_id') || field
                                            .startsWith('tiktok_user_id') ||
                                            field.startsWith('follower_count') || field.startsWith(
                                                'encrypted_password_2') ||
                                            field.startsWith('last_login_ip') || field.startsWith(
                                                'status')) {
                                            primaryErrorDisplay = errorDivs.tab1 || null;
                                        } else primaryErrorDisplay = errorDivs.tab1 || null;

                                        error.validationErrors[field].forEach(message => errorHtml +=
                                            `<li>${message}</li>`);
                                    }
                                    errorHtml += '</ul>';
                                    if (primaryErrorDisplay) {
                                        primaryErrorDisplay.innerHTML = errorHtml;
                                        primaryErrorDisplay.style.display = 'block';
                                    } else {
                                        showUIMessage(errorHtml, 'danger', true);
                                    }
                                } else {
                                    const message = error.message || 'Không thể cập nhật.';
                                    if (primaryErrorDisplay && (primaryErrorDisplay.style.display ===
                                            'none' || !primaryErrorDisplay.innerHTML)) {
                                        showUIMessage(message, 'danger');
                                    } else if (primaryErrorDisplay) {
                                        primaryErrorDisplay.textContent = message;
                                        primaryErrorDisplay.style.display = 'block';
                                    } else {
                                        showUIMessage(message, 'danger');
                                    }
                                }
                            })
                            .finally(() => {
                                if (saveButton) {
                                    saveButton.disabled = false;
                                    saveButton.innerHTML = 'Lưu Tất Cả Thay Đổi';
                                }
                            });
                    });
                }
                // Reset modal edit khi đóng
                editAccountModalElement.addEventListener('hidden.bs.modal', function() {
                    if (editAccountForm) editAccountForm.reset();
                    for (const tabKey in errorDivs) {
                        if (errorDivs[tabKey]) {
                            errorDivs[tabKey].innerHTML = '';
                            errorDivs[tabKey].style.display = 'none';
                        }
                    }
                    const platformSelect = document.getElementById('edit_platform_id');
                    if (platformSelect) platformSelect.innerHTML =
                        '<option value="">-- Chọn nền tảng --</option>';
                    // Reset/hide specific fields in edit modal
                    togglePlatformSpecificFieldsForEditModal(); // Đảm bảo mọi thứ được reset đúng cách
                    // Reset các trường cụ thể một lần nữa để chắc chắn
                    document.getElementById('edit_encrypted_password_2').value = '';
                    document.getElementById('edit_last_login_ip').value = '';
                    document.getElementById('edit_mail_account_id').value = '';
                    document.getElementById('edit_tiktok_user_id').value = '';
                    document.getElementById('edit_follower_count').value = '0';
                    document.getElementById('edit_account_status').value = 'active';
                });
            } else {
                console.warn('JS Warning: Modal #editAccountModal hoặc Bootstrap JS chưa sẵn sàng.');
            }


            // =================================================================================
            // KHỐI XỬ LÝ CHO MODAL THÊM MỚI TÀI KHOẢN (addAccountModal) - HỢP NHẤT VÀ SỬA LỖI TRÙNG LẶP
            // =================================================================================
            const addAccountModalElement = document.getElementById('addAccountModal'); // Lấy biến modal element

            // CHỈ KHAI BÁO CÁC BIẾN NÀY MỘT LẦN TRONG KHỐI NÀY
            const addPlatformSelect = document.getElementById('add_platform_id');
            const platformDetailsFields = document.getElementById('platform_details_fields');
            const platformDetailsTitle = document.getElementById('platform_details_title');
            const tiktokSpecificFields = document.getElementById('tiktok_specific_fields');
            const vneidSpecificFields = document.getElementById('vneid_specific_fields');
            const addStatusField = document.getElementById(
                'add_status'); // Trường status chung (đã có trong tiktok_specific_fields)

            // Nút hiện/ẩn pass trong modal THÊM (mật khẩu chính)
            const toggleAddPasswordButton = document.getElementById('toggleAddPassword');
            const addPasswordField = document.getElementById('add_password');
            if (toggleAddPasswordButton && addPasswordField) {
                toggleAddPasswordButton.addEventListener('click', function() {
                    const type = addPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    addPasswordField.setAttribute('type', type);
                    const icon = this.querySelector('i.fas');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            }

            // Nút hiện/ẩn mật khẩu cấp 2 trong modal THÊM (cho VNeID)
            const toggleAddPassword2Button = document.getElementById('toggleAddPassword2');
            const addPassword2Field = document.getElementById('encrypted_password_2');
            if (toggleAddPassword2Button && addPassword2Field) {
                toggleAddPassword2Button.addEventListener('click', function() {
                    const type = addPassword2Field.getAttribute('type') === 'password' ? 'text' :
                        'password';
                    addPassword2Field.setAttribute('type', type);
                    const icon = this.querySelector('i.fas');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            }

            // Hàm để bật/tắt hiển thị các trường chi tiết cụ thể cho từng nền tảng (Đã sửa lại)
            function togglePlatformSpecificFieldsForAddModal() {
                const selectedPlatformId = addPlatformSelect.value;

                // Ẩn tất cả các khối chi tiết và tiêu đề trước
                if (platformDetailsFields) platformDetailsFields.style.display = 'none';
                if (tiktokSpecificFields) tiktokSpecificFields.style.display = 'none';
                if (vneidSpecificFields) vneidSpecificFields.style.display = 'none';

                // Reset các giá trị của các trường cụ thể khi chúng bị ẩn
                const mailAccountIdInput = document.getElementById('add_mail_account_id');
                const tiktokUserIdInput = document.getElementById('add_tiktok_user_id');
                const followerCountInput = document.getElementById('add_follower_count');
                const password2Input = document.getElementById('encrypted_password_2');
                const lastLoginIpInput = document.getElementById('add_last_login_ip');
                const statusSelect = document.getElementById('add_status');

                if (mailAccountIdInput) mailAccountIdInput.value = '';
                if (tiktokUserIdInput) tiktokUserIdInput.value = '';
                if (followerCountInput) followerCountInput.value = '0';
                if (password2Input) password2Input.value = '';
                if (lastLoginIpInput) lastLoginIpInput.value = ''; // VNeID không có IP, nhưng vẫn reset nếu có
                if (statusSelect) statusSelect.value = 'active'; // Reset status to default

                // Hiển thị các khối chi tiết và trường cụ thể dựa trên nền tảng đã chọn
                if (selectedPlatformId === '6') { // TikTok
                    if (platformDetailsFields) platformDetailsFields.style.display = 'block';
                    if (platformDetailsTitle) platformDetailsTitle.textContent = 'Thông tin chi tiết TikTok';
                    if (tiktokSpecificFields) tiktokSpecificFields.style.display = 'block';
                    // Trường status được chứa trong tiktok_specific_fields nên sẽ tự hiển thị
                } else if (selectedPlatformId === '3') { // VNeID
                    if (platformDetailsFields) platformDetailsFields.style.display = 'block';
                    if (platformDetailsTitle) platformDetailsTitle.textContent = 'Thông tin chi tiết VNeID';
                    if (vneidSpecificFields) vneidSpecificFields.style.display = 'block';
                    // VNeID chỉ có encrypted_password_2, không có status hay last_login_ip theo yêu cầu mới.
                }
                // Các nền tảng khác không có chi tiết đặc biệt thì platformDetailsFields vẫn ẩn
            }

            // Chỉ gắn listener nếu addPlatformSelect tồn tại
            if (addPlatformSelect) {
                togglePlatformSpecificFieldsForAddModal(); // Kiểm tra ban đầu khi trang tải
                addPlatformSelect.addEventListener('change',
                    togglePlatformSpecificFieldsForAddModal); // Lắng nghe thay đổi
            }

            // Logic cho form thêm Platform mới bên trong addAccountModal (Giữ nguyên từ code bạn cung cấp)
            const btnToggleNewPlatformForm = document.getElementById('btnToggleNewPlatformForm');
            const newPlatformFormContainer = document.getElementById('newPlatformFormContainer');
            const btnCancelNewPlatform = document.getElementById('btnCancelNewPlatform');
            const btnSaveNewPlatformAjax = document.getElementById('btnSaveNewPlatformAjax');

            const platformSelectForAddModal = document.getElementById(
                'add_platform_id'); // Khai báo lại cho rõ ràng trong khối này
            const platformSelectForEditModal = document.getElementById('edit_platform_id');

            const ajaxNewPlatformErrorsDiv = document.getElementById('ajaxNewPlatformErrors');
            const ajaxNewPlatformSuccessDiv = document.getElementById('ajaxNewPlatformSuccess');

            if (btnToggleNewPlatformForm && newPlatformFormContainer) {
                btnToggleNewPlatformForm.addEventListener('click', function() {
                    const isHidden = newPlatformFormContainer.style.display === 'none' ||
                        newPlatformFormContainer.style.display === '';
                    newPlatformFormContainer.style.display = isHidden ? 'block' : 'none';
                    if (ajaxNewPlatformErrorsDiv) {
                        ajaxNewPlatformErrorsDiv.innerHTML = '';
                        ajaxNewPlatformErrorsDiv.style.display = 'none';
                    }
                    if (ajaxNewPlatformSuccessDiv) {
                        ajaxNewPlatformSuccessDiv.innerHTML = '';
                        ajaxNewPlatformSuccessDiv.style.display = 'none';
                    }
                });
            }

            if (btnCancelNewPlatform && newPlatformFormContainer) {
                btnCancelNewPlatform.addEventListener('click', function() {
                    newPlatformFormContainer.style.display = 'none';
                    if (ajaxNewPlatformErrorsDiv) {
                        ajaxNewPlatformErrorsDiv.innerHTML = '';
                        ajaxNewPlatformErrorsDiv.style.display = 'none';
                    }
                    if (ajaxNewPlatformSuccessDiv) {
                        ajaxNewPlatformSuccessDiv.innerHTML = '';
                        ajaxNewPlatformSuccessDiv.style.display = 'none';
                    }
                    document.getElementById('ajax_new_platform_name').value = '';
                    document.getElementById('ajax_new_platform_description').value = '';
                    document.getElementById('ajax_new_platform_logo_path').value = '';
                });
            }

            if (btnSaveNewPlatformAjax) {
                btnSaveNewPlatformAjax.addEventListener('click', function() {
                    const name = document.getElementById('ajax_new_platform_name').value.trim();
                    const description = document.getElementById('ajax_new_platform_description').value
                        .trim();
                    const logo_path = document.getElementById('ajax_new_platform_logo_path').value
                        .trim();

                    if (!name) {
                        if (ajaxNewPlatformErrorsDiv) {
                            ajaxNewPlatformErrorsDiv.textContent =
                                'Tên nền tảng mới không được để trống.';
                            ajaxNewPlatformErrorsDiv.style.display = 'block';
                        }
                        return;
                    }
                    if (ajaxNewPlatformErrorsDiv) ajaxNewPlatformErrorsDiv.style.display = 'none';
                    if (ajaxNewPlatformSuccessDiv) ajaxNewPlatformSuccessDiv.style.display = 'none';

                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Lưu...';
                    if (!csrfToken) {
                        console.error('CSRF Token missing!');
                        this.disabled = false;
                        this.innerHTML = 'Lưu Nền tảng';
                        return;
                    }

                    fetch('{{ route('accounts.platforms.storeAjax') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                name,
                                description,
                                logo_path
                            })
                        })
                        .then(response => {
                            if (response.status === 422) {
                                return response.json().then(err => Promise.reject({
                                    validationErrors: err.errors
                                }));
                            }
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err || {
                                    message: 'Lỗi server.'
                                }));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.platform) {
                                if (ajaxNewPlatformSuccessDiv) {
                                    ajaxNewPlatformSuccessDiv.textContent =
                                        `Đã thêm: ${data.platform.name}`;
                                    ajaxNewPlatformSuccessDiv.style.display = 'block';
                                }

                                const newOption = new Option(data.platform.name, data.platform.id);
                                if (platformSelectForAddModal) {
                                    platformSelectForAddModal.add(newOption.cloneNode(true));
                                    platformSelectForAddModal.value = data.platform
                                        .id; // Tự động chọn platform vừa thêm
                                }
                                if (platformSelectForEditModal) { // Cập nhật cả dropdown trong modal sửa nếu nó tồn tại
                                    platformSelectForEditModal.add(newOption.cloneNode(true));
                                }

                                // Reset form nhỏ và ẩn đi sau 2 giây
                                setTimeout(() => {
                                    if (newPlatformFormContainer) newPlatformFormContainer
                                        .style.display = 'none';
                                    document.getElementById('ajax_new_platform_name')
                                        .value = '';
                                    document.getElementById('ajax_new_platform_description')
                                        .value = '';
                                    document.getElementById('ajax_new_platform_logo_path')
                                        .value = '';
                                    if (ajaxNewPlatformSuccessDiv) ajaxNewPlatformSuccessDiv
                                        .style.display = 'none';
                                }, 2000);

                            } else {
                                let errorMsg = data.message || 'Không thể thêm platform.';
                                if (data.errors) { // Nếu server trả về lỗi validation cụ thể
                                    errorMsg = '<ul>';
                                    for (const field in data.errors) {
                                        data.errors[field].forEach(message => errorMsg +=
                                            `<li>${message}</li>`);
                                    }
                                    errorMsg += '</ul>';
                                    if (ajaxNewPlatformErrorsDiv) ajaxNewPlatformErrorsDiv
                                        .innerHTML = errorMsg;
                                    else console.error(errorMsg);
                                } else {
                                    if (ajaxNewPlatformErrorsDiv) ajaxNewPlatformErrorsDiv
                                        .textContent = errorMsg;
                                    else console.error(errorMsg);
                                }
                                if (ajaxNewPlatformErrorsDiv) ajaxNewPlatformErrorsDiv.style
                                    .display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Lỗi Tạo Platform Mới:', error);
                            let errorMsgText = 'Lỗi không xác định.';
                            if (error.validationErrors) {
                                errorMsgText = '<ul>';
                                for (const field in error.validationErrors) {
                                    error.validationErrors[field].forEach(message => errorMsgText +=
                                        `<li>${message}</li>`);
                                }
                                errorMsgText += '</ul>';
                                if (ajaxNewPlatformErrorsDiv) ajaxNewPlatformErrorsDiv.innerHTML =
                                    errorMsgText;
                                else console.error(errorMsgText);
                            } else if (error.message) {
                                errorMsgText = error.message;
                                if (ajaxNewPlatformErrorsDiv) ajaxNewPlatformErrorsDiv.textContent =
                                    errorMsgText;
                                else console.error(errorMsgText);
                            }
                            if (ajaxNewPlatformErrorsDiv) ajaxNewPlatformErrorsDiv.style.display =
                                'block';
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.innerHTML = 'Lưu Nền tảng';
                        });
                });
            }

            // Xử lý hiển thị lại modal Thêm Mới nếu có lỗi validation từ submit non-AJAX
            @if (isset($errors) && $errors->storeAccount && $errors->storeAccount->any())
                var addModalInstanceForErrors = new bootstrap.Modal(document.getElementById('addAccountModal'));
                if (addModalInstanceForErrors) addModalInstanceForErrors.show();
            @endif
        });
    </script>
@endsection
