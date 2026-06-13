<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PendampingImport extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'original_filename',
        'file_path',
        'delimiter',
        'header_map',
        'column_count',
        'total_rows',
        'processed_rows',
        'imported_count',
        'skipped_count',
        'status',
        'errors',
        'message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'header_map' => 'array',
        'errors' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function isFinished(): bool
    {
        return in_array($this->status, ['completed', 'failed'], true);
    }

    public function progressPercent(): int
    {
        if ($this->total_rows === 0) {
            return 0;
        }

        return (int) min(100, round(($this->processed_rows / $this->total_rows) * 100));
    }
}
