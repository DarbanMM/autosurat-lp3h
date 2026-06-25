<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatNomorCounter extends Model
{
    protected $table = 'format_nomor_counter';

    protected $fillable = [
        'id_format_nomor',
        'period_key',
        'last_nomor',
    ];

    public function formatNomor()
    {
        return $this->belongsTo(FormatNomorSurat::class, 'id_format_nomor', 'id_format_nomor');
    }

    /**
     * Generate the period_key for the current date based on reset period type.
     */
    public static function currentPeriodKey(string $resetPeriod): string
    {
        $now = now();

        return match ($resetPeriod) {
            'Mingguan' => $now->format('o-\WW'),   // e.g. "2026-W26"
            'Bulanan' => $now->format('Y-m'),       // e.g. "2026-06"
            'Tahunan' => $now->format('Y'),         // e.g. "2026"
            default => 'default',
        };
    }

    /**
     * Get the next number for a given format.
     * Atomically increments and returns the new number.
     */
    public static function getNextNumber(int $formatId, string $resetPeriod): int
    {
        $periodKey = self::currentPeriodKey($resetPeriod);

        $counter = self::firstOrCreate(
            [
                'id_format_nomor' => $formatId,
                'period_key' => $periodKey,
            ],
            ['last_nomor' => 0]
        );

        $counter->increment('last_nomor');

        return $counter->last_nomor;
    }

    /**
     * Reset all counters for a given format (e.g. when format is edited).
     */
    public static function resetForFormat(int $formatId): void
    {
        self::where('id_format_nomor', $formatId)->delete();
    }
}
