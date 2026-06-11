<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('attendance_type', 10)->default('wfo')->after('status');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('default_attendance_type', 10)->default('wfo')->after('shift_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('attendance_type');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('default_attendance_type');
        });
    }
};
