<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
//use App\Traits\CacheTrait;
use App\Helpers\UserHelper;
use App\Models\User;
use App\Helpers\ZipCodeHelper;

class CronCacheClear extends Command
{
    //use CacheTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:clearCache
                            {cachename?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to clear cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $userHelper;
    public function __construct()
    {
        parent::__construct();
        $user = new User();
        $zipcodeHelper = new ZipCodeHelper();
        $this->userHelper = new UserHelper($user,$zipcodeHelper);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $cacheName = $this->argument('cachename');
        $this->clearCache($cacheName);
    }

    public function clearCache($cacheName = null)
    {
        $cacheBase = null; ///$this->userHelper->zipcodeKeyBase;
        if($cacheName){
            $this->line('<-------------Clearing cache for '.$cacheName.' ---------------->');
            $this->userHelper->cacheDelete($cacheName);
        }else if($cacheBase){
            //$this->line('what a joke');
            $this->userHelper->bulkDelete($cacheBase);
        } else{
            $this->userHelper->cacheFlushAll();
            $this->line('<-----------------clearing all cached items------------->');
        }
            
    }
}
