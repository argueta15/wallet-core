<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;

class WalletRepository implements WalletRepositoryInterface
{
    protected $model;

    public function __construct(Wallet $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Wallet
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $wallet = $this->find($id);
        $wallet->update($data);
        return $wallet;
    }

    public function delete($id)
    {
        $wallet = $this->find($id);
        return $wallet->delete();
    }

    public function getUserTransactionsPaginated($userId, $perPage = 10, $page = 1): mixed
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getAllUserTransactions($userId): mixed
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();
    }
}