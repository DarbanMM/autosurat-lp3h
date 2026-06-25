<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    protected $table = 'surat';

    protected $primaryKey = 'id_surat';

    protected $fillable = [
        'nama_surat',
        'keterangan',
        'id_format_surat',
    ];

    public function formatNomorSurat()
    {
        return $this->belongsTo(FormatNomorSurat::class, 'id_format_surat', 'id_format_nomor');
    }
}
