<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!$this->indexExists('t_qcc_circle_steps', 't_qcc_circle_steps_circle_id_index')) {
            Schema::table('t_qcc_circle_steps', function (Blueprint $table) {
                $table->index('qcc_circle_id', 't_qcc_circle_steps_circle_id_index');
            });
        }

        Schema::table('t_qcc_circle_steps', function (Blueprint $table) {
            $table->dropUnique('t_qcc_circle_steps_unique');
            $table->unique(
                ['qcc_circle_id', 'qcc_theme_id', 'qcc_step_id'],
                't_qcc_circle_steps_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_qcc_circle_steps', function (Blueprint $table) {
            $table->dropUnique('t_qcc_circle_steps_unique');
            $table->unique(
                ['qcc_circle_id', 'qcc_step_id'],
                't_qcc_circle_steps_unique'
            );
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        return !empty(DB::select(
            'SHOW INDEX FROM '.$table.' WHERE Key_name = ?',
            [$index]
        ));
    }
};
