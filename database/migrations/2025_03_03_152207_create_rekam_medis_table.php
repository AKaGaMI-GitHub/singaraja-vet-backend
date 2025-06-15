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
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('pet_id')->nullable();
            $table->foreignId('jenis_hewan_id')->nullable();
            $table->foreignId('ras_id')->nullable();
            $table->string('nama_owner')->nullable();
            $table->string('nama_pet')->nullable();
            $table->string('diagnosa');
            $table->text('penanganan')->nullable();
            $table->longText('obat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
