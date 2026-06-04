<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ss_submissions', function (Blueprint $table) {
            $columns = [
                'admin_npk' => fn () => $table->string('admin_npk')->nullable()->after('kdp_score_total'),
                'admin_notes' => fn () => $table->text('admin_notes')->nullable()->after('admin_npk'),
                'admin_approved_at' => fn () => $table->timestamp('admin_approved_at')->nullable()->after('admin_notes'),
                'admin_status' => fn () => $table->string('admin_status')->nullable()->after('admin_approved_at'),
                'admin_scores' => fn () => $table->json('admin_scores')->nullable()->after('admin_status'),
                'admin_score_total' => fn () => $table->unsignedSmallInteger('admin_score_total')->nullable()->after('admin_scores'),
            ];

            foreach ($columns as $name => $definition) {
                if (!Schema::hasColumn('ss_submissions', $name)) {
                    $definition();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('ss_submissions', function (Blueprint $table) {
            foreach ([
                'admin_npk',
                'admin_notes',
                'admin_approved_at',
                'admin_status',
                'admin_scores',
                'admin_score_total',
            ] as $column) {
                if (Schema::hasColumn('ss_submissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
