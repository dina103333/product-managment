<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findById($id): ?User;
    public function findByEmail(string $email): ?User;
    public function update(User $user, array $data): bool;
    public function delete(User $user): bool;
}