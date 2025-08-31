<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Date;

readonly class MemberDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $customer_id,
        public int $plan_id,
        public string $name,
        public Date $dob,
        public int $phone,
        public ?string $blood_group,
        public float $weight,
        public float $height,
        public Date $joining_date,
        public ?string $photo,
        public int $fingerprint_id,
        public ?int $is_staff,
        public Date $plan_expiry

    )
    {}

    public static function fromArray(array $data): self
    {
        return new self(
            customer_id: $data["customer_id"],
            plan_id: $data['plan_id'],
            name: $data['name'],
            dob: $data['dob'],
            phone: $data['phone'],
            blood_group: $data['blood_group'] ?? null,
            weight: $data['weight'],
            height: $data['height'],
            joining_date: $data['joining_date'],
            photo: $data['photo'] ?? null,
            fingerprint_id: $data['fingerprint_id'],
            is_staff: $data['is_staff'] ?? 0,
            plan_expiry: $data['plan_expiry']
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
