<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisHak extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jenis_hak';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode',
        'nama',
    ];

    /**
     * Get all land plots of this type of right.
     *
     * @return HasMany<Tanah, $this>
     */
    public function tanah(): HasMany
    {
        return $this->hasMany(Tanah::class, 'jenis_hak_id');
    }
}
