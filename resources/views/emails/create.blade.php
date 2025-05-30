<!DOCTYPE html>
<html>

<head>
    <title>Thêm mới tài khoản Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
</head>

<body>
    <h1>Thêm mới tài khoản Email</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('emails.store') }}">
        @csrf

        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="app_password">App Password:</label>
            <input type="password" name="app_password" id="app_password" required>
        </div>

        <div>
            <label for="imap_host">IMAP Host:</label>
            <input type="text" name="imap_host" id="imap_host" value="{{ old('imap_host', 'imap.gmail.com') }}"
                required>
        </div>

        <div>
            <label for="imap_port">IMAP Port:</label>
            <input type="number" name="imap_port" id="imap_port" value="{{ old('imap_port', 993) }}" required>
        </div>

        <div>
            <label for="imap_encryption">IMAP Encryption:</label>
            <select name="imap_encryption" id="imap_encryption" required>
                <option value="ssl" {{ old('imap_encryption', 'ssl') == 'ssl' ? 'selected' : '' }}>SSL</option>
                <option value="tls" {{ old('imap_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
            </select>
        </div>

        <button type="submit">Thêm tài khoản</button>
    </form>

    <p><a href="{{ route('emails.index') }}">Quay lại danh sách tài khoản</a></p>
</body>

</html>
