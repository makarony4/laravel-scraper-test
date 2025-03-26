<?php

namespace App\Console\Commands;

use App\Service\CrawlManager;
use Illuminate\Console\Command;

class UpdateArticles extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start update articles';

    /**
     * Execute the console command.
     */
    public function handle(CrawlManager $crawlManager)
    {
        $response = $crawlManager->checkForNewArticles();

        isset($response['error']) ? $this->error($response['error']) : $this->info($response['info']);
    }
}
