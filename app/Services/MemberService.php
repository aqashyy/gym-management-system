<?php

namespace App\Services;

use App\DTOs\MemberDTO;
use App\DTOs\PaymentDTO;
use App\Interfaces\MemberRepoInterface;
use App\Interfaces\PaymentRepoInterface;
use App\Interfaces\PlanRepoInterface;
use App\Models\Member;
use Carbon\Carbon;

class MemberService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private MemberRepoInterface $memberRepoInterface,
        private PlanRepoInterface $planRepoInterface,
        private PaymentRepoInterface $paymentRepoInterface
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
        $days = 30 * $monthsDuration; //calculating days want to add
        $planExpiry = Carbon::parse($joinDate)->addDays($days)->format('Y-m-d');

        return $planExpiry;
    }

    public function isPlanExpired(int $member_id): bool
    {
        $member = Member::find($member_id);
        if($member->plan_expiry < now())
        {
            return true;
        }
        return false;
    }

    public function renewNow(Member $member, int $plan_id, $renewFrom, string $payment_method): bool
    {
        $plan = $this->planRepoInterface->findById($plan_id);

        if($plan != null) {

            $newExpiry = $this->calculatePlanExpiry($renewFrom, $plan->duration_months);
            $member->plan_expiry = $newExpiry;
            $member->save();

            // insert payment info
            $this->paymentRepoInterface->create(PaymentDTO::fromArray([
                'member_id' =>  $member->id,
                'amount'    =>  $plan->price,
                'paid_on'   =>  now(),
                'valid_until'   =>  $newExpiry,
                'method'        =>  $payment_method
            ]));

            return true;
        }
        return false;
    }
}
