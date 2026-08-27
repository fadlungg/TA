<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wilayah';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kecamatan',
        'nama_desa',
        'nama_dusun',
        'no_rw',
        'no_rt',
    ];

    /**
     * Get all land plots in this territory.
     *
     * @return HasMany<Tanah, $this>
     */
    public function tanah(): HasMany
    {
        return $this->hasMany(Tanah::class, 'wilayah_id');
    }
}
