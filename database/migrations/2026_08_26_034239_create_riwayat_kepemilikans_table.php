<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_kepemilikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanah_id')->constrained('tanah')->onDelete('cascade');
            $table->foreignId('pemilik_lama_id')->nullable()->constrained('pemilik');
            $table->foreignId('pemilik_baru_id')->constrained('pemilik');
            $table->string('jenis_mutasi'); // jual_beli, waris, hibah, tukar_guling
            $table->date('tanggal_mutasi');
            $table->string('dokumen_path')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kepemilikan');
    }
};
