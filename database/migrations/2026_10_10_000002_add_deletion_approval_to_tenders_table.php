<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            if (!Schema::hasColumn('tenders', 'deletion_status')) {
                $table->string('deletion_status', 30)->default('none')->after('submission_status');
            }
            if (!Schema::hasColumn('tenders', 'deletion_reason')) {
                $table->text('deletion_reason')->nullable()->after('deletion_status');
            }
            if (!Schema::hasColumn('tenders', 'deletion_requested_by')) {
                $table->foreignId('deletion_requested_by')->nullable()->after('deletion_reason')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('tenders', 'deletion_requested_at')) {
                $table->timestamp('deletion_requested_at')->nullable()->after('deletion_requested_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropForeign(['deletion_requested_by']);
            $table->dropColumn([
                'deletion_status',
                'deletion_reason',
                'deletion_requested_by',
                'deletion_requested_at',
            ]);
        });
    }
};
