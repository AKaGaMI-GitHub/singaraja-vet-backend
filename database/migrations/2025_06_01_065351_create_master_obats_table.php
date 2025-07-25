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
        Schema::create('master_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_obat_id');
            $table->string('nama_obat');
            $table->text('deskripsi_obat');
            $table->enum('is_active', [0, 1])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_obats');
    }
};
