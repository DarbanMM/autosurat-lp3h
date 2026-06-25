<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKetua extends Model
{
    protected $table = 'data_ketua';

    protected $primaryKey = 'nip';
    
    // NIP is a string (e.g. 19750817 200501 1 003)
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false; // No timestamps in the migration

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'barcode_ttd',
    ];
}
