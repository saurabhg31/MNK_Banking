<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'from_account_id',
        'from_account_bal_before_transfer',
        'from_account_bal_after_transfer',
        'to_account_id',
        'to_account_bal_before_transfer',
        'to_account_bal_after_transfer',
        'amount',
        'amount_currency',
        'payment_amt',
        'payment_currency',
        'description'
    ];

    public static function hasTransactions(int $accountId)
    {
        return self::where('from_account_id', $accountId)->orWhere('to_account_id', $accountId)->exists();
    }

    public static function transferAmount(
        $origAmt,
        $origCurrency,
        $connvertedAmt,
        $intendedCurrency,
        $recieverAccount,
        $userAccount,
        $description
    ) {
        $data = [
            'from_account_id' => $userAccount->id,
            'from_account_bal_before_transfer' => $userAccount->balance,
            'from_account_bal_after_transfer' => $userAccount->balance - $connvertedAmt,
            'to_account_id' => $recieverAccount->id,
            'to_account_bal_before_transfer' => $recieverAccount->balance,
            'to_account_bal_after_transfer' => $recieverAccount->balance + $connvertedAmt,
            'amount' => $origAmt,
            'amount_currency' => $origCurrency,
            'payment_amt' => $connvertedAmt,
            'payment_currency' => $intendedCurrency,
            'description' => $description
        ];
        self::create($data);

        $userAccount->balance = $userAccount->balance - $connvertedAmt;
        $userAccount->save();

        $recieverAccount->balance = $recieverAccount->balance + $connvertedAmt;
        $recieverAccount->save();

        return true;
    }

    public static function getAccountIdsWithTransactions($accountIds)
    {
        $accountIdsWithTransactions = self::select(['from_account_id', 'to_account_id'])->whereIn('from_account_id', $accountIds)
            ->orWhereIn('to_account_id', $accountIds)->get();
        $accountIdsWithTransactionsArr = [];
        foreach ($accountIdsWithTransactions as $accountIdWithTransaction) {
            if (!in_array($accountIdWithTransaction->from_account_id, $accountIdsWithTransactionsArr)) {
                array_push($accountIdsWithTransactionsArr, $accountIdWithTransaction->from_account_id);
            }
            if (!in_array($accountIdWithTransaction->to_account_id, $accountIdsWithTransactionsArr)) {
                array_push($accountIdsWithTransactionsArr, $accountIdWithTransaction->to_account_id);
            }
        }
        return $accountIdsWithTransactionsArr;
    }
}
