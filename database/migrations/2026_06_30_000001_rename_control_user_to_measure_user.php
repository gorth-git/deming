<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('control_user', 'measure_user');
        Schema::rename('control_user_group', 'measure_user_group');
    }

    public function down(): void
    {
        Schema::rename('measure_user', 'control_user');
        Schema::rename('measure_user_group', 'control_user_group');
    }
};
