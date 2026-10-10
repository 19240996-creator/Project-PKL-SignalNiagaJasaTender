<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('service_jobs', 'service_type')) {
            Schema::table('service_jobs', function (Blueprint $table) {
            $table->string('service_type', 30)->default('general')->after('name');
            $table->foreignId('tender_id')->nullable()->after('contract_id')->constrained('tenders')->nullOnDelete();
            $table->string('location', 255)->nullable()->after('klien');
            $table->string('work_type', 150)->nullable()->after('location');
            $table->decimal('estimated_labor_cost', 18, 2)->default(0)->after('biaya');
            $table->decimal('actual_cost', 18, 2)->default(0)->after('estimated_labor_cost');
            $table->decimal('estimated_labor_hours', 8, 2)->default(0)->after('actual_cost');
            $table->string('required_competency', 255)->nullable()->after('estimated_labor_hours');
            $table->text('diagnosis')->nullable()->after('required_competency');
            $table->text('parts_needed')->nullable()->after('diagnosis');
            $table->text('result_notes')->nullable()->after('parts_needed');
            $table->string('material_request_status', 30)->default('not_requested')->after('result_notes');
                $table->index(['service_type', 'status'], 'service_jobs_type_status_idx');
            });
        }

        if (!Schema::hasTable('technicians')) Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('name', 150);
            $table->string('phone', 40)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('competencies')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'name']);
        });

        if (!Schema::hasTable('technician_availabilities')) Schema::create('technician_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('technicians')->cascadeOnDelete();
            $table->date('available_from');
            $table->date('available_until');
            $table->string('status', 20)->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['technician_id', 'available_from', 'available_until'], 'tech_availability_dates_idx');
        });

        if (Schema::hasTable('technician_availabilities') && !DB::select("SHOW INDEX FROM technician_availabilities WHERE Key_name = 'tech_availability_dates_idx'")) {
            Schema::table('technician_availabilities', fn (Blueprint $table) => $table->index(['technician_id', 'available_from', 'available_until'], 'tech_availability_dates_idx'));
        }

        if (!Schema::hasTable('service_assignments')) Schema::create('service_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('technicians')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 30)->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['service_job_id', 'technician_id']);
            $table->index(['technician_id', 'start_date', 'end_date']);
        });

        if (!Schema::hasTable('service_material_requests')) Schema::create('service_material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name', 200);
            $table->decimal('quantity', 18, 2)->default(1);
            $table->string('unit', 30)->nullable();
            $table->string('status', 30)->default('requested');
            $table->text('notes')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['service_job_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_material_requests');
        Schema::dropIfExists('service_assignments');
        Schema::dropIfExists('technician_availabilities');
        Schema::dropIfExists('technicians');

        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropForeign(['tender_id']);
            $table->dropColumn([
                'service_type', 'tender_id', 'location', 'work_type', 'estimated_labor_cost',
                'actual_cost', 'estimated_labor_hours', 'required_competency', 'diagnosis',
                'parts_needed', 'result_notes', 'material_request_status',
            ]);
        });
    }
};
