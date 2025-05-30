{{-- ... (phần HTML của view giữ nguyên như trước) ... --}}
<div class="container">
    <head>
        <title>Hộp thư đến {{ $selectedAccount ? 'của ' . $selectedAccount->email : '' }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Quan trọng cho POST/PUT/DELETE AJAX nếu cần --}}
        <style>
            .btn-refresh-disabled {
                opacity: 0.65;
                cursor: not-allowed;
            }
        </style>
    </head>

    <h2>📥 Hộp thư đến {{ $selectedAccount ? 'của ' . $selectedAccount->email : '' }}</h2>

    <form method="GET" action="{{ route('emails.index') }}" class="mb-3" id="accountSelectForm">
        <div class="d-flex align-items-center gap-2">
            <label for="account_id">Tài khoản:</label>
            <select name="account_id" id="account_id" class="form-select w-auto" onchange="this.form.submit()">
                @if($accounts->isEmpty())
                    <option value="">-- Chưa có tài khoản --</option>
                @else
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ (string)$selectedAccountId == (string)$acc->id ? 'selected' : '' }}>
                            {{ $acc->email }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </form>

    <div class="mb-3">
        <button type="button" class="btn btn-primary" id="refreshEmailsButton">
            🔄 Làm mới Hộp thư
        </button>
    </div>

    <div id="alertMessages">
        @if (session('status'))
            <div class="alert alert-success mt-3">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif
        @if ($error && !session('error') && !session('status')) {{-- Chỉ hiển thị lỗi từ controller nếu không có lỗi session --}}
            <div class="alert alert-danger mt-3">{{ $error }}</div>
        @endif
    </div>


    @if($selectedAccount && $emails->isNotEmpty())
        <table class="table table-hover table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>📧 Chủ đề</th>
                    <th>👤 Người gửi</th>
                    <th>📅 Ngày gửi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($emails as $email)
                    <tr>
                        <td>{{ $email->subject }}</td>
                        <td>
                            {{ $email->from_name ?? $email->from ?? 'Không rõ' }}
                            @if($email->from_name && $email->from && $email->from_name !== $email->from)
                                <small>&lt;{{ $email->from }}&gt;</small>
                            @endif
                        </td>
                        <td>
                            {{ $email->date ? $email->date->format('d/m/Y H:i:s') : 'Không rõ' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
         {{-- {{ $emails->links() }} --}}
    @elseif($selectedAccount && !$error)
        <div class="alert alert-info mt-3">Không có email nào trong hộp thư của tài khoản {{ $selectedAccount->email }}.</div>
    @elseif(!$selectedAccount && $accounts->isNotEmpty() && !$error)
        <div class="alert alert-info mt-3">Vui lòng chọn một tài khoản để xem email.</div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const refreshButton = document.getElementById('refreshEmailsButton');
        const alertMessagesContainer = document.getElementById('alertMessages');
        const accountSelectElement = document.getElementById('account_id'); // Lấy select element

        let pollingIntervalId = null;
        let pollCount = 0;
        const maxPollCount = 20; 
        const pollFrequency = 5000; 
        const userInitiatedRefreshKey = 'user_initiated_email_refresh_for_account_'; // Thêm account ID vào key

        // Lấy selectedAccountId từ PHP nếu có, hoặc từ select box nếu người dùng vừa đổi
        let currentSelectedAccountId = '{{ $selectedAccountId ?? ($accounts->isNotEmpty() ? $accounts->first()->id : '') }}';
        if (accountSelectElement && accountSelectElement.value) {
            currentSelectedAccountId = accountSelectElement.value;
        }


        function getSessionKeyForCurrentAccount() {
            return userInitiatedRefreshKey + currentSelectedAccountId;
        }

        function setButtonState(state, message = null) {
            if (!refreshButton) return;

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
            if (!persistent) {
                alertMessagesContainer.innerHTML = ''; 
            }
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} mt-3`;
            alertDiv.textContent = message;
            alertMessagesContainer.appendChild(alertDiv);
        }

        async function checkJobStatus(isInitialLoad = false) {
            // Nếu không có tài khoản nào được chọn, không làm gì cả
            if (!currentSelectedAccountId) {
                setButtonState('idle'); // Đảm bảo nút có thể bấm nếu có tài khoản được thêm sau
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
                const response = await fetch('{{ route('api.email.sync.status') }}', { // API này vẫn kiểm tra chung
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
                const userInitiatedThisAccount = sessionStorage.getItem(getSessionKeyForCurrentAccount()) === 'true';

                if (data.status === 'completed_refresh') {
                    console.log('API says: Email sync completed for some/all accounts.');
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
                            console.log('Reloading to: ' + reloadUrl);
                            window.location.href = reloadUrl; // Chuyển hướng đến URL có account_id
                        }, 1500);
                    } else if (isInitialLoad) {
                        console.log('Initial load, sync already completed, no user action pending for account ' + currentSelectedAccountId);
                    }
                } else if (data.status === 'pending_refresh') {
                    console.log('API says: Email sync pending for some/all accounts...');
                    setButtonState('polling'); 
                    if (!pollingIntervalId) { 
                        startPolling(false); 
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
            if (pollingIntervalId) clearInterval(pollingIntervalId); 
            pollCount = 0; 
            // Không đặt polling ngay nếu không có tài khoản được chọn
            if (!currentSelectedAccountId) {
                 console.log('No account selected, polling not started.');
                 setButtonState('idle'); // Đảm bảo nút không bị kẹt ở trạng thái loading/polling
                 return;
            }
            setButtonState('polling');
            if (initialCheck) {
                checkJobStatus(); 
            }
            pollingIntervalId = setInterval(checkJobStatus, pollFrequency);
        }

        function stopPolling() {
            if (pollingIntervalId) {
                clearInterval(pollingIntervalId);
                pollingIntervalId = null;
                console.log('Polling stopped for account ' + currentSelectedAccountId);
            }
        }

        if (refreshButton) {
            if (currentSelectedAccountId) { // Chỉ kiểm tra trạng thái nếu có tài khoản được chọn
                checkJobStatus(true); 
            } else {
                setButtonState('idle'); // Nếu không có tài khoản nào, nút ở trạng thái bình thường
                refreshButton.disabled = true; // Vô hiệu hóa nút nếu không có tài khoản
                refreshButton.classList.add('btn-refresh-disabled');
            }


            refreshButton.addEventListener('click', async function () {
                if (this.disabled) return; 
                
                // Cập nhật lại currentSelectedAccountId phòng trường hợp trang không tải lại khi chọn tài khoản (mặc dù có onchange submit)
                if (accountSelectElement && accountSelectElement.value) {
                    currentSelectedAccountId = accountSelectElement.value;
                }
                if (!currentSelectedAccountId) {
                    showAlert('warning', 'Vui lòng chọn một tài khoản để làm mới.');
                    return;
                }

                setButtonState('loading');
                alertMessagesContainer.innerHTML = ''; 
                showAlert('info', 'Đang gửi yêu cầu làm mới hộp thư...');
                sessionStorage.setItem(getSessionKeyForCurrentAccount(), 'true'); 

                try {
                    // Route emails.triggerfetch sẽ dispatch job cho TẤT CẢ tài khoản
                    // API kiểm tra trạng thái cũng kiểm tra chung cho tất cả FetchMailJob
                    const response = await fetch('{{ route('emails.triggerfetch') }}', { 
                        method: 'GET', 
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.jobs_dispatched) {
                        showAlert('info', data.message || 'Đã gửi yêu cầu, đang chờ đồng bộ...');
                        startPolling(); 
                    } else {
                        throw new Error(data.message || 'Không thể gửi yêu cầu làm mới.');
                    }
                } catch (error) {
                    console.error('Error dispatching fetch job:', error);
                    setButtonState('idle');
                    showAlert('danger', error.message || 'Lỗi khi gửi yêu cầu làm mới.');
                    sessionStorage.removeItem(getSessionKeyForCurrentAccount()); 
                }
            });
        }
    });
</script>
{{-- @endsection --}}

{{-- <td><b>{{ $email->from_name }}</b></td> --}}