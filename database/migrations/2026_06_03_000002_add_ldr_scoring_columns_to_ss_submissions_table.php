<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ss_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('ss_submissions', 'ldr_scores')) {
                $table->json('ldr_scores')->nullable()->after('ldr_approved_at');
            }

            if (!Schema::hasColumn('ss_submissions', 'ldr_score_total')) {
                $table->unsignedSmallInteger('ldr_score_total')->nullable()->after('ldr_scores');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ss_submissions', function (Blueprint $table) {
            foreach (['ldr_scores', 'ldr_score_total'] as $column) {
                if (Schema::hasColumn('ss_submissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
