<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Interfaces\UserRepoInterface;
use App\Models\User;

class CustomerService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepoInterface $userRepoInterface
    )
    {
        //
    }

    public function createUser(UserDTO $userDTO): User
    {
        return $this->userRepoInterface->create($userDTO);
    }
}
