<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('filename');
            $table->string('path');
            $table->string('size', 50);
            $table->string('extension', 10);
            $table->text('description')->nullable();
            $table->enum('status', ['completed', 'failed', 'pending'])->default('completed');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('backups'); }
};
