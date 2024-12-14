<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'showDashboard'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/register/verify-2fa', [RegisteredUserController::class, 'verify2FA'])->name('verify.2fa');
Route::get('/login/verify-2fa', function () {
    return view('auth.verify_2fa_login');
})->name('verify.2fa.login');
Route::post('/login/verify-2fa', [AuthenticatedSessionController::class, 'verifyLogin2FA']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware(IsAdmin::class)->group(function () {
        Route::post('/add/accounts', [AccountsController::class, 'addAccounts'])->name('admin.add_accounts');
        Route::get('/transaction/detail/{accountId}', [TransactionController::class, 'showTransactions']);
    });
    Route::post('/transfer/funds', [TransactionController::class, 'transferFunds'])->name('funds.transfer');
});

require __DIR__ . '/auth.php';
