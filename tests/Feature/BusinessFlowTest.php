<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\SalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_quotation_can_be_created(): void
    {
        [$user, $client] = $this->foundation();

        $response = $this->actingAs($user)->post(route('commercial.service.store'), [
            'client_id' => $client->id, 'quotation_number' => 'QTN-TEST-001', 'quotation_date' => '2026-09-13',
            'status' => 'Draft', 'items' => [['description' => 'Maintenance server', 'quantity' => 2, 'price' => 1000000]],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_quotations', ['quotation_number' => 'QTN-TEST-001', 'total_amount' => 2220000]);
    }

    public function test_sale_rejects_insufficient_stock(): void
    {
        [$user] = $this->foundation();
        $product = Product::create(['sku' => 'SKU-TEST', 'name' => 'Produk Test', 'unit' => 'Unit', 'purchase_price' => 10, 'selling_price' => 20, 'minimum_stock' => 1, 'is_active' => true]);

        $this->expectException(\Exception::class);
        app(SalesService::class)->createSale(['customer_name' => 'Pelanggan', 'sale_date' => '2026-09-13'], [['product_id' => $product->id, 'quantity' => 2, 'price' => 20]], $user->id);
    }

    public function test_payment_cannot_exceed_invoice_balance(): void
    {
        [$user] = $this->foundation();
        $invoice = Invoice::create(['invoice_number' => 'INV-TEST-001', 'invoice_date' => '2026-09-13', 'due_date' => '2026-10-13', 'subtotal' => 100, 'tax_amount' => 0, 'total_amount' => 100, 'paid_amount' => 0, 'status' => 'Issued', 'created_by' => $user->id]);

        $this->expectException(\Exception::class);
        app(PaymentService::class)->recordPayment(['invoice_id' => $invoice->id, 'amount' => 101], $user->id);
    }

    private function foundation(): array
    {
        $role = Role::create(['name' => 'super_admin']);
        $user = User::create(['name' => 'Tester', 'email' => 'tester@example.com', 'password' => Hash::make('password'), 'role_id' => $role->id, 'is_active' => true]);
        $client = Client::create(['code' => 'CLI-TEST', 'name' => 'Client Test', 'status' => 'active']);
        return [$user, $client];
    }
}
