<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UserHelper;
use App\Models\User;
use App\Models\Zipcode;
use App\Helpers\ZipCodeHelper;

class CronTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:test{dist?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testing cron job';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->dots ='***************************************************';
        $this->smallDots='*******';
        $this->userModel = new User();
        $this->zipHelper = new ZipcodeHelper();
        $this->userHelper = new UserHelper($this->userModel,$this->zipHelper);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->line($this->dots);
        $this->runIt();
        $this->line('completed*****');
        $this->line($this->dots);
    }

    public function runIt()
    {
        $zipcode = '48326';//$this->ask('please enter zipcode ');
        $distance = 1;//$this->ask('please enter deistance ');
        $users = $this->userHelper->getUsersInZip($zipcode,$distance);
        $count = $users->count();
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach($users as $user)
        {
           $this->performTask($user,$zipcode);
           $bar->advance();
        }
        $bar->finish();

    }

    public function performTask($user,$zip)
    {
        //sleep(1);
        \Log::info("Current user firstname is $user->fname and last name is $user->lname and distance from $zip is $user->distance");
    }
}
