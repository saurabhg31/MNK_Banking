<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $isAdmin = Auth::user()->is_admin;
        if ($isAdmin) {
            $users = User::select(['id', 'name'])->where('is_admin', false)->get();
            $accounts = Account::getAccountsWithUserNames();
            foreach ($accounts as $account) {
                $account->hasTransaction = Transaction::hasTransactions($account->id);
            }
            return view('dashboard', compact('users', 'accounts'));
        }
        $user = true;
        
        return view('dashboard', compact('user'));
    }
}
