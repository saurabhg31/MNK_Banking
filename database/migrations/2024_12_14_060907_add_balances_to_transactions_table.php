<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->double('from_account_bal_before_transfer')->after('from_account_id');
            $table->double('from_account_bal_after_transfer')->after('from_account_bal_before_transfer');
            $table->double('to_account_bal_before_transfer')->after('to_account_id');
            $table->double('to_account_bal_after_transfer')->after('to_account_bal_before_transfer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
