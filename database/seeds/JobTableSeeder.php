<?php

use Illuminate\Database\Seeder;

use App\Models\Job;

class JobTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $jobs =[
            'fitness'=>['title'=>'Fitness Instructor','description'=>'Assist with fitness goals'],
            'coach'=>['title'=>'Life Coach','description'=>'Coaching']
        ];
        $this->command->info('users table seeding..');
        foreach($jobs as $key=> $aJob){
            factory(Job::class,10)->create(['title'=>$aJob['title'],'desciption'=>$aJob['description']]);
        }

    }
}
