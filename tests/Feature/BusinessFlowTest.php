<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\Tender;
use App\Models\TenderDocument;
use App\Models\TenderEvaluation;
use App\Models\User;
use App\Notifications\TenderDeadlineReminder;
use App\Services\PaymentService;
use App\Services\SalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_sale_creates_invoice_items(): void
    {
        [$user] = $this->foundation();
        $product = Product::create(['sku' => 'SKU-INVOICE', 'name' => 'Produk Invoice', 'unit' => 'Unit', 'purchase_price' => 10, 'selling_price' => 20, 'minimum_stock' => 1, 'is_active' => true]);
        StockMovement::create(['product_id' => $product->id, 'movement_type' => 'IN', 'quantity' => 5, 'movement_date' => now(), 'created_by' => $user->id]);

        app(SalesService::class)->createSale(['customer_name' => 'Pelanggan', 'sale_date' => '2026-09-13'], [['product_id' => $product->id, 'quantity' => 2, 'price' => 20]], $user->id);

        $this->assertDatabaseHas('invoice_items', ['description' => 'Produk Invoice', 'quantity' => 2, 'subtotal' => 40]);
    }

    public function test_tender_evaluation_is_recorded(): void
    {
        [$user, $client] = $this->foundation();
        $tender = Tender::create(['client_id' => $client->id, 'tender_number' => 'TDR-EVAL-001', 'name' => 'Tender Evaluasi', 'found_date' => '2026-09-13', 'status' => 'Evaluasi', 'estimated_value' => 100, 'bid_value' => 90, 'created_by' => $user->id]);

        $response = $this->actingAs($user)->post(route('tender.evaluations.store', $tender), ['score' => 88, 'decision' => 'Proceed', 'notes' => 'Layak dilanjutkan']);

        $response->assertRedirect();
        $this->assertDatabaseHas('tender_evaluations', ['tender_id' => $tender->id, 'decision' => 'Proceed', 'score' => 88]);
        $this->assertDatabaseHas('tenders', ['id' => $tender->id, 'status' => 'Penawaran']);
    }

    public function test_rejected_tender_moves_to_lost_status(): void
    {
        [$user, $client] = $this->foundation();
        $tender = Tender::create(['client_id' => $client->id, 'tender_number' => 'TDR-REJECT-001', 'name' => 'Tender Ditolak', 'found_date' => '2026-09-13', 'status' => 'Evaluasi', 'estimated_value' => 100, 'bid_value' => 90, 'created_by' => $user->id]);

        $response = $this->actingAs($user)->post(route('tender.evaluations.store', $tender), ['score' => 40, 'decision' => 'Reject', 'notes' => 'Tidak memenuhi syarat']);

        $response->assertRedirect();
        $this->assertDatabaseHas('tender_evaluations', ['tender_id' => $tender->id, 'decision' => 'Reject']);
        $this->assertDatabaseHas('tenders', ['id' => $tender->id, 'status' => 'Kalah', 'result' => 'Kalah']);
    }

    public function test_uploading_tender_document_replaces_previous_document(): void
    {
        [$user, $client] = $this->foundation();
        Storage::fake('public');
        $tender = Tender::create([
            'client_id' => $client->id,
            'tender_number' => 'TDR-DOCUMENT-001',
            'name' => 'Tender Dokumen',
            'found_date' => '2026-09-13',
            'status' => 'Penawaran',
            'estimated_value' => 100,
            'bid_value' => 90,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->post(route('tender.upload', $tender), [
            'document_name' => 'Proposal Lama',
            'file' => UploadedFile::fake()->create('proposal-lama.pdf', 10, 'application/pdf'),
        ]);
        $oldPath = TenderDocument::query()->where('tender_id', $tender->id)->value('file_path');

        $this->actingAs($user)->post(route('tender.upload', $tender), [
            'document_name' => 'Proposal Terbaru',
            'file' => UploadedFile::fake()->create('proposal-terbaru.pdf', 10, 'application/pdf'),
        ]);

        $this->assertDatabaseCount('tender_documents', 1);
        $this->assertDatabaseHas('tender_documents', [
            'tender_id' => $tender->id,
            'document_name' => 'Proposal Terbaru',
        ]);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_deadline_command_notifies_business_roles(): void
    {
        Notification::fake();
        [$user, $client] = $this->foundation();
        Tender::create(['client_id' => $client->id, 'tender_number' => 'TDR-REMINDER-001', 'name' => 'Tender Reminder', 'found_date' => '2026-09-13', 'deadline' => now()->addDays(3), 'status' => 'Evaluasi', 'estimated_value' => 100, 'bid_value' => 90, 'created_by' => $user->id]);

        $this->artisan('tenders:deadline-reminders')->assertSuccessful();

        Notification::assertSentTo($user, TenderDeadlineReminder::class);
    }

    public function test_local_password_reset_link_can_be_used(): void
    {
        [$user] = $this->foundation();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('reset_url');

        $token = Password::broker()->createToken($user);
        $resetResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $resetResponse->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_registration_requires_super_admin_approval_before_login(): void
    {
        $managementRole = Role::create(['name' => 'management']);

        $response = $this->post(route('register'), [
            'name' => 'New Employee',
            'email' => 'new.employee@example.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('users', [
            'email' => 'new.employee@example.com',
            'role_id' => $managementRole->id,
            'is_active' => false,
        ]);

        $this->post(route('login'), [
            'email' => 'new.employee@example.com',
            'password' => 'password-123',
        ])->assertSessionHasErrors('email');

        $user = User::where('email', 'new.employee@example.com')->firstOrFail();
        $user->update(['is_active' => true]);

        $this->post(route('login'), [
            'email' => 'new.employee@example.com',
            'password' => 'password-123',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_user_delete_requires_matching_email_confirmation(): void
    {
        [$admin] = $this->foundation();
        $target = User::create([
            'name' => 'Target User',
            'email' => 'target@example.com',
            'password' => Hash::make('password'),
            'role_id' => $admin->role_id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $target), ['email_confirmation' => 'wrong@example.com'])
            ->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $target->id]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $target), ['email_confirmation' => $target->email])
            ->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    private function foundation(): array
    {
        $role = Role::create(['name' => 'owner']);
        $user = User::create(['name' => 'Tester', 'email' => 'tester@example.com', 'password' => Hash::make('password'), 'role_id' => $role->id, 'is_active' => true]);
        $client = Client::create(['code' => 'CLI-TEST', 'name' => 'Client Test', 'status' => 'active']);
        return [$user, $client];
    }
}
