<?php

namespace App\Repositories;

use App\DTOs\PaymentDTO;
use App\Interfaces\PaymentRepoInterface;
use App\Models\Payment;

class PaymentRepo implements PaymentRepoInterface
{
    public function create(PaymentDTO $paymentDTO): Payment
    {
        return Payment::create($paymentDTO->toArray());
    }

    public function update(Payment $payment, array $data)
    {
        $payment->update($data);
        return $payment;
    }
    
}
