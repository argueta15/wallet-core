<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * @OA\Schema(
     *  schema="WalletResource",
     * type="object",
     * @OA\Property(property="id", type="integer"),
     * @OA\Property(property="type", type="string"),
     * @OA\Property(property="date", type="string"),
     * @OA\Property(property="amount", type="number"),
     * @OA\Property(property="description", type="string"),
     * @OA\Property(property="category", type="string")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'date' => $this->date,
            'amount' => $this->amount / 100,
            'description' => $this->description,
            'category' => $this->category ? $this->category->name : null,
        ];
    }
}
