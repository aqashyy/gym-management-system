<?php

namespace App\Interfaces;

use App\DTOs\UserDTO;
use App\Models\User;

interface UserRepoInterface
{
    public function create(UserDTO $userDTO): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): void;
}
