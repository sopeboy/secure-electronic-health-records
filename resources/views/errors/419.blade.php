<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expired · SEHRS</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
</head>
<body>
<div class="container py-5 text-center">
    <h1 class="h3">Your session has expired</h1>
    <p class="text-muted">For security, you are signed out after 30 minutes without activity.</p>
    <a href="{{ url('/login') }}" class="btn btn-primary">Sign in again</a>
</div>
</body>
</html>
