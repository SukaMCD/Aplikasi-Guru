<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jenis_kegiatan', function (Blueprint $table) {
            $table->id('id_jenis_kegiatan');
            $table->string('nama_kegiatan');
        });
    }

    public function down(): void {
        Schema::dropIfExists('jenis_kegiatan');
    }
};
