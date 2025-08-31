<?php

namespace App\Interfaces;

use App\DTOs\MemberDTO;
use App\Models\Member;

interface MemberRepoInterface
{
    public function findById(int $id): ?Member;
    
    public function create(MemberDTO $memberDTO): Member;

    public function update(Member $member, array $data): Member;

    public function delete(Member $member): void;
}
