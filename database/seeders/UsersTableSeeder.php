<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Definir los usuarios con sus datos
        $users = [
            [
                'name' => 'Stik Gamboa',
                'email' => 'stikadmin@gmail.com',
                'phone' => '3109876543',
                'document_number' => '1007703151',
                'role' => 'super_admin',
                'document_type_id' => DB::table('document_types')->where('name', 'CC')->value('id'), // Cédula de Ciudadanía
            ],
            [
                'name' => 'Angie Gamboa',
                'email' => 'angieadmin@gmail.com',
                'phone' => '3112345678',
                'document_number' => '1000323439',
                'role' => 'admin',
                'document_type_id' => DB::table('document_types')->where('name', 'CC')->value('id'), // Cédula de Ciudadanía
            ],
            [
                'name' => 'Beatriz Aponte',
                'email' => 'beatrizadmin@gmail.com',
                'phone' => '31436688',
                'document_number' => '31436688',
                'role' => 'admin',
                'document_type_id' => DB::table('document_types')->where('name', 'CC')->value('id'), // Cédula de Ciudadanía
            ],
            [
                'name' => 'Paulina Rubio',
                'email' => 'paulinarubio@example.com',
                'phone' => '3123456789',
                'document_number' => '52993449',
                'role' => 'collaborator',
                'document_type_id' => DB::table('document_types')->where('name', 'CC')->value('id'), // Cédula de Ciudadanía
            ],
            [
                'name' => 'Solangi Perez',
                'email' => 'solangiperez@example.com',
                'phone' => '3134567890',
                'document_number' => '1012456789',
                'role' => 'collaborator',
                'document_type_id' => DB::table('document_types')->where('name', 'CC')->value('id'), // Cédula de Ciudadanía
            ],
            [
                'name' => 'John Gamboa',
                'email' => 'johngamboa@example.com',
                'phone' => '3145678901',
                'document_number' => '79766214',
                'role' => 'collaborator',
                'document_type_id' => DB::table('document_types')->where('name', 'CC')->value('id'), // Cédula de Ciudadanía
            ],
            [
                'name' => 'Edificio Gran Reserva Toscana',
                'email' => 'granreservatoscana@example.com',
                'phone' => '3156789012',
                'document_number' => '900368989',
                'role' => 'client',
                'document_type_id' => DB::table('document_types')->where('name', 'NIT')->value('id'), // Número de Identificación Tributaria
            ],
            [
                'name' => 'Edificio Belmira',
                'email' => 'edificiobelmira@example.com',
                'phone' => '3167890123',
                'document_number' => '901042203',
                'role' => 'client',
                'document_type_id' => DB::table('document_types')->where('name', 'NIT')->value('id'), // Número de Identificación Tributaria
            ],
            [
                'name' => 'Claudia Janet',
                'email' => 'claudiajanet@example.com',
                'phone' => '3178901234',
                'document_number' => '1004987654',
                'role' => 'client',
                'document_type_id' => DB::table('document_types')->where('name', 'CC')->value('id'), // Cédula de Ciudadanía
            ],
            [
                'name' => 'Cristian Rodriguez',
                'email' => 'cristianrodriguez@example.com',
                'phone' => '5741234',
                'document_number' => '32014567',
                'role' => 'client',
                'document_type_id' => DB::table('document_types')->where('name', 'CC')->value('id'), // Cédula de Ciudadanía
            ],
            [
                'name' => 'Conjunto Residencial Souls',
                'email' => 'residencialsouls@example.com',
                'phone' => '5735678',
                'document_number' => '900235124',
                'role' => 'client',
                'document_type_id' => DB::table('document_types')->where('name', 'NIT')->value('id'), // Número de Identificación Tributaria
            ],
        ];

        // Crear un array para almacenar los IDs de los usuarios por rol
        $userIds = [];

        // Crear usuarios y asignar roles
        foreach ($users as $user) {
            $userId = Str::uuid();

            // Determinar el created_by_id basado en la jerarquía
            $createdById = null;
            switch ($user['role']) {
                case 'admin':
                    $createdById = $userIds['super_admin'];
                    break;
                case 'collaborator':
                    $createdById = $userIds['admin'];
                    break;
                case 'client':
                    $createdById = $userIds['collaborator'];
                    break;
                default:
                    $createdById = $userId; // El super_admin se crea a sí mismo
                    break;
            }

            DB::table('users')->insert([
                'id' => $userId,
                'document_number' => $user['document_number'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'email_verified_at' => now(),
                'password' => Hash::make('123'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'document_type_id' => $user['document_type_id'], // Tipo de documento según el rol
                'status' => 'active',
                'created_by_id' => $createdById, // Asignar created_by_id basado en la jerarquía
            ]);

            DB::table('model_has_roles')->insert([
                'role_id' => DB::table('roles')->where('name', $user['role'])->value('id'),
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);

            // Asignar rol adicional de panel_user
            DB::table('model_has_roles')->insert([
                'role_id' => DB::table('roles')->where('name', 'panel_user')->value('id'),
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);

            // Guardar el ID del usuario en el array correspondiente al rol
            $userIds[$user['role']] = $userId;
        }
    }
}