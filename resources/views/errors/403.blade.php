<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access denied · SEHRS</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sehrs.css') }}">
</head>
<body>
<div class="container py-5 text-center">
    <div class="display-1 text-danger"><i class="bi bi-shield-x"></i></div>
    <h1 class="h3">Access denied (403)</h1>
    <p class="text-muted">{{ $exception->getMessage() ?: 'You do not have permission to view this page.' }}</p>
    <p class="small text-muted">This attempt has been recorded in the audit log.</p>
    <a href="{{ url('/') }}" class="btn btn-primary"><i class="bi bi-house"></i> Back to dashboard</a>
</div>
</body>
</html>
