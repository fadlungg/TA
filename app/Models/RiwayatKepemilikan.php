<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKepemilikan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'riwayat_kepemilikan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanah_id',
        'pemilik_lama_id',
        'pemilik_baru_id',
        'jenis_mutasi',
        'tanggal_mutasi',
        'dokumen_path',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_mutasi' => 'date',
    ];

    /**
     * Get the land associated with this history log.
     *
     * @return BelongsTo<Tanah, $this>
     */
    public function tanah(): BelongsTo
    {
        return $this->belongsTo(Tanah::class, 'tanah_id');
    }

    /**
     * Get the old owner.
     *
     * @return BelongsTo<Pemilik, $this>
     */
    public function pemilikLama(): BelongsTo
    {
        return $this->belongsTo(Pemilik::class, 'pemilik_lama_id');
    }

    /**
     * Get the new owner.
     *
     * @return BelongsTo<Pemilik, $this>
     */
    public function pemilikBaru(): BelongsTo
    {
        return $this->belongsTo(Pemilik::class, 'pemilik_baru_id');
    }
}
