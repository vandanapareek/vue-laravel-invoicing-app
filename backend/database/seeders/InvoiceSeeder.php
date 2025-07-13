<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/data.json');
        $data = json_decode(file_get_contents($jsonPath), true);
        foreach ($data as $item) {
            // Only keep fields that exist in the invoices table
            $invoiceData = [
                'id' => $item['id'],
                'clientName' => $item['clientName'],
                'clientEmail' => $item['clientEmail'] ?? null,
                'description' => $item['description'] ?? null,
                'paymentTerms' => (string) $item['paymentTerms'],
                'createdAt' => $item['createdAt'],
                'paymentDue' => $item['paymentDue'],
                'items' => json_encode($item['items']),
                'status' => $item['status'],
                'total' => $item['total'],
                'senderAddress' => $item['senderAddress'] ?? null,
                'clientAddress' => $item['clientAddress'] ?? null,
            ];
            Invoice::create($invoiceData);
        }
    }
}
