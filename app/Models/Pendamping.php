<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendamping extends Model
{
    protected $table = 'pendamping';
    protected $primaryKey = 'no_registrasi';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'no_registrasi',
        'id_pendamping',
        'id_lembaga',
        'no_pendaftaran',
        'tgl_berlaku',
        'nama',
        'alamat',
        'kode_pos',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'no_hp',
        'tempat_lahir',
        'tgl_lahir',
        'nik',
        'pendidikan',
        'universitas',
        'status',
        'nama_lembaga',
        'sumber_data',
        'jumlah_pu',
        'pekerjaan',
        'pekerjaan_lain',
        'asal_unit_kerja',
        'pns',
        'pns_golongan',
    ];

    protected $casts = [
        'pns' => 'boolean',
        'tgl_berlaku' => 'date',
        'tgl_lahir' => 'date',
        'jumlah_pu' => 'integer',
    ];
}
