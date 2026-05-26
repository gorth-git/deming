<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'])) {
            return;
        }

        // After the table swap (2026_05_21_000001), MySQL auto-updated FK targets
        // during RENAME TABLE, leaving them semantically inverted:
        //   control_id → measures  (should be → controls)
        //   measure_id → controls  (should be → measures)
        // Drop and recreate with correct targets.
        Schema::table('control_measure', function (Blueprint $table) {
            $table->dropForeign(['control_id']);
            $table->dropForeign(['measure_id']);
            $table->foreign('control_id')->references('id')->on('controls');
            $table->foreign('measure_id')->references('id')->on('measures');
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'])) {
            return;
        }

        Schema::table('control_measure', function (Blueprint $table) {
            $table->dropForeign(['control_id']);
            $table->dropForeign(['measure_id']);
            $table->foreign('control_id')->references('id')->on('measures');
            $table->foreign('measure_id')->references('id')->on('controls');
        });
    }
};
