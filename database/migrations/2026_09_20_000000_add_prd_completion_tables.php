<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('name')->constrained('product_categories')->nullOnDelete();
        });

        Schema::create('tender_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_terminal')->default(false);
            $table->timestamps();
        });

        Schema::table('tenders', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('status')->constrained('tender_statuses')->nullOnDelete();
        });

        Schema::create('tender_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->restrictOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('decision', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('evaluated_at');
            $table->timestamps();
            $table->index(['tender_id', 'evaluated_at']);
        });

        Schema::create('service_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->string('bill_number', 50)->unique();
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->string('status', 20)->default('Draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('service_bill_id')->nullable()->after('service_job_id')->constrained('service_bills')->nullOnDelete();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description', 255);
            $table->decimal('quantity', 18, 2)->default(1);
            $table->decimal('price', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->timestamps();
        });

        foreach (['Ditemukan', 'Evaluasi', 'Persiapan Dokumen', 'Penawaran', 'Menang', 'Kalah', 'Kontrak', 'Selesai', 'Batal'] as $sort => $name) {
            DB::table('tender_statuses')->insert([
                'name' => $name,
                'sort_order' => $sort + 1,
                'is_terminal' => in_array($name, ['Menang', 'Kalah', 'Selesai', 'Batal'], true),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('products')->whereNotNull('category')->where('category', '<>', '')->distinct()->pluck('category')->each(function (string $name): void {
            $categoryId = DB::table('product_categories')->where('name', $name)->value('id');
            if (!$categoryId) {
                $categoryId = DB::table('product_categories')->insertGetId([
                    'name' => $name,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('products')->where('category', $name)->update(['category_id' => $categoryId]);
        });

        DB::table('tender_statuses')->get()->each(function (object $status): void {
            DB::table('tenders')->where('status', $status->name)->update(['status_id' => $status->id]);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', fn (Blueprint $table) => $table->dropConstrainedForeignId('service_bill_id'));
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('service_bills');
        Schema::dropIfExists('tender_evaluations');
        Schema::table('tenders', fn (Blueprint $table) => $table->dropConstrainedForeignId('status_id'));
        Schema::dropIfExists('tender_statuses');
        Schema::table('products', fn (Blueprint $table) => $table->dropConstrainedForeignId('category_id'));
        Schema::dropIfExists('product_categories');
    }
};