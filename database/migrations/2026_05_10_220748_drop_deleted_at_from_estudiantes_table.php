<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::table('estudiantes', function (Blueprint $table) {
            if (Schema::hasColumn('estudiantes', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
    public function down() {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
