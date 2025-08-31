<?php

namespace App\Repositories;

use App\DTOs\MemberDTO;
use App\Interfaces\MemberRepoInterface;
use App\Models\Member;

class MemberRepo implements MemberRepoInterface
{
    public function findById(int $id): ?Member
    {
        return Member::find($id);
    }
    public function create(MemberDTO $memberDTO): Member
    {
        return Member::create($memberDTO->toArray());
    }

    public function update(Member $member, array $data): Member
    {
        $member->update($data);
        return $member;
    }

    public function delete(Member $member): void
    {
        $member->delete();
    }
}
