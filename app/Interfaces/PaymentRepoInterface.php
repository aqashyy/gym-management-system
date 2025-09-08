<?php

namespace App\Interfaces;

use App\DTOs\PaymentDTO;
use App\Models\Payment;

interface PaymentRepoInterface
{
    public function create(PaymentDTO $paymentDTO): Payment;
}
