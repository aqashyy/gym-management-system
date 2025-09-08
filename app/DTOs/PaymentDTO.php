<?php

namespace App\DTOs;

class PaymentDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $member_id,
        public float $amount,
        public string $paid_on,
        public string $valid_until,
        public string $method,
    )
    {
        //
    }

    public static function fromArray(array $data): self
    {
        return new self(
            member_id: $data['member_id'],
            amount: $data['amount'],
            paid_on: $data['paid_on'],
            valid_until: $data['valid_until'],
            method: $data['method']
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
