<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherPaymentsSeeder extends Seeder
{
    public function run()
    {
        // Obtener IDs de las facturas creadas
        $invoice1Id = DB::table('invoices')->where('invoice_number', '310')->value('id');
        $invoice2Id = DB::table('invoices')->where('invoice_number', '220')->value('id');
        $invoice3Id = DB::table('invoices')->where('invoice_number', '212')->value('id');
        $invoice4Id = DB::table('invoices')->where('invoice_number', '313')->value('id');

        DB::table('voucher_payments')->insert([
            [
                'id' => Str::uuid(),
                'invoice_id' => $invoice1Id, // UUID del invoice 1
                'payment_date' => '2024-06-10',
                'amount' => 500.00,
                'payment_support' => 'SP001',
                'payment_support' => 'invoice/to/pdf1.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_id' => $invoice2Id, // UUID del invoice 2
                'payment_date' => '2024-06-15',
                'amount' => 1500.00,
                'payment_support' => 'SP002',
                'payment_support' => 'invoice/to/pdf2.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_id' => $invoice3Id, // UUID del invoice 3
                'payment_date' => '2024-06-20',
                'amount' => 2000.00,
                'payment_support' => 'SP003',
                'payment_support' => 'invoice/to/pdf3.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_id' => $invoice4Id, // UUID del invoice 4
                'payment_date' => '2024-06-25',
                'amount' => 2500.00,
                'payment_support' => 'SP004',
                'payment_support' => 'invoice/to/pdf4.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}