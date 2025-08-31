<?php

namespace App\Services;

use App\DTOs\MemberDTO;
use App\Interfaces\MemberRepoInterface;
use App\Models\Member;
use Carbon\Carbon;

class MemberService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MemberRepoInterface $memberRepoInterface
    )
    {
        //
    }

    public function create(MemberDTO $memberDTO): Member
    {
        return $this->memberRepoInterface->create($memberDTO);
    }

    public function update(int $member_id, $data): ?Member
    {
        $member = $this->memberRepoInterface->findById($member_id);
        if(!$member) return null;

        return $this->memberRepoInterface->update($member,$data);
    }

    public function calculatePlanExpiry(string $joinDate, string $monthsDuration)
    {
        $days = 30 * $monthsDuration - 1; //calculating days want to add
        $planExpiry = Carbon::parse($joinDate)->addDays($days)->format('Y-m-d');
        
        return $planExpiry;
    }
}
