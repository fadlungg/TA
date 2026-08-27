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
        Schema::create('tanah', function (Blueprint $table) {
            $table->id();
            $table->string('no_sertifikat')->unique();
            $table->foreignId('jenis_hak_id')->constrained('jenis_hak');
            $table->double('luas');
            $table->text('alamat');
            $table->foreignId('wilayah_id')->constrained('wilayah');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('status_tanah_id')->constrained('status_tanah');
            $table->foreignId('pemilik_id')->constrained('pemilik');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanah');
    }
};
