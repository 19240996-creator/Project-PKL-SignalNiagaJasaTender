<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * User references are optional for historical records. Deleting a user
     * must not delete or block access to the records they created.
     */
    public function up(): void
    {
        $references = [
            ['service_quotations', 'created_by'],
            ['tender_documents', 'uploaded_by'],
            ['sales_quotations', 'created_by'],
            ['customer_orders', 'created_by'],
            ['procurements', 'created_by'],
            ['sales', 'created_by'],
            ['invoices', 'created_by'],
            ['payments', 'created_by'],
            ['tender_evaluations', 'evaluator_id'],
            ['service_bills', 'created_by'],
            ['contracts', 'created_by'],
        ];

        foreach ($references as [$tableName, $column]) {
            Schema::table($tableName, function (Blueprint $table) use ($column): void {
                $table->dropForeign([$column]);
                $table->unsignedBigInteger($column)->nullable()->change();
                $table->foreign($column)
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $references = [
            ['service_quotations', 'created_by'],
            ['tender_documents', 'uploaded_by'],
            ['sales_quotations', 'created_by'],
            ['customer_orders', 'created_by'],
            ['procurements', 'created_by'],
            ['sales', 'created_by'],
            ['invoices', 'created_by'],
            ['payments', 'created_by'],
            ['tender_evaluations', 'evaluator_id'],
            ['service_bills', 'created_by'],
            ['contracts', 'created_by'],
        ];

        foreach ($references as [$tableName, $column]) {
            Schema::table($tableName, function (Blueprint $table) use ($column): void {
                $table->dropForeign([$column]);
                $table->foreign($column)
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();
            });
        }
    }
};
