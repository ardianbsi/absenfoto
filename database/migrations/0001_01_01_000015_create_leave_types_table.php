<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('quota')->default(0);
            $table->boolean('is_paid')->default(true);
            $table->boolean('is_deduct_quota')->default(true);
            $table->boolean('require_attachment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('max_days')->default(999);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
