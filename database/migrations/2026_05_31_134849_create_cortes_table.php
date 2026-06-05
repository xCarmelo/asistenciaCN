<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('cortes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->timestamps();
        });
        DB::table('cortes')->insert([
            ['nombre' => 'I Corte', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'II Corte', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'III Corte', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down() { Schema::dropIfExists('cortes'); }
};
