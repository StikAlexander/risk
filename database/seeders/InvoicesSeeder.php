<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvoicesSeeder extends Seeder
{
    public function run()
    {
        // Obtener IDs de usuarios con roles específicos
        $superAdminId = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'super_admin')
            ->value('users.id');

        $adminId = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'admin')
            ->value('users.id');

        $collaboratorId = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'collaborator')
            ->value('users.id');

        $client1Id = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'client')
            ->skip(0)->take(1)
            ->value('users.id');

        $client2Id = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'client')
            ->skip(1)->take(1)
            ->value('users.id');

        $client3Id = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'client')
            ->skip(2)->take(1)
            ->value('users.id');

        DB::table('invoices')->insert([
            [
                'id' => Str::uuid(),
                'invoice_number' => 'FEVD310',
                'issue_date' => '2024-06-01',
                'due_date' => '2024-06-15',
                'total_amount' => 1000.00,
                'client_id' => $client1Id,
                'created_by' => $superAdminId,
                'status' => 'Pending',
                'pending_amount' => 1000.00,
                'total_paid' => 0.00,
                'epayco_ref' => null,
                'epayco_status' => null,
                'invoice_pdf' => 'invoices/FEVD310.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_number' => 'FEDVD220',
                'issue_date' => '2024-06-05',
                'due_date' => '2024-06-20',
                'total_amount' => 1500.00,
                'client_id' => $client2Id,
                'created_by' => $adminId,
                'status' => 'Partially Paid',
                'pending_amount' => 500.00,
                'total_paid' => 1000.00,
                'epayco_ref' => 'EPAYCO002',
                'epayco_status' => 'Paid',
                'invoice_pdf' => 'invoices/FEDVD220.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_number' => 'FEVD212',
                'issue_date' => '2024-06-10',
                'due_date' => '2024-06-25',
                'total_amount' => 2000.00,
                'client_id' => $client3Id,
                'created_by' => $collaboratorId,
                'status' => 'Cancelled',
                'pending_amount' => 2000.00,
                'total_paid' => 0.00,
                'epayco_ref' => 'EPAYCO003',
                'epayco_status' => 'Cancelled',
                'invoice_pdf' => 'invoices/FEVD212.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_number' => 'FEVD313',
                'issue_date' => '2024-06-15',
                'due_date' => '2024-06-30',
                'total_amount' => 2500.00,
                'client_id' => $client1Id,
                'created_by' => $superAdminId,
                'status' => 'Paid',
                'pending_amount' => 0.00,
                'total_paid' => 2500.00,
                'epayco_ref' => 'EPAYCO004',
                'epayco_status' => 'Paid',
                'invoice_pdf' => 'invoices/FEVD313.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
