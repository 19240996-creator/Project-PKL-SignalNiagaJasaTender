<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_logos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('category', 100)->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('badge_text', 50)->nullable();
            $table->string('badge_color', 50)->default('text-blue-600');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_logos');
    }
};
