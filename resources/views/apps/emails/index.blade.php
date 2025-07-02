@extends('layouts.app')
@section('title', 'Trang chủ')
@section('content')

    <head>
        {{-- Các thẻ meta, title, link css khác --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Các script hoặc link khác --}}
        {{-- Các script hoặc link khác --}}
        {{-- Cần Bootstrap CSS và JS để modal hoạt động --}}
        {{-- Tùy thuộc vào layout của bạn, chúng có thể đã được load từ layouts.app --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
        {{-- Thêm thư viện WYSIWYG editor nếu muốn soạn thảo email chuyên nghiệp --}}
        <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
        <style>
            /* Custom style for disabled refresh button */
            .btn-refresh-disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
        </style>
    </head>
    <div class="content pt-0">
        <div class="email-container">
            <div class="row gx-lg-6 gx-3 py-4 z-2 position-sticky bg-body email-header">
                <div class="col-auto">
                    {{-- <a class="btn btn-primary email-sidebar-width d-none d-lg-block" href="../../apps/email/compose.html">Compose</a> --}}
                    <button type="button" class="btn btn-primary email-sidebar-width d-none d-lg-block" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        <i class="bi bi-plus"></i>
                        Compose
                    </button>
                    <button class="btn px-3 btn-phoenix-secondary text-body-tertiary d-lg-none"
                        data-phoenix-toggle="offcanvas"data-phoenix-target="#emailSidebarColumn">
                        <span class="fa-solid fa-bars"></span>
                    </button>
                </div>



                <div class="col-auto d-lg-none">
                    <a class="btn btn-primary px-3 px-sm-4" type="button" href="../../apps/email/compose.html"
                        data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        <span class="d-none d-sm-inline-block">Compose</span>
                        <span class="d-sm-none fas fa-plus"></span>
                    </a>
                </div>


                <div class="col-auto flex-1">
                    <div class="search-box w-100">
                        <form class="position-relative"><input class="form-control search-input search" type="search"
                                placeholder="Search ..." aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row g-lg-6 mb-8">
                <div class="col-lg-auto">
                    <div class="email-sidebar email-sidebar-width bg-body phoenix-offcanvas phoenix-offcanvas-fixed"
                        id="emailSidebarColumn" data-breakpoint="lg">
                        <div class="email-content scrollbar-overlay">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-uppercase fs-10 text-body-tertiary text-opacity-85 mb-2 fw-bold">
                                    mailbox</p><button class="btn d-lg-none p-0 mb-2" data-phoenix-dismiss="offcanvas"><span
                                        class="uil uil-times fs-8"></span></button>
                            </div>
                            <ul class="nav flex-column border-top border-translucent fs-9 vertical-nav mb-4">
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none active"
                                        aria-current="page" href="../../apps/email/inbox.html">
                                        <div class="d-flex align-items-center"><span
                                                class="me-2 nav-icons uil uil-inbox"></span><span
                                                class="flex-1">Inbox</span><span class="nav-item-count">5</span>
                                        </div>
                                    </a></li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="me-2 nav-icons uil uil-location-arrow"></span><span
                                                class="flex-1">Sent</span><span class="nav-item-count">23</span>
                                        </div>
                                    </a></li>
                                <li class="nav-item">
                                    {{-- Nút "Draft" sẽ mở popup để sửa thông tin MailAccount đang chọn --}}
                                    <a class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        href="#" data-bs-toggle="modal" data-bs-target="#editMailAccountModal"
                                        id="openEditSelectedMailAccountModal">
                                        <div class="d-flex align-items-center"><span
                                                class="me-2 nav-icons uil uil-pen"></span><span class="flex-1">Draft</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="me-2 nav-icons uil uil-exclamation-circle"></span><span
                                                class="flex-1">Spam</span></div>
                                    </a></li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="me-2 nav-icons uil uil-trash"></span><span
                                                class="flex-1">Trash</span></div>
                                    </a></li>
                            </ul>
                            <div class="d-flex justify-content-between">
                                <p class="text-uppercase fs-10 text-body-tertiary text-opacity-85 mb-2 fw-bold">
                                    Filtered</p><a class="fs-10 fw-bold" href="#!"><span
                                        class="fa-solid fa-plus me-2"></span>Add Folder</a>
                            </div>
                            <ul class="nav flex-column border-top border-translucent fs-9 vertical-nav mb-4">
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucenttext-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="me-2 nav-icons uil uil-star"></span><span
                                                class="flex-1">Starred</span></div>
                                    </a></li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucenttext-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="me-2 nav-icons uil uil-archive"></span><span
                                                class="flex-1">Archive</span></div>
                                    </a></li>
                            </ul>
                            <div class="d-flex justify-content-between">
                                <p class="text-uppercase fs-10 text-body-tertiary text-opacity-85 mb-2 fw-bold">
                                    Labels</p><a class="fs-10 fw-bold" href="#!"><span
                                        class="fa-solid fa-plus me-2"></span>Add Label</a>
                            </div>
                            <ul class="nav flex-column border-top border-translucent fs-9 vertical-nav">
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="ms-n1 me-1 fa-solid fa-circle text-primary"
                                                data-fa-transform="shrink-10"></span><span class="flex-1">Personal</span>
                                        </div>
                                    </a></li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="ms-n1 me-1 fa-solid fa-circle text-primary-dark"
                                                data-fa-transform="shrink-10"></span><span class="flex-1">Work</span>
                                        </div>
                                    </a></li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="ms-n1 me-1 fa-solid fa-circle text-success"
                                                data-fa-transform="shrink-10"></span><span class="flex-1">Payments</span>
                                        </div>
                                    </a></li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="ms-n1 me-1 fa-solid fa-circle text-warning"
                                                data-fa-transform="shrink-10"></span><span class="flex-1">Invoices</span>
                                        </div>
                                    </a></li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="ms-n1 me-1 fa-solid fa-circle text-danger"
                                                data-fa-transform="shrink-10"></span><span class="flex-1">Accounts</span>
                                        </div>
                                    </a></li>
                                <li class="nav-item"><a
                                        class="nav-link py-2 ps-0 pe-3 border-end border-bottom border-translucent text-start outline-none"
                                        aria-current="page" href="#!">
                                        <div class="d-flex align-items-center"><span
                                                class="ms-n1 me-1 fa-solid fa-circle text-info"
                                                data-fa-transform="shrink-10"></span><span class="flex-1">Forums</span>
                                        </div>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="phoenix-offcanvas-backdrop d-lg-none top-0" data-phoenix-backdrop="data-phoenix-backdrop">
                    </div>
                </div>
                <div class="col-lg">
                    <div class="px-lg-1">
                        {{-- Account selection --}}
                        <form method="GET" action="{{ route('emails.index') }}" class="mb-3" id="accountSelectForm">
                            <div class="d-flex align-items-center gap-2">
                                <label for="account_id">Tài khoản:</label>
                                <select name="account_id" id="account_id" class="form-select w-auto"
                                    onchange="this.form.submit()">
                                    @if ($accounts->isEmpty())
                                        <option value="">-- Chưa có tài khoản --</option>
                                    @else
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}"
                                                {{ (string) $selectedAccountId == (string) $acc->id ? 'selected' : '' }}>
                                                {{ $acc->email }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </form>
                        {{-- Actions --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                {{-- <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#addAccountModal">
                                    <i class="bi bi-plus"></i>
                                    Thêm tài khoản
                                </button> --}}
                                <button type="button" class="btn btn-primary" id="refreshEmailsButton">
                                    🔄 Làm mới Hộp thư
                                </button>
                            </div>
                            {{-- <div>
                                <button type="button" class="btn btn-secondary"
                                    onclick="window.location.href='{{ route('home') }}'">
                                    <i class="bi bi-house"></i>
                                    ️🏠 Trang chủ
                                </button>
                            </div> --}}
                        </div>
                        {{-- Popup Form --}}
                        <div class="modal fade" id="addAccountModal" tabindex="-1"
                            aria-labelledby="addAccountModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- modal-lg cho modal rộng hơn một chút --}}
                                <div class="modal-content">
                                    <form id="addAccountForm" action="{{ route('emails.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addAccountModalLabel">Thêm Tài Khoản Email Mới
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="modal_email_address" class="form-label">Địa chỉ Email <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="modal_email_address"
                                                    name="email" required value="{{ old('email') }}">
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="modal_app_password" class="form-label">Mật khẩu ứng dụng <span
                                                        class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="modal_app_password"
                                                    name="app_password" required>
                                                @error('app_password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8 mb-3">
                                                    <label for="modal_imap_host" class="form-label">Máy chủ IMAP <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="modal_imap_host"
                                                        name="imap_host" required
                                                        value="{{ old('imap_host', 'imap.gmail.com') }}">
                                                    @error('imap_host')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="modal_imap_port" class="form-label">Cổng IMAP <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="modal_imap_port"
                                                        name="imap_port" required value="{{ old('imap_port', 993) }}">
                                                    @error('imap_port')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="modal_imap_encryption" class="form-label">Mã hóa IMAP
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="modal_imap_encryption"
                                                    name="imap_encryption" required>
                                                    <option value="ssl"
                                                        {{ old('imap_encryption', 'ssl') == 'ssl' ? 'selected' : '' }}>
                                                        SSL</option>
                                                    <option value="tls"
                                                        {{ old('imap_encryption') == 'tls' ? 'selected' : '' }}>TLS
                                                    </option>
                                                    <option value="none"
                                                        {{ old('imap_encryption') == 'none' ? 'selected' : '' }}>Không có
                                                    </option>
                                                </select>
                                                @error('imap_encryption')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-primary">Lưu Tài Khoản</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- Alert Messages --}}
                        <div id="alertMessages">
                            @if (session('status'))
                                <div class="alert alert-success mt-3">{{ session('status') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                            @endif
                            @if ($error && !session('error') && !session('status'))
                                {{-- Chỉ hiển thị lỗi từ controller nếu không có lỗi session --}}
                                <div class="alert alert-danger mt-3">{{ $error }}</div>
                            @endif
                        </div>
                        <div
                            class="d-flex align-items-center flex-wrap position-sticky pb-2 bg-body z-2 email-toolbar inbox-toolbar">
                            <div class="d-flex align-items-center flex-1 me-2">
                                <button class="btn btn-sm p-0 me-2" type="button" onclick="location.reload()">
                                    <span class="text-primary fas fa-redo fs-10"></span>
                                </button>
                                <p class="fw-semibold fs-10 text-body-tertiary text-opacity-85 mb-0 lh-sm text-nowrap">
                                    Last refreshed 1m ago</p>
                            </div>
                            <div class="d-flex">
                                <p class="text-body-tertiary text-opacity-85 fs-9 fw-semibold mb-0 me-3">Showing :
                                    <span class="text-body">1-7 </span>of <span class="text-body">205</span>
                                </p>
                                <button class="btn p-0 me-3" type="button"><span
                                        class="text-body-quaternary fa-solid fa-angle-left fs-10"></span></button><button
                                    class="btn p-0" type="button"><span
                                        class="text-primary fa-solid fa-angle-right fs-10"></span></button>
                            </div>
                        </div>
                        <div class="border-top border-translucent py-2 d-flex justify-content-between">
                            <div class="form-check mb-0 fs-8"><input class="form-check-input" type="checkbox"
                                    data-bulk-select-row="data-bulk-select-row" /></div>
                            <div><button class="btn p-0 me-2 text-body-quaternary hover text-body-tertiary text-opacity-85"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Archive"><span
                                        class="fas fa-archive fs-10"></span></button><button
                                    class="btn p-0 me-2 text-body-quaternary hover text-body-tertiary text-opacity-85"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete"><span
                                        class="fas fa-trash fs-10"></span></button><button
                                    class="btn p-0 me-2 text-body-quaternary hover text-body-tertiary text-opacity-85"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Star"><span
                                        class="fas fa-star fs-10"></span></button><button
                                    class="btn p-0 text-body-quaternary hover text-body-tertiary text-opacity-85"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tags"><span
                                        class="fas fa-tag fs-10"></span></button></div>
                        </div>
                        {{-- Nội dung --}}
                        @if ($selectedAccount && $emails->isNotEmpty())
                            @foreach ($emails as $email)
                                <div class="border-top border-translucent hover-actions-trigger py-3">
                                    <div class="row align-items-sm-center gx-2">
                                        <div class="col-auto">
                                            <div class="d-flex flex-column flex-sm-row"><input
                                                    class="form-check-input mb-2 m-sm-0 me-sm-2" type="checkbox"
                                                    id="checkbox-1" data-bulk-select-row="data-bulk-select-row" /><button
                                                    class="btn p-0"><span
                                                        class="fas text-warning fa-star"></span></button>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="avatar avatar-s  rounded-circle">
                                                <img class="rounded-circle "
                                                    src="https://lh3.googleusercontent.com/a/default-user=s40-p"
                                                    alt="" />
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a class="text-body-emphasis fw-bold inbox-link fs-9"
                                                href="#">{{ $email->from_name }}</a>
                                        </div>
                                        <div class="col-auto ms-auto">
                                            <div class="hover-actions end-0">
                                                <button
                                                    class="btn btn-phoenix-secondary btn-icon dropdown-toggle dropdown-caret-none"
                                                    type="button" data-bs-toggle="dropdown" data-boundary="window"
                                                    aria-haspopup="true" aria-expanded="false"
                                                    data-bs-reference="parent">
                                                    <span class="fa-solid fa-ellipsis"></span>
                                                </button>
                                                {{-- Actions  --}}
                                                <div class="dropdown-menu dropdown-menu-end py-2">
                                                    <a class="dropdown-item" href="#!">Mark Unread</a>
                                                    <a class="dropdown-item" href="#!">Mark
                                                        Important</a>
                                                    <a class="dropdown-item" href="#!">Archive</a>
                                                    <a class="dropdown-item" href="#!">Download</a>
                                                    <a class="dropdown-item" href="#!">Print</a>
                                                    <a class="dropdown-item" href="#!">Report
                                                        Spam</a>
                                                    <a class="dropdown-item" href="#!">Report
                                                        Phishing</a>
                                                    <a class="dropdown-item" href="#!">Mute Jessica
                                                        Ball</a>
                                                    <a class="dropdown-item" href="#!">Block Jessica
                                                        Ball</a>
                                                    <a class="dropdown-item text-danger" href="#!">Delete</a>
                                                </div>
                                            </div>
                                            <span
                                                class="fs-10 fw-bold">{{ $email->date ? $email->date->format('d/m/Y H:i:s') : 'Không rõ' }}</span>
                                        </div>
                                    </div>
                                    <div class="ms-4 mt-n3 mt-sm-0 ms-sm-11">
                                        <a class="d-block inbox-link" href="#">
                                            <span
                                                class="fs-9 line-clamp-1 text-body-emphasis">{{ $email->subject }}</span>

                                            <p class="fs-9 ps-0 text-body-tertiary mb-0 line-clamp-2">
                                                {{ !empty($email->body_text) ? Str::limit(strip_tags($email->body_text), 50, '...') : '(Không có nội dung)' }}
                                            </p>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            {{-- {{ $emails->links() }} --}}
                        @elseif($selectedAccount && !$error)
                            <div class="alert alert-info mt-3">Không có email nào trong hộp thư của tài khoản
                                {{ $selectedAccount->email }}.</div>
                        @elseif(!$selectedAccount && $accounts->isNotEmpty() && !$error)
                            <div class="alert alert-info mt-3">Vui lòng chọn một tài khoản để xem email.</div>
                        @endif
                        {{-- Nội dung --}}
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const refreshButton = document.getElementById('refreshEmailsButton');
                            const alertMessagesContainer = document.getElementById('alertMessages');
                            const accountSelectElement = document.getElementById('account_id');
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'); // Lấy CSRF token

                            let pollingIntervalId = null;
                            let pollCount = 0;
                            const maxPollCount = 20;
                            const pollFrequency = 5000;
                            const userInitiatedRefreshKeyPrefix = 'user_initiated_email_refresh_for_account_';

                            let currentSelectedAccountId = '{{ $selectedAccountId ?? '' }}';
                            if (accountSelectElement && accountSelectElement.value) {
                                currentSelectedAccountId = accountSelectElement.value;
                            }

                            function getSessionKeyForCurrentAccount() {
                                return userInitiatedRefreshKeyPrefix + currentSelectedAccountId;
                            }

                            function setButtonState(state, message = null) {
                                if (!refreshButton) return;
                                // ... (giữ nguyên hàm setButtonState) ...
                                if (state === 'loading') {
                                    refreshButton.classList.add('btn-refresh-disabled');
                                    refreshButton.disabled = true;
                                    refreshButton.innerHTML = message || '⏳ Đang gửi yêu cầu...';
                                } else if (state === 'polling') {
                                    refreshButton.classList.add('btn-refresh-disabled');
                                    refreshButton.disabled = true;
                                    refreshButton.innerHTML = message || '⏳ Đang đồng bộ hóa...';
                                } else {
                                    refreshButton.classList.remove('btn-refresh-disabled');
                                    refreshButton.disabled = false;
                                    refreshButton.innerHTML = '🔄 Làm mới Hộp thư';
                                }
                            }

                            function showAlert(type, message, persistent = false) {
                                // ... (giữ nguyên hàm showAlert) ...
                                if (!persistent) {
                                    alertMessagesContainer.innerHTML = '';
                                }
                                const alertDiv = document.createElement('div');
                                alertDiv.className = `alert alert-${type} mt-3`;
                                alertDiv.textContent = message;
                                alertMessagesContainer.appendChild(alertDiv);
                            }

                            async function checkJobStatus(isInitialLoad = false) {
                                // ... (logic checkJobStatus giữ nguyên, vì API status của bạn là chung) ...
                                // (Chỉ cần đảm bảo nó xử lý đúng các trạng thái và reload khi cần)
                                if (!currentSelectedAccountId &&
                                    pollingIntervalId) { // Thêm kiểm tra nếu không có acc thì dừng poll
                                    stopPolling();
                                    setButtonState('idle');
                                    return;
                                }
                                if (!currentSelectedAccountId && !
                                    isInitialLoad) { // Nếu không có acc và không phải initial load thì không làm gì
                                    return;
                                }


                                pollCount++;
                                if (pollCount > maxPollCount && pollingIntervalId) {
                                    console.warn('Polling timeout reached for account ' + currentSelectedAccountId);
                                    stopPolling();
                                    setButtonState('idle');
                                    showAlert('warning', 'Quá trình đồng bộ mất quá nhiều thời gian. Vui lòng thử lại sau.');
                                    sessionStorage.removeItem(getSessionKeyForCurrentAccount());
                                    return;
                                }

                                try {
                                    const response = await fetch('{{ route('emails.api.email.sync.status') }}', {
                                        method: 'GET',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    });
                                    if (!response.ok) {
                                        if (pollingIntervalId) stopPolling();
                                        setButtonState('idle');
                                        showAlert('danger', `Lỗi máy chủ khi kiểm tra trạng thái: ${response.status}`);
                                        sessionStorage.removeItem(getSessionKeyForCurrentAccount());
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    const data = await response.json();
                                    const userInitiatedThisAccount = sessionStorage.getItem(
                                        getSessionKeyForCurrentAccount()) === 'true';

                                    if (data.status === 'completed_refresh') {
                                        console.log('API says: Email sync completed.');
                                        stopPolling();
                                        setButtonState('idle');
                                        if (userInitiatedThisAccount) {
                                            showAlert('success', 'Đồng bộ email hoàn tất! Đang tải lại trang...');
                                            sessionStorage.removeItem(getSessionKeyForCurrentAccount());
                                            setTimeout(() => {
                                                let reloadUrl = '{{ route('emails.index') }}';
                                                if (currentSelectedAccountId) {
                                                    reloadUrl += '?account_id=' + currentSelectedAccountId;
                                                }
                                                window.location.href = reloadUrl;
                                            }, 1500);
                                        }
                                    } else if (data.status === 'pending_refresh') {
                                        console.log('API says: Email sync pending...');
                                        setButtonState('polling', data.message ||
                                            '⏳ Đang đồng bộ hóa...'); // Cập nhật message từ API nếu có
                                        if (!pollingIntervalId) {
                                            startPolling(false); // Không check ngay, đợi interval
                                        }
                                    } else {
                                        console.warn('API returned unexpected status:', data.status);
                                        if (pollingIntervalId) stopPolling();
                                        setButtonState('idle');
                                        sessionStorage.removeItem(getSessionKeyForCurrentAccount());
                                    }
                                } catch (error) {
                                    console.error('Error checking job status:', error);
                                    if (pollingIntervalId) stopPolling();
                                    setButtonState('idle');
                                    sessionStorage.removeItem(getSessionKeyForCurrentAccount());
                                }
                            }

                            function startPolling(initialCheck = true) {
                                // ... (giữ nguyên hàm startPolling, đảm bảo có currentSelectedAccountId) ...
                                if (pollingIntervalId) clearInterval(pollingIntervalId);
                                pollCount = 0;
                                if (!currentSelectedAccountId) {
                                    console.log('No account selected, polling not started.');
                                    setButtonState('idle');
                                    if (refreshButton) refreshButton.disabled = true; // Vô hiệu hóa nút nếu không có tài khoản
                                    return;
                                }
                                setButtonState('polling');
                                if (initialCheck) {
                                    checkJobStatus();
                                }
                                pollingIntervalId = setInterval(checkJobStatus, pollFrequency);
                            }

                            function stopPolling() {
                                // ... (giữ nguyên hàm stopPolling) ...
                                if (pollingIntervalId) {
                                    clearInterval(pollingIntervalId);
                                    pollingIntervalId = null;
                                    console.log('Polling stopped for account ' + currentSelectedAccountId);
                                }
                            }

                            if (refreshButton) {
                                if (currentSelectedAccountId) {
                                    checkJobStatus(true); // Kiểm tra trạng thái khi tải trang nếu có tài khoản được chọn
                                } else {
                                    setButtonState('idle');
                                    refreshButton.disabled = true;
                                    refreshButton.classList.add('btn-refresh-disabled');
                                }

                                refreshButton.addEventListener('click', async function() {
                                    if (this.disabled) return;

                                    // Luôn cập nhật currentSelectedAccountId từ select box trước khi gửi request
                                    if (accountSelectElement && accountSelectElement.value) {
                                        currentSelectedAccountId = accountSelectElement.value;
                                    } else { // Nếu không có giá trị nào từ select (ví dụ select rỗng)
                                        currentSelectedAccountId = ''; // Reset
                                    }

                                    if (!currentSelectedAccountId) {
                                        showAlert('warning', 'Vui lòng chọn một tài khoản để làm mới.');
                                        return;
                                    }

                                    setButtonState('loading');
                                    alertMessagesContainer.innerHTML = ''; // Xóa thông báo cũ
                                    showAlert('info', 'Đang gửi yêu cầu làm mới cho tài khoản ' +
                                        currentSelectedAccountId + '...');
                                    sessionStorage.setItem(getSessionKeyForCurrentAccount(), 'true');

                                    // --- THAY ĐỔI CHÍNH Ở ĐÂY ---
                                    // Tạo URL động cho route fetchForAccount
                                    // Đảm bảo bạn đã định nghĩa route 'emails.fetchForAccount' trong web.php
                                    // Route::post('/emails/account/{accountId}/fetch', ...)->name('emails.fetchForAccount');
                                    // Cách 1: Tạo URL thủ công (nếu không dùng Ziggy hoặc tương tự)
                                    let fetchUrl = `/emails/apps/email/account/${currentSelectedAccountId}/fetch`;
                                    // Cách 2: Nếu bạn có Ziggy hoặc một helper để tạo route trong JS:
                                    // let fetchUrl = route('emails.fetchForAccount', { accountId: currentSelectedAccountId });

                                    try {
                                        const response = await fetch(fetchUrl, {
                                            method: 'POST', // Sử dụng POST
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'X-CSRF-TOKEN': csrfToken // Gửi kèm CSRF token
                                            }
                                        });

                                        const data = await response.json();

                                        // Kiểm tra key 'job_dispatched' (singular) từ response của fetchEmailsForSpecificAccount
                                        if (response.ok && data.job_dispatched) {
                                            showAlert('info', data.message ||
                                                `Đã gửi yêu cầu cho tài khoản ${currentSelectedAccountId}, đang chờ đồng bộ...`
                                            );
                                            startPolling(); // Bắt đầu kiểm tra trạng thái
                                        } else {
                                            // Xử lý lỗi cụ thể từ server nếu có
                                            let errorMessage = data.message || (data.error ? data.error :
                                                'Không thể gửi yêu cầu làm mới.');
                                            if (response.status === 404) errorMessage =
                                                'API không tìm thấy hoặc tài khoản không tồn tại.';
                                            throw new Error(errorMessage);
                                        }
                                    } catch (error) {
                                        console.error('Error dispatching fetch job for specific account:', error);
                                        setButtonState('idle');
                                        showAlert('danger', error.message || 'Lỗi khi gửi yêu cầu làm mới.');
                                        sessionStorage.removeItem(getSessionKeyForCurrentAccount());
                                    }
                                });
                            }

                            // Cập nhật currentSelectedAccountId nếu người dùng thay đổi lựa chọn trong select box
                            if (accountSelectElement) {
                                accountSelectElement.addEventListener('change', function() {
                                    // currentSelectedAccountId = this.value; // Form sẽ submit, trang reload, PHP sẽ lấy giá trị mới
                                    // Không cần set currentSelectedAccountId ở đây vì form submit sẽ reload trang
                                    // và PHP sẽ cập nhật biến $selectedAccountId.
                                    // Tuy nhiên, nếu bạn bỏ onchange="this.form.submit()" và xử lý bằng JS hoàn toàn thì cần cập nhật ở đây.
                                });
                            }
                            // END XỬ LÝ NÚT "LÀM MỚI HỘP THƯ"

                            // START XỬ LÝ CHỨC NĂNG "SỬA THÔNG TIN MAILACCOUNT TRONG POPUP"
                            // -------------------------------------------------------------
                            // LOGIC CHO CHỨC NĂNG "SỬA THÔNG TIN MAILACCOUNT TRONG POPUP"
                            // -------------------------------------------------------------

                            const editMailAccountModal = new bootstrap.Modal(document.getElementById('editMailAccountModal'));
                            const editMailAccountForm = document.getElementById('editMailAccountForm');
                            // Lấy references cho các input trong modal *trước khi* mở modal
                            const editMailAccountIdInput = document.getElementById('edit_mail_account_id');
                            const editMailAccountEmailInput = document.getElementById('edit_mail_account_email');
                            const editMailAccountAppPasswordInput = document.getElementById('edit_mail_account_app_password');
                            const editMailAccountImapHostInput = document.getElementById('edit_mail_account_imap_host');
                            const editMailAccountImapPortInput = document.getElementById('edit_mail_account_imap_port');
                            const editMailAccountImapEncryptionInput = document.getElementById('edit_mail_account_imap_encryption');


                            // Xử lý khi click vào nút "Draft (Sửa TK)" ở sidebar
                            const openEditSelectedMailAccountModalBtn = document.getElementById('openEditSelectedMailAccountModal');
                            if (openEditSelectedMailAccountModalBtn) {
                                openEditSelectedMailAccountModalBtn.addEventListener('click', async function() {
                                    if (!currentSelectedAccountId || currentSelectedAccountId === '') {
                                        showAlert('warning', 'Vui lòng chọn một tài khoản để chỉnh sửa.');
                                        editMailAccountModal.hide(); // Đóng modal nếu không có tài khoản
                                        return;
                                    }

                                    // Hiển thị loading trong modal
                                    const modalBody = document.querySelector('#editMailAccountModal .modal-body');
                                    // Lưu lại nội dung gốc của modal body để khôi phục sau khi tải xong
                                    // SỬA LỖI Ở ĐÂY: Lấy nội dung form HTML, không phải toàn bộ modal-body
                                    // Để không mất tham chiếu input, ta chỉ ẩn/hiện loader bên trong form.
                                    const formContent = editMailAccountForm.innerHTML; // Lưu nội dung form
                                    editMailAccountForm.innerHTML =
                                        '<div class="text-center py-5" id="loaderContent">Đang tải thông tin tài khoản...</div>';


                                    try {
                                        // Gửi AJAX GET đến route để lấy thông tin MailAccount
                                        const response = await fetch(
                                            `{{ url('emails/get-mail-account') }}/${currentSelectedAccountId}`, {
                                                method: 'GET',
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'X-Requested-With': 'XMLHttpRequest'
                                                }
                                            });

                                        if (!response.ok) {
                                            const errorData = await response.json();
                                            throw new Error(errorData.message || 'Lỗi khi tải thông tin tài khoản.');
                                        }

                                        const accountData = await response.json();

                                        // SỬA LỖI Ở ĐÂY: Khôi phục nội dung form TRƯỚC KHI điền dữ liệu
                                        editMailAccountForm.innerHTML = formContent;

                                        // SỬA LỖI Ở ĐÂY: Lấy LẠI tham chiếu đến các input SAU KHI khôi phục HTML
                                        const currentEditMailAccountIdInput = document.getElementById(
                                            'edit_mail_account_id');
                                        const currentEditMailAccountEmailInput = document.getElementById(
                                            'edit_mail_account_email');
                                        const currentEditMailAccountAppPasswordInput = document.getElementById(
                                            'edit_mail_account_app_password');
                                        const currentEditMailAccountImapHostInput = document.getElementById(
                                            'edit_mail_account_imap_host');
                                        const currentEditMailAccountImapPortInput = document.getElementById(
                                            'edit_mail_account_imap_port');
                                        const currentEditMailAccountImapEncryptionInput = document.getElementById(
                                            'edit_mail_account_imap_encryption');

                                        // Điền dữ liệu vào form popup bằng CÁC THAM CHIẾU MỚI
                                        currentEditMailAccountIdInput.value = accountData.id;
                                        currentEditMailAccountEmailInput.value = accountData.email || '';
                                        currentEditMailAccountAppPasswordInput.value = ''; // Luôn để trống mật khẩu
                                        currentEditMailAccountImapHostInput.value = accountData.imap_host || '';
                                        currentEditMailAccountImapPortInput.value = accountData.imap_port || '';

                                        // Xử lý cho thẻ <select>
                                        // Đảm bảo option được chọn dựa trên giá trị từ database
                                        if (currentEditMailAccountImapEncryptionInput) {
                                            currentEditMailAccountImapEncryptionInput.value = accountData
                                                .imap_encryption || 'ssl';
                                        }

                                        document.getElementById('editMailAccountModalLabel').textContent =
                                            `Sửa Tài khoản: ${accountData.email}`;
                                        // Cập nhật action của form để trỏ đến route update của MailAccount
                                        editMailAccountForm.action = `{{ url('emails/update') }}/${accountData.id}`;
                                        editMailAccountForm.querySelector('input[name="_method"]').value =
                                        'PUT'; // Đặt phương thức là PUT

                                        editMailAccountModal.show();
                                    } catch (error) {
                                        console.error('Lỗi khi tải tài khoản để chỉnh sửa:', error);
                                        showAlert('danger', error.message ||
                                            'Không thể tải thông tin tài khoản để chỉnh sửa.');
                                        // Khôi phục form về trạng thái ban đầu nếu có lỗi
                                        editMailAccountForm.innerHTML = formContent;
                                    }
                                });
                            }

                            // Xử lý submit form chỉnh sửa MailAccount
                            editMailAccountForm.addEventListener('submit', async function(event) {
                                event.preventDefault();

                                const method = editMailAccountForm.querySelector('input[name="_method"]')
                                .value; // Lấy phương thức hiện tại (PUT)
                                const submitUrl = editMailAccountForm.action;

                                const formData = new FormData(editMailAccountForm);
                                // Chuyển FormData sang JSON object
                                const data = {};
                                formData.forEach((value, key) => {
                                    data[key] = value;
                                });
                                // Xử lý trường _method riêng
                                if (data._method) {
                                    delete data._method;
                                }

                                try {
                                    const response = await fetch(submitUrl, {
                                        method: method, // Sử dụng phương thức PUT
                                        headers: {
                                            'Content-Type': 'application/json', // Quan trọng: gửi JSON
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': csrfToken
                                        },
                                        body: JSON.stringify(data)
                                    });

                                    const result = await response.json();

                                    if (response.ok) {
                                        showAlert('success', result.message ||
                                        'Tài khoản đã được cập nhật thành công!');
                                        editMailAccountModal.hide();
                                        // Tải lại trang để thấy các thay đổi (nếu có)
                                        setTimeout(() => window.location.reload(), 1000);
                                    } else {
                                        // Hiển thị lỗi validation từ server
                                        let errorMessage = result.message || 'Lỗi khi cập nhật tài khoản.';
                                        if (result.errors) {
                                            errorMessage += '<br>' + Object.values(result.errors).map(e => e.join(', '))
                                                .join('<br>');
                                        }
                                        showAlert('danger', errorMessage);
                                    }
                                } catch (error) {
                                    console.error('Lỗi khi cập nhật tài khoản:', error);
                                    showAlert('danger', 'Đã xảy ra lỗi không mong muốn khi cập nhật tài khoản.');
                                }
                            });

                            // Hàm để reset form khi modal đóng
                            function resetMailAccountForm() {
                                editMailAccountForm.reset();
                                // Reset các tham chiếu input về giá trị ban đầu nếu chúng đã được khai báo ở scope global
                                // hoặc bạn có thể bỏ qua dòng này nếu các input luôn được lấy lại khi modal mở
                                if (editMailAccountIdInput) editMailAccountIdInput.value = '';
                                if (editMailAccountEmailInput) editMailAccountEmailInput.value = '';
                                if (editMailAccountAppPasswordInput) editMailAccountAppPasswordInput.value = '';
                                if (editMailAccountImapHostInput) editMailAccountImapHostInput.value = '';
                                if (editMailAccountImapPortInput) editMailAccountImapPortInput.value = '';
                                if (editMailAccountImapEncryptionInput) editMailAccountImapEncryptionInput.value = '';

                                document.getElementById('editMailAccountModalLabel').textContent = "Sửa Tài khoản";
                                editMailAccountForm.querySelector('input[name="_method"]').value =
                                'PUT'; // Đặt phương thức mặc định là PUT
                            }

                            // Reset form khi modal đóng
                            document.getElementById('editMailAccountModal').addEventListener('hidden.bs.modal', function() {
                                // SỬA LỖI Ở ĐÂY: Khi modal đóng, khôi phục lại form HTML gốc.
                                // Điều này đảm bảo lần mở tiếp theo form sẽ đúng cấu trúc.
                                // Sau đó, gọi resetMailAccountForm() để xóa dữ liệu cũ.
                                // Cần một cách để lấy nội dung HTML gốc của form.
                                // Cách đơn giản nhất là giữ nó trong một biến khi DOMContentLoaded.
                                // Hoặc chỉ reset các trường mà không ghi đè innerHTML của form.
                                // Phương án 2 (chỉ reset trường) được ưu tiên hơn để tránh mất tham chiếu.
                                // resetMailAccountForm(); // Hàm này đã được gọi ở cuối listener click.
                            });
                            //END XỬ LÝ CHỨC NĂNG "SỬA THÔNG TIN MAILACCOUNT TRONG POPUP"
                        });
                    </script>
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
    {{-- MODAL MỚI CHO CHỈNH SỬA THÔNG TIN MAILACCOUNT --}}
    <div class="modal fade" id="editMailAccountModal" tabindex="-1" aria-labelledby="editMailAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="editMailAccountForm" method="POST"> {{-- action và method sẽ được set bằng JS --}}
                    @csrf
                    @method('PUT') {{-- Placeholder, sẽ được ghi đè bằng PUT bởi JS --}}
                    <input type="hidden" name="id" id="edit_mail_account_id"> {{-- Để lưu ID MailAccount --}}

                    <div class="modal-header">
                        <h5 class="modal-title" id="editMailAccountModalLabel">Sửa Tài khoản Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_mail_account_email" class="form-label">Địa chỉ Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_mail_account_email" name="email"
                                required readonly>
                            {{-- Validation error sẽ được xử lý từ phản hồi AJAX --}}
                        </div>

                        <div class="mb-3">
                            <label for="edit_mail_account_app_password" class="form-label">Mật khẩu ứng dụng (Để trống nếu
                                không muốn thay đổi)</label>
                            <input type="password" class="form-control" id="edit_mail_account_app_password"
                                name="app_password">
                            <small class="form-text text-muted">Chỉ nhập nếu bạn muốn thay đổi mật khẩu ứng dụng.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="edit_mail_account_imap_host" class="form-label">Máy chủ IMAP</label>
                                <input type="text" class="form-control" id="edit_mail_account_imap_host"
                                    name="imap_host" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_mail_account_imap_port" class="form-label">Cổng IMAP</label>
                                <input type="number" class="form-control" id="edit_mail_account_imap_port"
                                    name="imap_port" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_mail_account_imap_encryption" class="form-label">Mã hóa IMAP</label>
                            <select class="form-select" id="edit_mail_account_imap_encryption" name="imap_encryption"
                                readonly>
                                <option value="ssl" readonly>SSL</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
