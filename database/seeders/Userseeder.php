<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
 
class UserSeeder extends Seeder
{
    public function run()
    {
        // Ensure roles exist before assigning them
        $roles = ['admin','marketing', 'sales', 'support','custom'];
 
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
 
        $users = [
            [
                'name' => 'Om',
                'last_name' => 'Limbasiya',
                'email' => 'f@gmail.com',
                'password' => Hash::make('12345678'),
                'roles' => ['marketing', 'sales', 'support'],
            ],
            [
                'name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('12345678'),
                'roles' => ['sales'],
            ],
            [
                'name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('12345678'),
                'roles' => ['support', 'marketing'],
            ],
        ];
 
        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'last_name' => $userData['last_name'],
                    'password' => $userData['password'],
                ]
            );
 
            // Assign roles to the user
            $user->assignRole($userData['roles']);
        }
    }
}