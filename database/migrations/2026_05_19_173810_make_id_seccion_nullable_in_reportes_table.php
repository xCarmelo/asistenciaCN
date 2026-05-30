<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->bigInteger('id_seccion')->unsigned()->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->bigInteger('id_seccion')->unsigned()->nullable(false)->change();
        });
    }
};
