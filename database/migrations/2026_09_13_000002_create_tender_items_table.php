<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name', 200);
            $table->decimal('quantity', 18, 2);
            $table->string('unit', 30)->nullable();
            $table->decimal('estimated_price', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tender_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_items');
    }
};
