<?php

namespace App\DomainData;

use App\Enum\TRANSACTION_STATUS;

trait PaymentDto {

    public function getRules(array $fields = []): array {
        $data = [
            'transaction_id' => 'required|numeric',
            'user_id' => 'numeric',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'date',
            'details' => 'string'
        ];

       if(sizeof($fields) == 0) return $data;
       return array_intersect_key($data, array_flip($fields));
    }


    public function initializePaymentDto(): void {
        $this->fillable = array_keys($this->getRules());
    }
}
