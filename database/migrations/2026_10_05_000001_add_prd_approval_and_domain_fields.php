<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tenders table: Add metode_penanganan, nama_vendor_relasi, approval_status
        Schema::table('tenders', function (Blueprint $table) {
            if (!Schema::hasColumn('tenders', 'metode_penanganan')) {
                $table->string('metode_penanganan', 30)->default('internal')->after('name');
            }
            if (!Schema::hasColumn('tenders', 'nama_vendor_relasi')) {
                $table->string('nama_vendor_relasi', 200)->nullable()->after('metode_penanganan');
            }
            if (!Schema::hasColumn('tenders', 'approval_status')) {
                $table->string('approval_status', 20)->default('approved')->after('status');
            }
            if (!Schema::hasColumn('tenders', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('tenders', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approval_notes')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('tenders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });

        // 2. Service jobs table: Add direct client, biaya, approval_status, created_by
        Schema::table('service_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('service_jobs', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('contract_id')->constrained('clients')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_jobs', 'klien')) {
                $table->string('klien', 200)->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('service_jobs', 'biaya')) {
                $table->decimal('biaya', 18, 2)->default(0)->after('name');
            }
            if (!Schema::hasColumn('service_jobs', 'deskripsi_pekerjaan')) {
                $table->text('deskripsi_pekerjaan')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('service_jobs', 'approval_status')) {
                $table->string('approval_status', 20)->default('approved')->after('status');
            }
            if (!Schema::hasColumn('service_jobs', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('service_jobs', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('approval_notes')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_jobs', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_jobs', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });

        // Allow contract_id in service_jobs to be nullable for direct standalone services.
        // Schema::change() keeps this migration compatible with SQLite test databases.
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->foreignId('contract_id')->nullable()->change();
        });

        // 3. Sales table: Add approval_status, catatan_pengiriman, nama_barang, kuantitas, harga_satuan
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'nama_barang')) {
                $table->string('nama_barang', 200)->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('sales', 'kuantitas')) {
                $table->decimal('kuantitas', 18, 2)->default(1)->after('nama_barang');
            }
            if (!Schema::hasColumn('sales', 'harga_satuan')) {
                $table->decimal('harga_satuan', 18, 2)->default(0)->after('kuantitas');
            }
            if (!Schema::hasColumn('sales', 'catatan_pengiriman')) {
                $table->text('catatan_pengiriman')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('sales', 'approval_status')) {
                $table->string('approval_status', 20)->default('approved')->after('status');
            }
            if (!Schema::hasColumn('sales', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('sales', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approval_notes')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('sales', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['metode_penanganan', 'nama_vendor_relasi', 'approval_status', 'approval_notes', 'approved_by', 'approved_at']);
        });

        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['client_id', 'klien', 'biaya', 'deskripsi_pekerjaan', 'approval_status', 'approval_notes', 'created_by', 'approved_by', 'approved_at']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['nama_barang', 'kuantitas', 'harga_satuan', 'catatan_pengiriman', 'approval_status', 'approval_notes', 'approved_by', 'approved_at']);
        });
    }
};
