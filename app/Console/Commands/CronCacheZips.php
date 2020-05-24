<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\UserHelper;

class CronCacheZips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:CacheZips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For caching all occupied zips for search purpose';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserHelper $userHelper)
    {
        parent::__construct();
        $this->userHelper = $userHelper;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->cacheZips();
    }

    public function cacheZips()
    {
        $start = \Carbon\Carbon::now();
        $this->line('----------------Zip Caching started------------->');
        \Log::info('----------------Zip Caching started------------->');
        $occupied = $this->userHelper->getAllOcupiedzips();
        $count = count($occupied);
        $this->line("caching $count Zips");
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach($occupied as $zip){
            $this->line("");
            $this->line("    caching Inventory for zipcode $zip");
            $this->userHelper->getUsersInZip($zip,100);
            $bar->advance();
        }
        $finish = \Carbon\Carbon::now();
        $complete = $this->userHelper->timeDiff($start,$finish);
        \Log::info("--------------zipcodes caching completed---------->");
        \Log::info("---------time in miliseconds---------> $complete");
        $bar->finish();
        $this->line("--------------zipcodes caching completed---------->");
        $this->line("---------time cost for zipcodes caching in miliseconds---------> $complete");
        //$this->userHelper->createInventoryForZips($occupied);
    }

}
