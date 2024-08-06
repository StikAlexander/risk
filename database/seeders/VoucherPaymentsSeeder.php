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
        $invoice1Id = DB::table('invoices')->where('invoice_number', 'FEVD310')->value('id');
        $invoice2Id = DB::table('invoices')->where('invoice_number', 'FEDVD220')->value('id');
        $invoice3Id = DB::table('invoices')->where('invoice_number', 'FEVD212')->value('id');
        $invoice4Id = DB::table('invoices')->where('invoice_number', 'FEVD313')->value('id');

        DB::table('voucher_payments')->insert([
            [
                'id' => Str::uuid(),
                'invoice_id' => $invoice1Id, // UUID del invoice 1
                'payment_date' => '2024-06-10',
                'amount' => 500.00,
                'voucher' => 'VOUCHER001',
                'epayco_ref' => 'EPAYCO001',
                'epayco_transaction_id' => 'TRANSACTION001',
                'epayco_transaction_date' => '2024-06-10 15:00:00',
                'pdf_path' => 'path/to/pdf1.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_id' => $invoice2Id, // UUID del invoice 2
                'payment_date' => '2024-06-15',
                'amount' => 1500.00,
                'voucher' => 'VOUCHER002',
                'epayco_ref' => 'EPAYCO002',
                'epayco_transaction_id' => 'TRANSACTION002',
                'epayco_transaction_date' => '2024-06-15 16:00:00',
                'pdf_path' => 'path/to/pdf2.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_id' => $invoice3Id, // UUID del invoice 3
                'payment_date' => '2024-06-20',
                'amount' => 2000.00,
                'voucher' => 'VOUCHER003',
                'epayco_ref' => 'EPAYCO003',
                'epayco_transaction_id' => 'TRANSACTION003',
                'epayco_transaction_date' => '2024-06-20 17:00:00',
                'pdf_path' => 'path/to/pdf3.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'invoice_id' => $invoice4Id, // UUID del invoice 4
                'payment_date' => '2024-06-25',
                'amount' => 2500.00,
                'voucher' => 'VOUCHER004',
                'epayco_ref' => 'EPAYCO004',
                'epayco_transaction_id' => 'TRANSACTION004',
                'epayco_transaction_date' => '2024-06-25 18:00:00',
                'pdf_path' => 'path/to/pdf4.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
