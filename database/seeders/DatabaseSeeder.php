<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PartnerLogo;
use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ServiceJob;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tender;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        $rolesData = [
            'super_admin' => 'Akses penuh terhadap seluruh sistem.',
            'management' => 'Dashboard, KPI, monitoring, dan laporan.',
            'tender_officer' => 'Pengelolaan proses tender.',
            'service_officer' => 'Pengelolaan klien, kontrak, dan pekerjaan jasa.',
            'purchasing' => 'Pengelolaan supplier dan pengadaan.',
            'warehouse' => 'Pengelolaan persediaan dan pergerakan stok.',
            'sales' => 'Pengelolaan penjualan dan transaksi perdagangan.',
            'finance' => 'Pengelolaan invoice, tagihan, dan pembayaran.',
        ];

        $rolesMap = [];
        foreach ($rolesData as $name => $description) {
            $role = Role::firstOrCreate(['name' => $name], ['description' => $description]);
            $rolesMap[$name] = $role->id;
        }

        // 2. Seed Users
        $adminUser = User::firstOrCreate(
            ['email' => 'superadmin@signalpanca.co.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role_id' => $rolesMap['super_admin'],
                'is_active' => true,
            ]
        );

        foreach ($rolesMap as $roleName => $roleId) {
            if ($roleName === 'super_admin') continue;
            User::firstOrCreate(
                ['email' => $roleName . '@signalpanca.co.id'],
                [
                    'name' => ucwords(str_replace('_', ' ', $roleName)) . ' SPU',
                    'password' => Hash::make('password'),
                    'role_id' => $roleId,
                    'is_active' => true,
                ]
            );
        }

        // 3. Seed Master Clients
        $clients = [
            ['code' => 'CLI-001', 'name' => 'Dinas Komunikasi & Informatika', 'company_name' => 'Diskominfo Pemprov DKI', 'phone' => '021-3456789', 'email' => 'pengadaan@diskominfo.go.id', 'address' => 'Jl. Medan Merdeka Selatan No. 8, Jakarta Central'],
            ['code' => 'CLI-002', 'name' => 'PT Bank Mandiri (Persero) Tbk', 'company_name' => 'Procurement Div. Mandiri', 'phone' => '021-5268000', 'email' => 'procurement@bankmandiri.co.id', 'address' => 'Plaza Mandiri, Jl. Jend. Gatot Subroto Kav. 36'],
            ['code' => 'CLI-003', 'name' => 'Kementerian Perhubungan RI', 'company_name' => 'Biro Pelayanan Pengadaan', 'phone' => '021-3811308', 'email' => 'lpse@dephub.go.id', 'address' => 'Jl. Medan Merdeka Barat No. 8, Jakarta'],
        ];

        $createdClients = [];
        foreach ($clients as $c) {
            $createdClients[] = Client::firstOrCreate(['code' => $c['code']], $c);
        }

        // 4. Seed Master Suppliers
        $suppliers = [
            ['code' => 'SUP-001', 'name' => 'PT Asus Indonesia Utama', 'phone' => '021-29922233', 'email' => 'distributor@asus.co.id', 'address' => 'Kawasan Industri Jababeka 2, Cikarang'],
            ['code' => 'SUP-002', 'name' => 'PT Seiko Epson Indonesia', 'phone' => '021-5728200', 'email' => 'sales@epson.co.id', 'address' => 'Wisma Keiai Lt. 16, Jl. Jend Sudirman'],
            ['code' => 'SUP-003', 'name' => 'PT Paperone Nusantara', 'phone' => '021-8971234', 'email' => 'supply@paperone.co.id', 'address' => 'Jl. Industri Pulogadung No. 45, Jakarta'],
        ];

        $createdSuppliers = [];
        foreach ($suppliers as $s) {
            $createdSuppliers[] = Supplier::firstOrCreate(['code' => $s['code']], $s);
        }

        // 5. Seed Products
        $products = [
            ['sku' => 'LAP-ASUS-I7', 'name' => 'Laptop Asus ExpertBook Core i7 16GB', 'category' => 'Komputer', 'unit' => 'Unit', 'purchase_price' => 12500000, 'selling_price' => 14800000, 'minimum_stock' => 5],
            ['sku' => 'PRN-EPS-L3210', 'name' => 'Printer Epson EcoTank L3210 All-in-One', 'category' => 'Printer', 'unit' => 'Unit', 'purchase_price' => 2100000, 'selling_price' => 2500000, 'minimum_stock' => 3],
            ['sku' => 'KRT-A4-80G', 'name' => 'Kertas PaperOne A4 80gr Box (5 Ream)', 'category' => 'ATK', 'unit' => 'Box', 'purchase_price' => 210000, 'selling_price' => 255000, 'minimum_stock' => 20],
        ];

        $createdProducts = [];
        foreach ($products as $p) {
            $prod = Product::firstOrCreate(['sku' => $p['sku']], $p);
            $createdProducts[] = $prod;

            // Initial Stock Movement
            StockMovement::firstOrCreate(
                ['product_id' => $prod->id, 'reference_type' => 'Initial Stock'],
                [
                    'movement_type' => 'IN',
                    'quantity' => 15,
                    'movement_date' => now(),
                    'notes' => 'Stok awal persediaan',
                    'created_by' => $adminUser->id,
                ]
            );
        }

        // 6. Seed Tenders
        $tenders = [
            [
                'tender_number' => 'TDR-2026-001',
                'client_id' => $createdClients[0]->id,
                'name' => 'Pengadaan Laptops & IT Equipment Diskominfo',
                'source' => 'LPSE DKI Jakarta',
                'found_date' => '2026-08-01',
                'deadline' => '2026-09-25',
                'estimated_value' => 750000000,
                'bid_value' => 715000000,
                'status' => 'Menang',
                'result' => 'Menang',
                'created_by' => $adminUser->id,
            ],
            [
                'tender_number' => 'TDR-2026-002',
                'client_id' => $createdClients[1]->id,
                'name' => 'Jasa Pemeliharaan Data Center Bank Mandiri',
                'source' => 'e-Procurement Mandiri',
                'found_date' => '2026-08-15',
                'deadline' => '2026-09-18',
                'estimated_value' => 1200000000,
                'bid_value' => 1150000000,
                'status' => 'Evaluasi',
                'result' => null,
                'created_by' => $adminUser->id,
            ],
        ];

        $createdTenders = [];
        foreach ($tenders as $t) {
            $createdTenders[] = Tender::firstOrCreate(['tender_number' => $t['tender_number']], $t);
        }

        // 7. Seed Contract from Won Tender
        $contract = Contract::firstOrCreate(
            ['contract_number' => 'CTR-2026-001'],
            [
                'tender_id' => $createdTenders[0]->id,
                'client_id' => $createdClients[0]->id,
                'start_date' => '2026-09-01',
                'end_date' => '2027-08-31',
                'contract_value' => 715000000,
                'fee_percentage' => 5.0,
                'fee_amount' => 35750000,
                'status' => 'Aktif',
                'notes' => 'Kontrak pengadaan & pemeliharaan laptop Diskominfo',
                'created_by' => $adminUser->id,
            ]
        );

        // 8. Seed Service Job
        $serviceJob = ServiceJob::firstOrCreate(
            ['job_number' => 'JOB-2026-001'],
            [
                'contract_id' => $contract->id,
                'name' => 'Instalasi OS & Deployment Laptops Diskominfo',
                'start_date' => '2026-09-05',
                'end_date' => '2026-09-30',
                'status' => 'Dalam Pelaksanaan',
                'progress' => 60,
                'notes' => 'Tahap instalasi software lisensi',
            ]
        );

        // 9. Seed Procurement
        $procurement = Procurement::firstOrCreate(
            ['procurement_number' => 'PRC-20260901-001'],
            [
                'supplier_id' => $createdSuppliers[0]->id,
                'tender_id' => $createdTenders[0]->id,
                'procurement_date' => '2026-09-02',
                'total_amount' => 125000000,
                'status' => 'Received',
                'notes' => 'Pengadaan 10 unit laptop Asus',
                'created_by' => $adminUser->id,
            ]
        );

        ProcurementItem::firstOrCreate(
            ['procurement_id' => $procurement->id, 'product_id' => $createdProducts[0]->id],
            [
                'quantity' => 10,
                'price' => 12500000,
                'subtotal' => 125000000,
            ]
        );

        StockMovement::firstOrCreate(
            ['reference_type' => 'Procurement', 'reference_id' => $procurement->id],
            [
                'product_id' => $createdProducts[0]->id,
                'movement_type' => 'IN',
                'quantity' => 10,
                'movement_date' => now(),
                'notes' => 'Pengadaan ' . $procurement->procurement_number,
                'created_by' => $adminUser->id,
            ]
        );

        // 10. Seed Sale & Invoice & Payment
        $sale = Sale::firstOrCreate(
            ['sale_number' => 'SLS-20260905-001'],
            [
                'customer_name' => 'PT Mandiri Solusindo',
                'customer_phone' => '0811998877',
                'sale_date' => '2026-09-05',
                'total_amount' => 29600000,
                'status' => 'Completed',
                'notes' => 'Penjualan 2 unit laptop',
                'created_by' => $adminUser->id,
            ]
        );

        SaleItem::firstOrCreate(
            ['sale_id' => $sale->id, 'product_id' => $createdProducts[0]->id],
            [
                'quantity' => 2,
                'price' => 14800000,
                'subtotal' => 29600000,
            ]
        );

        StockMovement::firstOrCreate(
            ['reference_type' => 'Sale', 'reference_id' => $sale->id],
            [
                'product_id' => $createdProducts[0]->id,
                'movement_type' => 'OUT',
                'quantity' => 2,
                'movement_date' => now(),
                'notes' => 'Penjualan ' . $sale->sale_number,
                'created_by' => $adminUser->id,
            ]
        );

        $invoice = Invoice::firstOrCreate(
            ['invoice_number' => 'INV-SLS-20260905-001'],
            [
                'sale_id' => $sale->id,
                'invoice_date' => '2026-09-05',
                'due_date' => '2026-10-05',
                'subtotal' => 29600000,
                'tax_amount' => 3256000,
                'total_amount' => 32856000,
                'paid_amount' => 32856000,
                'status' => 'Paid',
                'notes' => 'Invoice lunas untuk Penjualan SLS-20260905-001',
                'created_by' => $adminUser->id,
            ]
        );

        Payment::firstOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'payment_date' => '2026-09-06',
                'amount' => 32856000,
                'payment_method' => 'Transfer Bank BCA',
                'reference_number' => 'TRX-BCA-99881122',
                'notes' => 'Pelunasan invoice penjualan',
                'created_by' => $adminUser->id,
            ]
        );

        // Additional Unpaid Invoice for Service Job
        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-SVC-20260908-001'],
            [
                'service_job_id' => $serviceJob->id,
                'invoice_date' => '2026-09-08',
                'due_date' => '2026-10-08',
                'subtotal' => 150000000,
                'tax_amount' => 16500000,
                'total_amount' => 166500000,
                'paid_amount' => 50000000,
                'status' => 'Partial',
                'notes' => 'Tagihan Termin 1 Pekerjaan Jasa Diskominfo',
                'created_by' => $adminUser->id,
            ]
        );

        // 11. Seed Partner Logos for Landing Page Marquee
        $partnerLogos = [
            ['name' => 'Hutama Karya', 'category' => 'BUMN Karya', 'badge_text' => 'HK', 'badge_color' => '#1e3a8a', 'sort_order' => 1],
            ['name' => 'Brantas Abipraya', 'category' => 'BUMN Karya', 'badge_text' => 'ABIPRAYA', 'badge_color' => '#0284c7', 'sort_order' => 2],
            ['name' => 'PT PP (Persero) Tbk', 'category' => 'BUMN Konstruksi', 'badge_text' => 'PT PP', 'badge_color' => '#15803d', 'sort_order' => 3],
            ['name' => 'Nindya Karya', 'category' => 'BUMN Infrastruktur', 'badge_text' => 'NINDYA', 'badge_color' => '#b45309', 'sort_order' => 4],
            ['name' => 'CIMB Niaga', 'category' => 'Perbankan & Keuangan', 'badge_text' => 'CIMB NIAGA', 'badge_color' => '#b91c1c', 'sort_order' => 5],
            ['name' => 'PT Nusa Halmahera Minerals', 'category' => 'Pertambangan & Energi', 'badge_text' => 'NHM', 'badge_color' => '#4d7c0f', 'sort_order' => 6],
            ['name' => 'PT Danareksa (Persero)', 'category' => 'Jasa Keuangan BUMN', 'badge_text' => 'DANAREKSA', 'badge_color' => '#4338ca', 'sort_order' => 7],
            ['name' => 'Diskominfo Pemprov DKI', 'category' => 'Pemerintahan', 'badge_text' => 'DISKOMINFO DKI', 'badge_color' => '#0f766e', 'sort_order' => 8],
            ['name' => 'PT Bank Mandiri (Persero) Tbk', 'category' => 'Perbankan BUMN', 'badge_text' => 'BANK MANDIRI', 'badge_color' => '#1d4ed8', 'sort_order' => 9],
        ];

        foreach ($partnerLogos as $pl) {
            PartnerLogo::firstOrCreate(
                ['name' => $pl['name']],
                [
                    'category' => $pl['category'],
                    'badge_text' => $pl['badge_text'],
                    'badge_color' => $pl['badge_color'],
                    'is_active' => true,
                    'sort_order' => $pl['sort_order'],
                ]
            );
        }
    }
}
