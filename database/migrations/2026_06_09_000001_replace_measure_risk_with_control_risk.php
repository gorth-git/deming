<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('control_risk');
        Schema::dropIfExists('measure_risk');

        Schema::create('control_risk', function (Blueprint $table) {
            $table->unsignedInteger('control_id');
            $table->unsignedBigInteger('risk_id');
            $table->primary(['control_id', 'risk_id']);
            $table->foreign('control_id')->references('id')->on('controls')->cascadeOnDelete();
            $table->foreign('risk_id')->references('id')->on('risks')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_risk');

        Schema::create('measure_risk', function (Blueprint $table) {
            $table->unsignedInteger('risk_id');
            $table->unsignedInteger('measure_id');
            $table->primary(['risk_id', 'measure_id']);
        });
    }
};
