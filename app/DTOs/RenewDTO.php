<?php

namespace App\DTOs;

readonly class RenewDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $member_id,
        public int $plan_id,
        public string $renew_from,
        public string $payment_method,
        public float $recieved_amount,
    )
    {}

    public static function fromArray(array $data): self
    {
        return new self(
            member_id: $data['member_id'],
            plan_id: $data['plan_id'],
            renew_from: $data['renew_from'],
            payment_method: $data['payment_method'],
            recieved_amount: $data['recieved_amount']
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
