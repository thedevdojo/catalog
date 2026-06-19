<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

// Application routes are file-based via Laravel Folio.
// See resources/views/pages and App\Providers\FolioServiceProvider.

if (app()->environment('local')) {
    // Convenience login for local demos: /_demo-login signs in the seeded
    // demo customer (demo@catalog.test) without going through the auth flow.
    Route::get('/_demo-login', function () {
        auth()->login(User::where('email', 'demo@catalog.test')->firstOrFail());

        return redirect()->route('home');
    });
}
