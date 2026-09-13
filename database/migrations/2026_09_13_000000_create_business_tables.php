<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('company_name', 200)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });

        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('tender_number', 50)->unique();
            $table->string('name', 200);
            $table->string('source', 100)->nullable();
            $table->date('found_date');
            $table->date('deadline')->nullable();
            $table->decimal('estimated_value', 18, 2)->default(0);
            $table->decimal('bid_value', 18, 2)->default(0);
            $table->string('status', 30);
            $table->string('result', 20)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'deadline']);
        });

        Schema::create('tender_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('document_name', 200);
            $table->string('file_path', 500);
            $table->string('file_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->nullable()->unique()->constrained('tenders')->nullOnDelete();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('contract_number', 50)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('contract_value', 18, 2)->default(0);
            $table->decimal('fee_percentage', 5, 2)->default(0);
            $table->decimal('fee_amount', 18, 2)->default(0);
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'end_date']);
        });

        Schema::create('service_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->restrictOnDelete();
            $table->string('job_number', 50)->unique();
            $table->string('name', 200);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['contract_id', 'status']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 200);
            $table->string('category', 100)->nullable();
            $table->string('unit', 30);
            $table->decimal('purchase_price', 18, 2)->default(0);
            $table->decimal('selling_price', 18, 2)->default(0);
            $table->decimal('minimum_stock', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['name', 'is_active']);
        });

        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->string('procurement_number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('tender_id')->nullable()->constrained('tenders')->nullOnDelete();
            $table->date('procurement_date');
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'procurement_date']);
        });

        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('procurements')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 18, 2);
            $table->decimal('price', 18, 2);
            $table->decimal('subtotal', 18, 2);
            $table->timestamps();
            $table->index('product_id');
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number', 50)->unique();
            $table->string('customer_name', 200);
            $table->string('customer_phone', 30)->nullable();
            $table->foreignId('tender_id')->nullable()->constrained('tenders')->nullOnDelete();
            $table->date('sale_date');
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'sale_date']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 18, 2);
            $table->decimal('price', 18, 2);
            $table->decimal('subtotal', 18, 2);
            $table->timestamps();
            $table->index('product_id');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('service_job_id')->nullable()->constrained('service_jobs')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'due_date']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 18, 2);
            $table->string('payment_method', 30);
            $table->string('reference_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['invoice_id', 'payment_date']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('movement_type', 20);
            $table->decimal('quantity', 18, 2);
            $table->string('reference_type', 30)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->dateTime('movement_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['product_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->string('table_name', 100);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['table_name', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ([
            'audit_logs', 'stock_movements', 'payments', 'invoices', 'sale_items',
            'sales', 'procurement_items', 'procurements', 'products', 'suppliers',
            'service_jobs', 'contracts', 'tender_documents', 'tenders', 'clients',
        ] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }
};
