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
            $table->json('hero_images');
            $table->string('organization_image', 255);
            $table->json('lembaga');
            $table->json('fasilitas');
            $table->text('history');
            $table->text('vision');
            $table->text('mission');
            $table->enum('primary_color', ['#000000', '#ffffff']);
            $table->enum('secondary_color', ['#000000', '#ffffff']);
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
