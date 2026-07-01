<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatSurat extends Model
{
    protected $table = 'riwayat_surat';
    
    protected $primaryKey = 'id_riwayat';

    public $timestamps = false;

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
