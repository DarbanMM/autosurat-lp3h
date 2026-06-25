<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatSurat extends Model
{
    protected $table = 'riwayat_surat';
    
    protected $primaryKey = 'id_riwayat';

    public $timestamps = false; // We only have created_at, handled manually or via cast if needed. But wait, the migration has created_at nullable. Let me check the migration again.

    protected $fillable = [
        'tgl_dibuat',
        'nomor_surat',
        'nama_surat',
        'keterangan',
        'created_at',
    ];

    protected $casts = [
        'tgl_dibuat' => 'datetime',
        'created_at' => 'datetime',
        'keterangan' => 'array',
    ];
}
