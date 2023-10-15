<?php

namespace App\Repositories;


class TransactionRepository extends Repository
{
    public function isUserHasTrasactions($userId, array $statusArray = []): bool  
    {
        return $this->getModel
                    ->where('user_id', $userId)
                    ->whereIn('status', $statusArray)
                    ->exists();
    }
}