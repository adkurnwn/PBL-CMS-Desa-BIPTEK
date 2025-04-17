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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('judul');
            $table->text('isi');
            $table->enum('kategori', ['Saran', 'Keluhan']);
            $table->string('nama_user', 50);
            $table->string('email', 50);
            $table->enum('status', ['Menunggu', 'Ditanggapi'])->default('Menunggu');
            $table->text('tanggapan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
