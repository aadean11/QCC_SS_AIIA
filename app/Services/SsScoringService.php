<?php

namespace App\Services;

class SsScoringService
{
    public const CRITERIA = [
        'safety' => 'Safety',
        'lingkungan' => 'Lingkungan',
        'ergonomi_higiene' => 'Ergonomi/Higiene',
        'quality' => 'Quality',
        'usaha' => 'Usaha',
        'manfaat' => 'Manfaat',
        'kepekaan' => 'Kepekaan',
        'keaslian' => 'Keaslian',
        'delivery' => 'Delivery',
        'cost_mp' => 'Cost/MP',
    ];

    public const CRITERIA_MAX_SCORES = [
        'safety' => 30,
        'lingkungan' => 30,
        'ergonomi_higiene' => 20,
        'quality' => 20,
        'usaha' => 20,
        'manfaat' => 10,
        'kepekaan' => 10,
        'keaslian' => 10,
        'delivery' => 10,
        'cost_mp' => 30,
    ];

    public const IDEA_TYPES = [
        'quality' => 'Quality',
        'cost' => 'Cost',
        'delivery' => 'Delivery',
        'safety' => 'Safety',
        'moral' => 'Moral',
    ];

    public const IMPLEMENTATION_STATUSES = [
        'belum_dilaksanakan' => 'Belum Dilaksanakan',
        'sedang_dilaksanakan' => 'Sedang Dilaksanakan',
        'sudah_dilaksanakan' => 'Sudah Dilaksanakan',
    ];

    public const SUPERVISOR_DECISIONS = [
        'perlu_dilakukan' => 'Perlu / Boleh Dilakukan',
        'dipertimbangkan' => 'Dipertimbangkan (Max. 1 Bulan)',
        'ditangguhkan' => 'Ditangguhkan',
        'ditolak' => 'Tidak Perlu Dilaksanakan / Ditolak',
    ];

    public const STANDARD_REVIEWS = [
        'meningkatkan_std' => 'Meningkatkan STD',
        'pencegahan' => 'Pencegahan',
        'mengembalikan_std' => 'Mengembalikan STD',
    ];

    public static function criteria(): array
    {
        return self::CRITERIA;
    }

    public static function criteriaMaxScores(): array
    {
        return self::CRITERIA_MAX_SCORES;
    }

    public static function maxScoreForCriterion(string $key): int
    {
        return self::CRITERIA_MAX_SCORES[$key] ?? 0;
    }

    public static function scoreValidationRules(): array
    {
        $rules = [
            'scores' => 'required_if:action,approved|array',
        ];

        foreach (self::CRITERIA as $key => $label) {
            $rules["scores.{$key}"] = 'nullable|integer|min:0|max:' . self::maxScoreForCriterion($key);
        }

        return $rules;
    }

    public static function ideaTypes(): array
    {
        return self::IDEA_TYPES;
    }

    public static function implementationStatuses(): array
    {
        return self::IMPLEMENTATION_STATUSES;
    }

    public static function supervisorDecisions(): array
    {
        return self::SUPERVISOR_DECISIONS;
    }

    public static function standardReviews(): array
    {
        return self::STANDARD_REVIEWS;
    }

    public static function normalizeScores(array $scores): array
    {
        $normalized = [];

        foreach (self::CRITERIA as $key => $label) {
            $value = (int) ($scores[$key] ?? 0);
            $normalized[$key] = max(0, min(self::maxScoreForCriterion($key), $value));
        }

        return $normalized;
    }

    public static function total(array $scores): int
    {
        return array_sum(self::normalizeScores($scores));
    }

}
