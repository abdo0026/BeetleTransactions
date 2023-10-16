<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

Use App\Repositories\Repository;

class OverDueTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change status for all transaction that passed its due date to over due';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Repository::getRepository('Transaction')->UpdateToDueTransactions();
        
        return Command::SUCCESS;
    }
}
