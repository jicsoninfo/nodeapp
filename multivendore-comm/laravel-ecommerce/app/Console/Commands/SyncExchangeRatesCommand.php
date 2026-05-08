<?php
namespace App\Console\Commands;
use App\Jobs\SyncExchangeRates;
use Illuminate\Console\Command;

class SyncExchangeRatesCommand extends Command
{
    protected $signature   = 'rates:sync';
    protected $description = 'Fetch and store the latest currency exchange rates';

    public function handle(): int
    {
        SyncExchangeRates::dispatch();
        $this->info('Exchange rate sync dispatched.');
        return self::SUCCESS;
    }
}
