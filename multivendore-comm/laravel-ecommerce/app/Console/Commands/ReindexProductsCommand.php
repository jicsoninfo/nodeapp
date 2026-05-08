<?php
namespace App\Console\Commands;
use App\Models\Product;
use Illuminate\Console\Command;

class ReindexProductsCommand extends Command
{
    protected $signature   = 'products:reindex {--vendor= : Only reindex products for a specific vendor}';
    protected $description = 'Re-push all active products to the search index (Algolia)';

    public function handle(): int
    {
        $query = Product::active()->with('translations');

        if ($vendorId = $this->option('vendor')) {
            $query->where('vendor_id', $vendorId);
        }

        $count = $query->count();
        $this->info("Reindexing {$count} products...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(100, function ($products) use ($bar) {
            $products->searchable();
            $bar->advance($products->count());
        });

        $bar->finish();
        $this->newLine();
        $this->info('Done.');
        return self::SUCCESS;
    }
}
