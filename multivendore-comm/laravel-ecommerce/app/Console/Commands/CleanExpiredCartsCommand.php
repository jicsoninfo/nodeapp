<?php
namespace App\Console\Commands;
use App\Jobs\CleanExpiredCarts;
use Illuminate\Console\Command;

class CleanExpiredCartsCommand extends Command
{
    protected $signature   = 'carts:clean';
    protected $description = 'Delete expired guest carts from the database';

    public function handle(): int
    {
        CleanExpiredCarts::dispatch();
        $this->info('Cart cleanup dispatched.');
        return self::SUCCESS;
    }
}
