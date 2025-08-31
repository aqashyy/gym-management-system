<?php

namespace App\Repositories;

use App\DTOs\UserDTO;
use App\Interfaces\UserRepoInterface;
use App\Models\User;

class UserRepo implements UserRepoInterface
{

    public function create(UserDTO $userDTO): User
    {
        return User::create($userDTO->toArray());
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
