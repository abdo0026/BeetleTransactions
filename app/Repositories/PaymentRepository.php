<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository extends Repository
{
    public function getTransactionLatestPayment($transactionId): Payment
    {
        return $this->getModel
            ->where('transaction_id', $transactionId)
            ->orderBy('created_at', 'desc')
            ->first();
    }


}