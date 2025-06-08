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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('jenis_hewan_id');
            $table->string('nama_depan_pet');
            $table->string('nama_belakang_pet')->nullable();
            $table->text('avatar')->nullable();
            $table->char('tanggal_lahir', 2)->nullable();
            $table->char('bulan_lahir', 2)->nullable();
            $table->char('tahun_lahir', 4)->nullable();
            $table->enum('jenis_kelamin_pet', ['male', 'female']);
            $table->enum('is_alive', ['0', '1']);
            $table->string('alasan_meninggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
