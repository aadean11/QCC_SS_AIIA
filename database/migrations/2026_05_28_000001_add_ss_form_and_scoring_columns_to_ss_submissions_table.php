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
                'idea_no' => fn () => $table->string('idea_no')->nullable()->after('id'),
                'idea_title' => fn () => $table->string('idea_title')->nullable()->after('department_code'),
                'idea_types' => fn () => $table->json('idea_types')->nullable()->after('idea_title'),
                'implemented_date' => fn () => $table->date('implemented_date')->nullable()->after('submission_date'),
                'idea_location' => fn () => $table->string('idea_location')->nullable()->after('implemented_date'),
                'before_condition' => fn () => $table->text('before_condition')->nullable()->after('notes'),
                'cause' => fn () => $table->text('cause')->nullable()->after('before_condition'),
                'action' => fn () => $table->text('action')->nullable()->after('cause'),
                'result' => fn () => $table->text('result')->nullable()->after('action'),
                'standardization' => fn () => $table->text('standardization')->nullable()->after('result'),
                'benefit' => fn () => $table->text('benefit')->nullable()->after('standardization'),
                'benefit_amount' => fn () => $table->decimal('benefit_amount', 17, 2)->nullable()->after('benefit'),
                'implementation_status' => fn () => $table->string('implementation_status')->nullable()->after('ldr_npk'),
                'ldr_status' => fn () => $table->string('ldr_status')->nullable()->after('implementation_status'),
                'ldr_notes' => fn () => $table->text('ldr_notes')->nullable()->after('ldr_status'),
                'ldr_approved_at' => fn () => $table->timestamp('ldr_approved_at')->nullable()->after('ldr_notes'),
                'supervisor_decision' => fn () => $table->string('supervisor_decision')->nullable()->after('spv_status'),
                'supervisor_reason' => fn () => $table->text('supervisor_reason')->nullable()->after('supervisor_decision'),
                'standard_review' => fn () => $table->string('standard_review')->nullable()->after('supervisor_reason'),
                'spv_scores' => fn () => $table->json('spv_scores')->nullable()->after('standard_review'),
                'spv_score_total' => fn () => $table->unsignedTinyInteger('spv_score_total')->nullable()->after('spv_scores'),
                'kdp_scores' => fn () => $table->json('kdp_scores')->nullable()->after('kdp_status'),
                'kdp_score_total' => fn () => $table->unsignedTinyInteger('kdp_score_total')->nullable()->after('kdp_scores'),
                'final_score' => fn () => $table->unsignedTinyInteger('final_score')->nullable()->after('kdp_score_total'),
                'calculated_reward_amount' => fn () => $table->decimal('calculated_reward_amount', 17, 2)->nullable()->after('reward_amount'),
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
                'idea_no',
                'idea_title',
                'idea_types',
                'implemented_date',
                'idea_location',
                'before_condition',
                'cause',
                'action',
                'result',
                'standardization',
                'benefit',
                'benefit_amount',
                'implementation_status',
                'ldr_status',
                'ldr_notes',
                'ldr_approved_at',
                'supervisor_decision',
                'supervisor_reason',
                'standard_review',
                'spv_scores',
                'spv_score_total',
                'kdp_scores',
                'kdp_score_total',
                'final_score',
                'calculated_reward_amount',
            ] as $column) {
                if (Schema::hasColumn('ss_submissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
