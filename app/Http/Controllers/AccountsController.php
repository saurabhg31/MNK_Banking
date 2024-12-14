<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AccountsController extends Controller
{
    public function addAccounts(Request $request)
    {
        $request->validate([
            'user' => 'required|array',
            'user.*' => 'required|integer|exists:users,id',
            'firstname' => 'required|array',
            'lastname' => 'required|array',
            'dob' => 'required|array',
            'dob.*' => 'required|date|date_format:Y-m-d',
            'address' => 'required|array'
        ]);

        $accounts = [];

        foreach ($request->firstname as $index => $firstName) {
            $account = [
                'user_id' => $request->user[$index],
                'first_name' => $firstName,
                'last_name' => $request->lastname[$index],
                'dob' => $request->dob[$index],
                'address' => $request->address[$index]
            ];
            array_push($accounts, Account::addAccount($account));
        }

        foreach($accounts as $accountInfo) {
            if (isset($accountInfo['error'])){
                Session::flash('error', $accountInfo['error']);
                return redirect(route('dashboard'));
            }
        }

        Session::flash('msg', 'Accounts created');
        return redirect(route('dashboard'));
    }
}
