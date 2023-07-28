<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Akhilesh\Neodash\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $user = User::factory()->create([
            'name'              =>  'Admin',
            'mobile'            =>  '7079582411',
            'email'             =>  'neodash@gmail.com',
            'email_verified_at' =>  date('Y-m-d H:i:s'),
            'password'          =>  Hash::make('password'),
        ]);
        $userRole = Role::firstOrCreate(['name' => 'admin']);
        $user->roles()->sync([$userRole->id]);

        User::factory(10)->create();
    }
}
