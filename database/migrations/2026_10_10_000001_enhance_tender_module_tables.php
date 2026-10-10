<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance tenders table with columns for Administrasi, RAB, and Lapangan/Proyek
        Schema::table('tenders', function (Blueprint $table) {
            if (!Schema::hasColumn('tenders', 'alasan_metode')) {
                $table->text('alasan_metode')->nullable()->after('nama_vendor_relasi');
            }
            if (!Schema::hasColumn('tenders', 'submission_status')) {
                $table->string('submission_status', 30)->default('draft')->after('approval_status');
            }
            if (!Schema::hasColumn('tenders', 'revisi_notes')) {
                $table->text('revisi_notes')->nullable()->after('approval_notes');
            }
            if (!Schema::hasColumn('tenders', 'rab_status')) {
                $table->string('rab_status', 30)->default('draft')->after('revisi_notes');
            }
            if (!Schema::hasColumn('tenders', 'rab_notes')) {
                $table->text('rab_notes')->nullable()->after('rab_status');
            }
            if (!Schema::hasColumn('tenders', 'rab_submitted_at')) {
                $table->timestamp('rab_submitted_at')->nullable()->after('rab_notes');
            }
            if (!Schema::hasColumn('tenders', 'rab_approved_by')) {
                $table->foreignId('rab_approved_by')->nullable()->after('rab_submitted_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('tenders', 'rab_approved_at')) {
                $table->timestamp('rab_approved_at')->nullable()->after('rab_approved_by');
            }
            if (!Schema::hasColumn('tenders', 'project_status')) {
                $table->string('project_status', 30)->default('Persiapan')->after('rab_approved_at');
            }
            if (!Schema::hasColumn('tenders', 'project_progress')) {
                $table->unsignedTinyInteger('project_progress')->default(0)->after('project_status');
            }
            if (!Schema::hasColumn('tenders', 'project_location')) {
                $table->string('project_location', 255)->nullable()->after('project_progress');
            }
            if (!Schema::hasColumn('tenders', 'project_pic')) {
                $table->string('project_pic', 150)->nullable()->after('project_location');
            }
            if (!Schema::hasColumn('tenders', 'project_start_date')) {
                $table->date('project_start_date')->nullable()->after('project_pic');
            }
            if (!Schema::hasColumn('tenders', 'project_end_date')) {
                $table->date('project_end_date')->nullable()->after('project_start_date');
            }
            if (!Schema::hasColumn('tenders', 'actual_cost')) {
                $table->decimal('actual_cost', 18, 2)->default(0)->after('project_end_date');
            }
            if (!Schema::hasColumn('tenders', 'vendor_progress')) {
                $table->unsignedTinyInteger('vendor_progress')->default(0)->after('actual_cost');
            }
            if (!Schema::hasColumn('tenders', 'vendor_notes')) {
                $table->text('vendor_notes')->nullable()->after('vendor_progress');
            }
        });

        // 2. Table tender_rab_items for Estimasi / RAB (Barang, Tenaga Kerja, Transportasi, Operasional, Vendor)
        if (!Schema::hasTable('tender_rab_items')) {
            Schema::create('tender_rab_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
                $table->string('category', 50); // barang, tenaga_kerja, transportasi, operasional, vendor
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('item_name', 200);
                $table->decimal('quantity', 18, 2)->default(1);
                $table->string('unit', 50)->nullable();
                $table->decimal('unit_cost', 18, 2)->default(0); // HPP / Biaya Modal
                $table->decimal('unit_price', 18, 2)->default(0); // Penawaran ke klien
                $table->decimal('subtotal_cost', 18, 2)->default(0);
                $table->decimal('subtotal_price', 18, 2)->default(0);
                $table->decimal('allocated_quantity', 18, 2)->default(0); // Alokasi dari Gudang
                $table->decimal('used_quantity', 18, 2)->default(0); // Terpakai di Lapangan
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tender_id', 'category']);
                $table->index('product_id');
            });
        }

        // 3. Table tender_assignments for Lapangan / Proyek (Penugasan Teknisi & Tenaga Kerja)
        if (!Schema::hasTable('tender_assignments')) {
            Schema::create('tender_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('technician_name', 150);
                $table->string('role_or_competency', 100);
                $table->string('contact_phone', 50)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status', 30)->default('Ditugaskan'); // Ditugaskan, Sedang Bekerja, Selesai, Digantikan
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tender_id', 'status']);
            });
        }

        // 4. Table tender_project_logs for Lapangan / Proyek (Log Progres, Kendala, Solusi, Foto & Biaya Aktual)
        if (!Schema::hasTable('tender_project_logs')) {
            Schema::create('tender_project_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
                $table->date('log_date');
                $table->unsignedTinyInteger('progress_percentage')->default(0);
                $table->text('activity_description');
                $table->text('obstacles')->nullable();
                $table->text('solutions')->nullable();
                $table->decimal('actual_cost_spent', 18, 2)->default(0);
                $table->string('documentation_file', 500)->nullable();
                $table->foreignId('logged_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['tender_id', 'log_date']);
            });
        }

        // 5. Table tender_approval_history for recording approval & revision actions
        if (!Schema::hasTable('tender_approval_history')) {
            Schema::create('tender_approval_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
                $table->string('module_type', 30); // administrasi, rab, proyek
                $table->string('action', 30); // diajukan, disetujui, ditolak, perlu_revisi
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tender_id', 'module_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_approval_history');
        Schema::dropIfExists('tender_project_logs');
        Schema::dropIfExists('tender_assignments');
        Schema::dropIfExists('tender_rab_items');

        Schema::table('tenders', function (Blueprint $table) {
            $table->dropForeign(['rab_approved_by']);
            $table->dropColumn([
                'alasan_metode',
                'submission_status',
                'revisi_notes',
                'rab_status',
                'rab_notes',
                'rab_submitted_at',
                'rab_approved_by',
                'rab_approved_at',
                'project_status',
                'project_progress',
                'project_location',
                'project_pic',
                'project_start_date',
                'project_end_date',
                'actual_cost',
                'vendor_progress',
                'vendor_notes',
            ]);
        });
    }
};
