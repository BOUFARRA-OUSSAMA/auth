<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User as UserEntity;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\ValueObjects\Status;
use App\Infrastructure\Persistence\Models\User as UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID
     *
     * @param int $id
     * @return UserEntity|null
     */
    public function findById(int $id): ?UserEntity
    {
        $model = UserModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find a user by email
     *
     * @param string $email
     * @return UserEntity|null
     */
    public function findByEmail(string $email): ?UserEntity
    {
        $model = UserModel::where('email', $email)->first();

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Save a user (create or update)
     *
     * @param UserEntity $user
     * @return UserEntity
     */
    public function save(UserEntity $user): UserEntity
    {
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'status' => $user->getStatus()->getValue(),
        ];

        if ($user->getId()) {
            $model = UserModel::findOrFail($user->getId());
            $model->update($data);
        } else {
            $model = UserModel::create($data);
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Delete a user
     *
     * @param UserEntity $user
     * @return bool
     */
    public function delete(UserEntity $user): bool
    {
        if (!$user->getId()) {
            return false;
        }

        $model = UserModel::find($user->getId());
        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * Find users by criteria
     *
     * @param array $criteria
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findByCriteria(array $criteria, int $page = 1, int $perPage = 15): array
    {
        $query = UserModel::query();

        if (isset($criteria['name'])) {
            $query->where('name', 'like', '%' . $criteria['name'] . '%');
        }

        if (isset($criteria['email'])) {
            $query->where('email', 'like', '%' . $criteria['email'] . '%');
        }

        if (isset($criteria['status'])) {
            $query->where('status', $criteria['status']);
        }

        // Execute the query with pagination
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Map results to domain entities
        $items = $paginator->items();
        $mappedItems = array_map(function ($model) {
            return $this->mapModelToEntity($model);
        }, $items);

        return [
            'items' => $mappedItems,
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    /**
     * Map Eloquent model to domain entity
     *
     * @param UserModel $model
     * @return UserEntity
     */
    private function mapModelToEntity(UserModel $model): UserEntity
    {
        $status = new Status($model->status ?? Status::PENDING);

        return new UserEntity(
            $model->name,
            $model->email,
            $model->phone,
            $status,
            $model->id
        );
    }
}
