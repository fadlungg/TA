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
        Schema::table('wilayah', function (Blueprint $table) {
            $table->string('nama_dusun')->nullable()->after('nama_desa');
            $table->string('no_rw')->nullable()->after('nama_dusun');
            $table->string('no_rt')->nullable()->after('no_rw');
        });

        Schema::table('tanah', function (Blueprint $table) {
            $table->string('no_letter_c')->nullable()->after('no_sertifikat');
            $table->string('no_persil')->nullable()->after('no_letter_c');
            $table->string('klas_tanah')->nullable()->after('no_persil');
            $table->string('status_bengkok')->nullable()->after('klas_tanah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wilayah', function (Blueprint $table) {
            $table->dropColumn(['nama_dusun', 'no_rw', 'no_rt']);
        });

        Schema::table('tanah', function (Blueprint $table) {
            $table->dropColumn(['no_letter_c', 'no_persil', 'klas_tanah', 'status_bengkok']);
        });
    }
};
