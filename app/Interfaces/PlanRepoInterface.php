<?php

namespace App\Interfaces;

use App\Models\Plan;

interface PlanRepoInterface
{
    public function findById(int $id): ?Plan;
}
