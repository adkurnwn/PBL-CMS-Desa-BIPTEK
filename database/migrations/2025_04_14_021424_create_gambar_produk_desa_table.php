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
        Schema::create('gambar_produk_desa', function (Blueprint $table) {
            $table->integerIncrements('id')->unsigned();
            $table->unsignedSmallInteger('produk_desa_id');
            $table->foreign('produk_desa_id')
                ->references('id')
                ->on('produk_desa')
                ->onDelete('cascade');
            $table->string('images');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambar_produk_desa');
    }
};
