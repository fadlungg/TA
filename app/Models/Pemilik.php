<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemilik extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemilik';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'email',
        'foto_ktp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get all land plots currently owned.
     *
     * @return HasMany<Tanah, $this>
     */
    public function tanah(): HasMany
    {
        return $this->hasMany(Tanah::class, 'pemilik_id');
    }

    /**
     * Get all mutation history where this person is the old owner.
     *
     * @return HasMany<RiwayatKepemilikan, $this>
     */
    public function riwayatPemilikLama(): HasMany
    {
        return $this->hasMany(RiwayatKepemilikan::class, 'pemilik_lama_id');
    }

    /**
     * Get all mutation history where this person is the new owner.
     *
     * @return HasMany<RiwayatKepemilikan, $this>
     */
    public function riwayatPemilikBaru(): HasMany
    {
        return $this->hasMany(RiwayatKepemilikan::class, 'pemilik_baru_id');
    }
}
