<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WalletCollection extends ResourceCollection
{
    /**
     * @OA\Schema(
     *  schema="WalletCollection",
     * type="object",
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/WalletResource")
     * ),
     * @OA\Property(
     * property="pagination",
     * type="object",
     * @OA\Property(property="total", type="integer"),
     * @OA\Property(property="count", type="integer"),
     * @OA\Property(property="per_page", type="integer"),
     * @OA\Property(property="current_page", type="integer"),
     * @OA\Property(property="total_pages", type="integer")
     * )
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'pagination' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
            ],
        ];
    }
}
