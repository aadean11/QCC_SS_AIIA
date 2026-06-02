<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('m_ss_scoring_ranges')) {
            Schema::create('m_ss_scoring_ranges', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('min_score');
                $table->unsignedSmallInteger('max_score');
                $table->unsignedTinyInteger('ranking');
                $table->decimal('reward_amount', 17, 2);
                $table->string('approver_level')->nullable();
                $table->text('description')->nullable();
                $table->unsignedSmallInteger('extra_score_increment')->nullable();
                $table->decimal('extra_reward_increment', 17, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'min_score', 'max_score']);
            });
        }

        if (DB::table('m_ss_scoring_ranges')->count() === 0) {
            $now = now();
            DB::table('m_ss_scoring_ranges')->insert([
                ['min_score' => 5, 'max_score' => 27, 'ranking' => 19, 'reward_amount' => 3500, 'approver_level' => 'SPV', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 28, 'max_score' => 33, 'ranking' => 18, 'reward_amount' => 5000, 'approver_level' => 'SPV', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 34, 'max_score' => 39, 'ranking' => 17, 'reward_amount' => 7500, 'approver_level' => 'SPV', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 40, 'max_score' => 45, 'ranking' => 16, 'reward_amount' => 10000, 'approver_level' => 'SPV', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 46, 'max_score' => 51, 'ranking' => 15, 'reward_amount' => 15000, 'approver_level' => 'SPV', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 52, 'max_score' => 57, 'ranking' => 14, 'reward_amount' => 20000, 'approver_level' => 'SPV', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 58, 'max_score' => 63, 'ranking' => 13, 'reward_amount' => 35000, 'approver_level' => 'Manager', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 64, 'max_score' => 69, 'ranking' => 12, 'reward_amount' => 50000, 'approver_level' => 'Manager', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 70, 'max_score' => 75, 'ranking' => 11, 'reward_amount' => 70000, 'approver_level' => 'Komite', 'description' => 'Diputuskan oleh SPV/MGR/Komite dari usulan Kepala Departemen terkait, tanpa melalui presentasi.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 76, 'max_score' => 81, 'ranking' => 10, 'reward_amount' => 100000, 'approver_level' => 'GM', 'description' => 'Diputuskan oleh GM melalui presentasi dengan didampingi oleh MGR dan SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 82, 'max_score' => 87, 'ranking' => 9, 'reward_amount' => 150000, 'approver_level' => 'GM', 'description' => 'Diputuskan oleh GM melalui presentasi dengan didampingi oleh MGR dan SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 88, 'max_score' => 97, 'ranking' => 8, 'reward_amount' => 200000, 'approver_level' => 'GM', 'description' => 'Diputuskan oleh GM melalui presentasi dengan didampingi oleh MGR dan SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 98, 'max_score' => 107, 'ranking' => 7, 'reward_amount' => 300000, 'approver_level' => 'Dir. In Charge', 'description' => 'Diputuskan oleh direktur in charge melalui presentasi di depan komite yang beranggotakan Direktur, GM, Manager, SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 108, 'max_score' => 117, 'ranking' => 6, 'reward_amount' => 400000, 'approver_level' => 'Dir. In Charge', 'description' => 'Diputuskan oleh direktur in charge melalui presentasi di depan komite yang beranggotakan Direktur, GM, Manager, SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 118, 'max_score' => 127, 'ranking' => 5, 'reward_amount' => 500000, 'approver_level' => 'Dir. In Charge', 'description' => 'Diputuskan oleh direktur in charge melalui presentasi di depan komite yang beranggotakan Direktur, GM, Manager, SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 128, 'max_score' => 137, 'ranking' => 4, 'reward_amount' => 600000, 'approver_level' => 'VP/PD', 'description' => 'Diputuskan oleh VP/PD melalui presentasi di depan komite yang beranggotakan Direktur, GM, Manager, SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 138, 'max_score' => 147, 'ranking' => 3, 'reward_amount' => 700000, 'approver_level' => 'VP/PD', 'description' => 'Diputuskan oleh VP/PD melalui presentasi di depan komite yang beranggotakan Direktur, GM, Manager, SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 148, 'max_score' => 157, 'ranking' => 2, 'reward_amount' => 800000, 'approver_level' => 'VP/PD', 'description' => 'Diputuskan oleh VP/PD melalui presentasi di depan komite yang beranggotakan Direktur, GM, Manager, SPV.', 'extra_score_increment' => null, 'extra_reward_increment' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['min_score' => 158, 'max_score' => 168, 'ranking' => 1, 'reward_amount' => 900000, 'approver_level' => 'VP/PD', 'description' => 'Diputuskan oleh VP/PD melalui presentasi di depan komite yang beranggotakan Direktur, GM, Manager, SPV. Apabila nilai di atas 168, setiap penambahan 27 poin ditambah Rp 3.500.', 'extra_score_increment' => 27, 'extra_reward_increment' => 3500, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('m_ss_scoring_ranges');
    }
};
