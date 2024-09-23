<?php

namespace App\Repositories\Interfaces;

interface WalletRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getUserTransactionsPaginated($userId, $perPage = 10, $page = 1);
    public function getAllUserTransactions($userId);
}