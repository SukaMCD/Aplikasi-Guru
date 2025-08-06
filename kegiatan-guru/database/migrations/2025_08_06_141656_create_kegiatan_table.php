<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id('id_kegiatan');
            $table->unsignedBigInteger('id_guru');
            $table->unsignedBigInteger('id_kelas');
            $table->unsignedBigInteger('id_jenis_kegiatan');
            $table->date('tanggal');
            $table->text('laporan')->nullable();

            // Foreign key sesuai kolom yang kamu buat di SQL
            $table->foreign('id_guru')->references('id_guru')->on('guru');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas');
            $table->foreign('id_jenis_kegiatan')->references('id_jenis_kegiatan')->on('jenis_kegiatan');
        });
    }

    public function down(): void {
        Schema::dropIfExists('kegiatan');
    }
};
