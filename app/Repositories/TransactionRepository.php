<?php

namespace App\Repositories;

use App\Enum\TRANSACTION_STATUS;
use Carbon\Carbon;

class TransactionRepository extends Repository
{
    public function isUserHasTrasactions($userId, array $statusArray = []): bool  
    {
        return $this->getModel
                    ->where('user_id', $userId)
                    ->whereIn('status', $statusArray)
                    ->exists();
    }



    public function UpdateToDueTransactions()
    {   
        $today = Carbon::now()->toDateString();
        $openTransactionsArray = [TRANSACTION_STATUS::OUTSTANDING->value];

        return $this->getModel
              ->where('due_date', '<', $today)    
              ->whereIn('status', $openTransactionsArray)
               ->update(['status' => TRANSACTION_STATUS::OVERDUE->value]);
    }


}