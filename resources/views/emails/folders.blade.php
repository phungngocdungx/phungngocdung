<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>📁 Thư mục của {{ $account->email }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <a href="{{ route('mail.index') }}" class="btn btn-secondary mb-3">← Chọn tài khoản khác</a>
    <h3>📁 Thư mục của: <strong>{{ $account->email }}</strong></h3>

    <div class="list-group mt-4">
        @foreach($folders as $folder)
            <a href="{{ route('mail.emails', ['id' => $account->id, 'folder' => $folder->path]) }}"
               class="list-group-item list-group-item-action">
                {{ $folder->name }} <small class="text-muted">({{ $folder->path }})</small>
            </a>
        @endforeach
    </div>
</div>
</body>
</html>
