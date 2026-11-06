<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

// php artisan sehrs:lab-token lab@sehrs.test
Artisan::command('sehrs:lab-token {email}', function (string $email) {
    $user = User::where('email', $email)->first();

    if (! $user || ! $user->hasRole(Role::LAB) || ! $user->isActive()) {
        $this->error('No active lab staff account with that email.');

        return 1;
    }

    $token = $user->createToken('lab-system', ['lab-results:create']);

    $this->info('Lab API token (shown only once — keep it secret, never commit it):');
    $this->line($token->plainTextToken);

    return 0;
})->purpose('Create an API token for the laboratory system');
