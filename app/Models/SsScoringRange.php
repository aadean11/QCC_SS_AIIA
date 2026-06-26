<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsScoringRange extends Model
{
    protected $table = 'm_ss_scoring_ranges';

    protected $fillable = [
        'min_score',
        'max_score',
        'ranking',
        'reward_amount',
        'approver_level',
        'description',
        'extra_score_increment',
        'extra_reward_increment',
        'is_active',
    ];

    protected $casts = [
        'min_score' => 'integer',
        'max_score' => 'integer',
        'ranking' => 'integer',
        'reward_amount' => 'decimal:0',
        'extra_score_increment' => 'integer',
        'extra_reward_increment' => 'decimal:0',
        'is_active' => 'boolean',
    ];

    public static function rangeForScore(?int $score): ?self
    {
        if ($score === null) {
            return null;
        }

        return self::where('is_active', true)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->orderBy('min_score')
            ->first();
    }

    public static function needsApprovalAfterSpv(?int $score): bool
    {
        return self::needsKdpScoringAfterSpv($score);
    }

    public static function needsKdpScoringAfterSpv(?int $score): bool
    {
        if ($score === null) {
            return false;
        }

        $range = self::rangeForScore($score);

        if ($range) {
            return strtoupper((string) $range->approver_level) !== 'SPV';
        }

        $spvMaxScore = self::maxActiveScoreForApprovers(['SPV']);

        return $spvMaxScore !== null && $score > $spvMaxScore;
    }

    public static function needsAdminScoringAfterKdp(?int $score): bool
    {
        if ($score === null) {
            return false;
        }

        $range = self::rangeForScore($score);

        if ($range) {
            return !in_array(strtoupper((string) $range->approver_level), ['SPV', 'MANAGER', 'MGR', 'KDP'], true);
        }

        $managerMaxScore = self::maxActiveScoreForApprovers(['SPV', 'MANAGER', 'MGR', 'KDP']);

        return $managerMaxScore !== null && $score > $managerMaxScore;
    }

    public static function effectiveReviewScore(?int $kdpScore, ?int $spvScore): ?int
    {
        return $kdpScore ?? $spvScore;
    }

    private static function maxActiveScoreForApprovers(array $approverLevels): ?int
    {
        $normalizedLevels = array_map('strtoupper', $approverLevels);

        $maxScore = self::where('is_active', true)
            ->whereIn('approver_level', $approverLevels)
            ->max('max_score');

        if ($maxScore !== null) {
            return (int) $maxScore;
        }

        $fallbackMaxScore = self::where('is_active', true)
            ->get(['approver_level', 'max_score'])
            ->filter(fn ($range) => in_array(strtoupper((string) $range->approver_level), $normalizedLevels, true))
            ->max('max_score');

        return $fallbackMaxScore !== null ? (int) $fallbackMaxScore : null;
    }

    public static function rewardForScore(?int $score): int
    {
        if ($score === null) {
            return 0;
        }

        $range = self::rangeForScore($score);

        if ($range) {
            return (int) $range->reward_amount;
        }

        $topRange = self::where('is_active', true)
            ->whereNotNull('extra_score_increment')
            ->whereNotNull('extra_reward_increment')
            ->orderByDesc('max_score')
            ->first();

        if (!$topRange || $score <= $topRange->max_score) {
            return 0;
        }

        $increments = (int) ceil(($score - $topRange->max_score) / $topRange->extra_score_increment);

        return (int) $topRange->reward_amount + ($increments * (int) $topRange->extra_reward_increment);
    }
}
