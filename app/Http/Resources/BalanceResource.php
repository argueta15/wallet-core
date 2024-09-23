<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
{
    /**
     * @OA\Schema(
     *  schema="BalanceResource",
     * type="object",
     * @OA\Property(property="userId", type="integer"),
     * @OA\Property(property="totalIncome", type="number"),
     * @OA\Property(property="incomeThisMonth", type="number"),
     * @OA\Property(property="totalExpense", type="number"),
     * @OA\Property(property="expenseThisMonth", type="number"),
     * @OA\Property(property="generalBalance", type="number"),
     * @OA\Property(property="balanceThisMonth", type="number")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'userId' => $this->user_id,
            'totalIncome' => $this->total_income / 100,
            'incomeThisMonth' => $this->income_this_month / 100,
            'totalExpense' => $this->total_expense / 100,
            'expenseThisMonth' => $this->expense_this_month / 100,
            'generalBalance' => $this->general_balance / 100,
            'balanceThisMonth' => $this->balance_this_month / 100,
            'tip' => $this->tip,
        ];
    }
}
