<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatNomorSurat extends Model
{
    protected $table = 'format_nomor_surat';

    protected $primaryKey = 'id_format_nomor';

    protected $fillable = [
        'setting_surat',
        'reset_period',
        'display_format',
    ];

    protected $casts = [
        'setting_surat' => 'array',
    ];
}
