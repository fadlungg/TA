<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusTanah extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_tanah';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
    ];

    /**
     * Get all land plots of this status.
     *
     * @return HasMany<Tanah, $this>
     */
    public function tanah(): HasMany
    {
        return $this->hasMany(Tanah::class, 'status_tanah_id');
    }
}
