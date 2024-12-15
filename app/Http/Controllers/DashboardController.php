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

            $accountIds = $accounts->pluck('id');
            $accountIdsWithTransactions = Transaction::getAccountIdsWithTransactions($accountIds);

            foreach ($accounts as $account) {
                $account->hasTransaction = in_array($account->id, $accountIdsWithTransactions);
            }
            return view('dashboard', compact('users', 'accounts'));
        }
        $user = true;
        $account = Account::where('user_id', Auth::id())->first();
        return view('dashboard', compact('user', 'account'));
    }
}
