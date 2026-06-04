<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('m_ss_targets')) {
            return;
        }

        Schema::create('m_ss_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('department_code', 20);
            $table->unsignedInteger('target_amount')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month', 'department_code'], 'ss_targets_year_month_dept_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_ss_targets');
    }
};
