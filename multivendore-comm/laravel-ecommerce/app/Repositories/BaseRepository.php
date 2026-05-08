<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    public function __construct(protected Model $model) {}

    public function findById(string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    public function findOrFail(string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
