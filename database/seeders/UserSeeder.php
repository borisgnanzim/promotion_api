<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Models\UserRole;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin
        $admin = User::factory()->create([
            'name' => 'Administrateur',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
            
        ]);

        $roleAdmin = Role::where('name', 'admin')->first();
        UserRole::create([
            'user_ref' => $admin->ref,
            'role_ref' => $roleAdmin->ref,
            'start_at' => now(),
            'is_active' => true,
        ]);

        // seller
        $seller = User::factory()->create([
            'name' => 'seller',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'store_ref' => Store::inRandomOrder()->first()->ref,
            'is_active' => true,
        ]);
        $roleSeller = Role::where('name', 'seller')->first();  
        UserRole::create([
            'user_ref' => $seller->ref,
            'role_ref' => $roleSeller->ref,
            'start_at' => now(),
            'is_active' => true,
        ]);

        // candidat
        $candidat = User::factory()->create([
            'name' => 'candidat',
            'email' => 'candidat@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $roleCandidat = Role::where('name', 'candidat')->first();  
        UserRole::create([
            'user_ref' => $candidat->ref,
            'role_ref' => $roleCandidat->ref,
            'start_at' => now(),
            'is_active' => true,
        ]);

        // client
        $client = User::factory()->create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $roleClient = Role::where('name', 'client')->first();
        UserRole::create([
            'user_ref' => $client->ref,
            'role_ref' => $roleClient->ref,
            'start_at' => now(),
            'is_active' => true,
        ]);
        
        // Create other users with specific roles
        $this->createUsersWithRole('admin', 3); 
        $this->createUsersWithRole('seller', 6);
        $this->createUsersWithRole('candidat', 4);
        $this->createUsersWithRole('client', 10);



    }

    private function createUsersWithRole(string $roleName, int $count)
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            $this->command->error("Role '{$roleName}' not found.");
            return;
        }

        User::factory()->count($count)->create()->each(function ($user) use ($role) {
            UserRole::create([
                'user_ref' => $user->ref,
                'role_ref' => $role->ref,
                'start_at' => now(),
                'is_active' => true,
            ]);
        });
    }
}
