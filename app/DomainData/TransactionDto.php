<?php

namespace App\DomainData;

use App\Enum\TRANSACTION_STATUS;

trait TransactionDto {

    public function getRules(array $fields = []): array {
        $data = [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'sub_category_id' => 'nullable|numeric',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'numeric|min:0',
            'due_date' => 'required|date',
            'vat_percentage' => 'required|integer|min:0|max:100',
            'is_vat_included' => 'required|boolean',
            'status' =>  'in:' . implode(',', array_column(TRANSACTION_STATUS::cases(), 'value')),
            'admin_id' => 'numeric',
        ];

       if(sizeof($fields) == 0) return $data;
       return array_intersect_key($data, array_flip($fields));
    }


    public function initializeTransactionDto(): void {
        $this->fillable = array_keys($this->getRules());
    }
}
