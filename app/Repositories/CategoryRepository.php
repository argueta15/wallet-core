<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
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

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = $this->find($id);
        return $category->delete();
    }

    public function findOrCreateCategoryByUser($userId, $name): Category
    {
        return $this->model->firstOrCreate([
            'name' => $name,
            'user_id' => $userId,
        ]);
    }
}