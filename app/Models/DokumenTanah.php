<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenTanah extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dokumen_tanah';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanah_id',
        'nama_dokumen',
        'file_path',
        'uploaded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the land associated with this document.
     *
     * @return BelongsTo<Tanah, $this>
     */
    public function tanah(): BelongsTo
    {
        return $this->belongsTo(Tanah::class, 'tanah_id');
    }
}
