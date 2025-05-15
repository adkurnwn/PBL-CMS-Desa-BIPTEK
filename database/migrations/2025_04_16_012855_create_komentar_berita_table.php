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
        Schema::create('komentar_berita', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('email', 50)->unique();
            $table->unsignedSmallInteger('id_berita');
            $table->foreign('id_berita')
                ->references('id')
                ->on('news')
                ->onDelete('cascade');
            $table->unsignedInteger('id_ref_komentar')->nullable();
            $table->foreign('id_ref_komentar')
                ->references('id')
                ->on('komentar_berita')
                ->onDelete('cascade');
            $table->string('nama_user', 50);
            $table->boolean('is_approved')->default(false);
            $table->text('komentar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komentar_berita');
    }
};
