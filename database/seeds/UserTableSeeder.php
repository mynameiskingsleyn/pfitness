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
        //delete users table records
        DB::table('users')->delete();
        // $this->call(UsersTableSeeder::class);
//        DB::table('users')->insert([
//            'id' => 1,
//            'name'=> 'King',
//            'email' => 'king@yahoo.com',
//            'password'=> bcrypt('password'),
//            'remember_token'=> str_random(7),
//            'created_at'=>now(),
//            'updated_at'=>now()
//        ]);

        factory(User::class, 40)->create();

        $this->command->info('users table seeded..');
    }
}