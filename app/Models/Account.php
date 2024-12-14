<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id', 'account_no', 'balance', 'currency', 'first_name', 'last_name', 'dob', 'address'
    ];

    public static function addAccount(array $data)
    {
        $userName = User::find($data['user_id'])->name;
        if (self::where('user_id', $data['user_id'])->exists()) {
            return ['error' => 'Saving account already exists for '.$userName];
        }
        $data['account_no'] = self::generateUniqueAccountNumber();
        return self::create($data);
    }

    public static function generateUniqueAccountNumber()
    {
        $accountNumber = rand(100000, 999999);
        while(self::where('account_no', $accountNumber)->exists()){
            $accountNumber = rand(100000, 999999);
        }
        return $accountNumber;
    }

    public static function getAccountsWithUserNames()
    {
        return self::select(['accounts.*', 'users.name as username'])->join('users', 'users.id', '=', 'accounts.user_id')->get();
    }
}
