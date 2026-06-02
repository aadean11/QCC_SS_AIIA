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
        if ($score === null) {
            return false;
        }

        $range = self::rangeForScore($score);
        if (!$range) {
            $topRange = self::where('is_active', true)->orderByDesc('max_score')->first();

            return $topRange && $score > $topRange->max_score;
        }

        return $range->max_score > 57 || strtoupper((string) $range->approver_level) !== 'SPV';
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
