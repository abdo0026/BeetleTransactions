<?php

namespace App\Listeners\Transactions;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Transactions\CrudServices\TransactionCrudService;

class CalculateTransactionPaidAmount
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private TransactionCrudService $transactionCrudService
    )
    {}

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $output = new \stdClass;

        $data = [
            'id' => $event->transactionId,
            'related_objects_count' => [],
            'related_objects' => []
        ];

        $this->transactionCrudService->getById($data, $output);
        
        $this->transactionCrudService->reCalculateTransactionPaidAmount($output->transaction);
    }
}
