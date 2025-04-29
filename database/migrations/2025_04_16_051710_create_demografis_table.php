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
        Schema::create('demografi', function (Blueprint $table) {
            $table->tinyIncrements('id')->unsigned();
            $table->tinyInteger('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('penduduk_pria');
            $table->unsignedSmallInteger('penduduk_wanita');
            $table->year('tahun');
            $table->unsignedSmallInteger('jumlah_peserta_bpjs');
            $table->unsignedSmallInteger('jumlah_pindah_keluar');
            $table->unsignedSmallInteger('jumlah_pendatang');
            $table->unsignedSmallInteger('tamat_pt');
            $table->unsignedSmallInteger('tamat_sma');
            $table->unsignedSmallInteger('tamat_smp');
            $table->unsignedSmallInteger('tamat_sd');
            $table->unsignedSmallInteger('jumlah_lansia');
            $table->unsignedSmallInteger('jumlah_dewasa');
            $table->unsignedSmallInteger('jumlah_remaja');
            $table->unsignedSmallInteger('jumlah_anak');
            $table->unsignedSmallInteger('jumlah_penduduk');
            $table->unsignedSmallInteger('jumlah_keluarga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demografi');
    }
};
