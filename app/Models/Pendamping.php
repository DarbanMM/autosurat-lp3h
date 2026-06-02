<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'tgl_berlaku' => 'date',
        'tgl_lahir' => 'date',
        'jumlah_pu' => 'integer',
    ];

    protected function pns(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool) $value,
            set: function ($value) {
                if (is_bool($value)) {
                    return $value;
                }
                if ($value === null || $value === '') {
                    return false;
                }
                if (is_numeric($value)) {
                    return ((int) $value) === 1;
                }

                return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'y', 'on'], true);
            }
        );
    }
}
