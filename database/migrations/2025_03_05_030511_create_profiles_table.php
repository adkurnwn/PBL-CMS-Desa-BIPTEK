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
        Schema::create('profiles', function (Blueprint $table) {
            $table->tinyInteger('id', 1)->unsigned();
            $table->string('name', 50);
            $table->string('email', 255)->unique();
            $table->string('phone', 20);
            $table->string('logo', 255);
            $table->string('provinsi', 50);
            $table->string('kabupaten', 50);
            $table->string('kecamatan', 50);
            $table->string('kelurahan', 50);
            $table->string('kode_pos', 10);
            $table->json('hero_images');
            $table->string('organization_image', 255);
            $table->json('lembaga')->nullable();
            $table->json('fasilitas')->nullable();
            $table->text('history');
            $table->text('vision');
            $table->text('mission');
            $table->enum('tipe_pemerintahan', ['desa', 'kelurahan'])->default('desa');
            $table->json('media_sosial')->nullable();
            $table->json('batas_desa')->nullable();
            $table->enum('primary_color', ['#244031', '#242540', '#882828'])->default('#244031');
            $table->enum('secondary_color', ['#3B614A', '#3B4659', '#9F4941'])->default('#3B614A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
