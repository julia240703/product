<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
  
class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
               'name'=>'Admin',
               'email'=>'admin@mail.com',
               'type' => 1,
               'password'=> bcrypt('123456'),
            ],

        ];
    
        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}