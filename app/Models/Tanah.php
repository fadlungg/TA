<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tanah extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tanah';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_sertifikat',
        'no_letter_c',
        'no_persil',
        'klas_tanah',
        'status_bengkok',
        'jenis_hak_id',
        'luas',
        'alamat',
        'wilayah_id',
        'latitude',
        'longitude',
        'status_tanah_id',
        'pemilik_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'luas' => 'double',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the type of right for this land.
     *
     * @return BelongsTo<JenisHak, $this>
     */
    public function jenisHak(): BelongsTo
    {
        return $this->belongsTo(JenisHak::class, 'jenis_hak_id');
    }

    /**
     * Get the territory associated with this land.
     *
     * @return BelongsTo<Wilayah, $this>
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    /**
     * Get the status of this land.
     *
     * @return BelongsTo<StatusTanah, $this>
     */
    public function statusTanah(): BelongsTo
    {
        return $this->belongsTo(StatusTanah::class, 'status_tanah_id');
    }

    /**
     * Get the current active owner.
     *
     * @return BelongsTo<Pemilik, $this>
     */
    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(Pemilik::class, 'pemilik_id');
    }

    /**
     * Get all documents related to this land.
     *
     * @return HasMany<DokumenTanah, $this>
     */
    public function dokumenTanah(): HasMany
    {
        return $this->hasMany(DokumenTanah::class, 'tanah_id');
    }

    /**
     * Get the owner history for this land.
     *
     * @return HasMany<RiwayatKepemilikan, $this>
     */
    public function riwayatKepemilikan(): HasMany
    {
        return $this->hasMany(RiwayatKepemilikan::class, 'tanah_id');
    }
}
