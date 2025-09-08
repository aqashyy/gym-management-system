<?php

namespace App\Repositories;

use App\Interfaces\PlanRepoInterface;
use App\Models\Plan;

class PlanRepo implements PlanRepoInterface
{
    public function findById(int $id): Plan|null
    {
        return Plan::find($id);
    }
}
