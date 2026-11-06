<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign in') · SEHRS</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sehrs.css') }}">
</head>
<body>
<div class="auth-wrapper p-3">
    <div class="w-100 d-flex flex-column align-items-center">
        <div class="text-white text-center mb-4">
            <div class="fs-1"><i class="bi bi-shield-plus"></i></div>
            <h1 class="h4 fw-bold mb-0">Secure Electronic Health Records</h1>
            <div class="small opacity-75">Iconic Open University · Group 12</div>
        </div>
        @yield('content')
        <div class="text-white-50 synthetic-banner mt-3 text-center">
            Academic prototype — synthetic patient data only
        </div>
    </div>
</div>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
