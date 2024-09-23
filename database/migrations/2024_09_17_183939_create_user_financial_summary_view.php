<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW user_financial_summary AS
            SELECT
                user_id,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN type = 'income' AND date_trunc('month', date) = date_trunc('month', CURRENT_DATE) THEN amount ELSE 0 END) AS income_this_month,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total_expense,
                SUM(CASE WHEN type = 'expense' AND date_trunc('month', date) = date_trunc('month', CURRENT_DATE) THEN amount ELSE 0 END) AS expense_this_month,
                (SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END)) AS general_balance,
                (
                    SUM(CASE WHEN type = 'income' AND date_trunc('month', date) = date_trunc('month', CURRENT_DATE) THEN amount ELSE 0 END) -
                    SUM(CASE WHEN type = 'expense' AND date_trunc('month', date) = date_trunc('month', CURRENT_DATE) THEN amount ELSE 0 END)
                ) AS balance_this_month
            FROM
                wallets
            GROUP BY
                user_id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS user_financial_summary;");
    }
};
