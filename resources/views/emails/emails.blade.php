<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>📬 Hộp thư {{ $account->email }} - {{ $folderPath }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <a href="{{ route('mail.folders', ['id' => $account->id]) }}" class="btn btn-secondary mb-3">← Chọn thư mục khác</a>
    <h4>📬 Hộp thư của: <strong>{{ $account->email }}</strong></h4>
    <h6>Thư mục: <em>{{ $folderPath }}</em></h6>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(count($messages) == 0)
        <div class="alert alert-info">Không có email nào trong thư mục này.</div>
    @endif

    @foreach($messages as $message)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $message->getSubject() ?: '(Không tiêu đề)' }}</h5>
                <h6 class="card-subtitle mb-2 text-muted">
                    Từ: {{ implode(', ', $message->getFrom()) }} | 
                    Ngày: {{ $message->getDate()->format('d/m/Y H:i') }}
                </h6>
                <hr>
                <div>{!! $message->getHTMLBody(true) ?: nl2br(e($message->getTextBody())) !!}</div>
            </div>
        </div>
    @endforeach
</div>
</body>
</html>
