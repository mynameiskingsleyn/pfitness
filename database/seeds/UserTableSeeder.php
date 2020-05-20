<?php

use Illuminate\Database\Seeder;
use App\Models\User;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $zipCodes = App\Models\Zipcode::whereBetween('zip',['32000','48900'])->get();
        $this->command->info('users table seeding..');
        DB::table('users')->delete();
        $zipCodes->each(function($zipC){
            factory(User::class, 10)->create(['zipcode'=>$zipC->zip,
               'city'=>$zipC->city, 'state'=>$zipC->state ]);
        });
       // factory(User::class, 40)->create();
        $this->command->info('users table seeded..');
    }
}
