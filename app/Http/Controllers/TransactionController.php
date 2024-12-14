<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{
    public function showTransactions(int $accountId)
    {
        $transactions = Transaction::select([
            'from.account_no as from_account',
            'to.account_no as to_account',
            'transactions.*'
        ])->where('from_account_id', $accountId)
            ->orWhere('to_account_id', $accountId)
            ->join('accounts as from', 'from.id', '=', 'transactions.from_account_id')
            ->join('accounts as to', 'to.id', '=', 'transactions.to_account_id')
            ->orderBy('transactions.id', 'desc')->get();

        $account = Account::find($accountId);
        return view('transactions', compact('transactions', 'account'));
    }

    public function transferFunds(Request $request)
    {
        $recieverAccount = Account::where('account_no', $request->recipient_account_no)->first();
        if (!$recieverAccount) {
            Session::flash('error', 'The entered account number could not be found.');
            return back();
        }

        $userAccount = Account::where('user_id', Auth::id())->first();
        if ($request->recipient_account_no == $userAccount->account_no) {
            Session::flash('error', 'Cannot transfer to own account.');
            return back();
        }

        if ($request->amount <= 0) {
            Session::flash('error', 'Transfer amount must be greater than zero');
            return back();
        }

        $amount = $request->amount;
        $intendedCurrency = $request->currency;

        if ($intendedCurrency != 'USD') {

            // $currencyConversionData = Http::get('https://api.exchangeratesapi.io/v1/latest?access_key='.env('EXCHANGE_RATE_KEY').'&base=EUR&symbols=USD,GBP,EUR');

            // using alternative exchange rate api because of base currency restrictions from exchangeratesapi
            $currencyConversionData = Http::get('https://v6.exchangerate-api.com/v6/' . env('EXCHANGE_RATE_2_API_KEY') . '/latest/' . $intendedCurrency);
            if (!$currencyConversionData->successful()) {
                Session::flash('error', 'Unable to fetch conversion data from exchange rates api');
                return back();
            }

            $currencyConversionData = $currencyConversionData->json();
            $currencyConversionData = $currencyConversionData['conversion_rates'];

            $amountInUsd = $amount * $currencyConversionData['USD'];
        } else {
            $amountInUsd = $amount;
        }

        try {
            DB::beginTransaction();
            $transfer = Transaction::transferAmount(
                $amount,
                $intendedCurrency,
                $amountInUsd,
                'USD',
                $recieverAccount,
                $userAccount,
                $request->description
            );
            DB::commit();
            Session::flash('msg', 'Transfer Complete.');
            return redirect(route('dashboard'));
        } catch (Exception $error) {
            DB::rollBack();
            Session::flash('error', $error->getMessage());
            return back();
        }
    }
}
