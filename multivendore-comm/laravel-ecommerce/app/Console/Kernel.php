<?php
namespace App\Console;
use App\Console\Commands\CleanExpiredCartsCommand;
use App\Console\Commands\ProcessMonthlyPayouts;
use App\Console\Commands\SyncExchangeRatesCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Sync exchange rates every hour
        $schedule->command(SyncExchangeRatesCommand::class)
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Clean expired guest carts daily at 3 AM
        $schedule->command(CleanExpiredCartsCommand::class)
            ->dailyAt('03:00')
            ->withoutOverlapping();

        // Monthly payouts on the 1st at 9 AM UTC
        $schedule->command(ProcessMonthlyPayouts::class)
            ->monthlyOn(1, '09:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure(config('mail.from.address'));

        // Prune Telescope entries (dev/staging)
        $schedule->command('telescope:prune --hours=48')
            ->daily()
            ->environments(['local', 'staging']);

        // Prune activity log older than 90 days
        $schedule->command('activitylog:clean --days=90')
            ->weekly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
