<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Definir los roles
        $roles = [
            'super_admin' => 'stikadmin@gmail.com',
            'admin' => 'admin@gmail.com',
            'collaborator' => $faker->unique()->safeEmail,
            'client' => $faker->unique()->safeEmail
        ];
        
        // Crear un array para almacenar los IDs de los usuarios
        $userIds = [];

        // Crear usuarios para cada rol
        foreach ($roles as $role => $email) {
            $userId = Str::uuid();
            $userIds[$role] = $userId; // Guardar el UUID en el array correspondiente al rol

            DB::table('users')->insert([
                'id' => $userId,
                'document_number' => $faker->unique()->numerify('##########'),
                'name' => $faker->name,
                'email' => $email,
                'phone' => $faker->phoneNumber,
                'email_verified_at' => now(),
                'password' => Hash::make('123'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'document_type_id' => $faker->numberBetween(1, 6),
                'status' => 'active',
            ]);

            DB::table('model_has_roles')->insert([
                'role_id' => DB::table('roles')->where('name', $role)->value('id'),
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);
        }

        // Crear usuarios adicionales para clientes
        for ($i = 0; $i < 3; $i++) {
            $userId = Str::uuid();
            $userIds['client' . ($i + 1)] = $userId; // Guardar el UUID en el array correspondiente al cliente
            
            DB::table('users')->insert([
                'id' => $userId,
                'document_number' => $faker->unique()->numerify('##########'),
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'email_verified_at' => now(),
                'password' => Hash::make('123'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'document_type_id' => $faker->numberBetween(1, 6),
                'status' => 'active',
            ]);

            DB::table('model_has_roles')->insert([
                'role_id' => DB::table('roles')->where('name', 'client')->value('id'),
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);
        }
    }
}
