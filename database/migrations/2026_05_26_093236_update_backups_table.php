<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('backups', function (Blueprint $table) {
            if (!Schema::hasColumn('backups', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('backups', 'filename')) {
                $table->string('filename')->nullable()->after('name');
            }
            if (!Schema::hasColumn('backups', 'path')) {
                $table->string('path')->nullable()->after('filename');
            }
            if (!Schema::hasColumn('backups', 'size')) {
                $table->string('size', 50)->nullable()->after('path');
            }
            if (!Schema::hasColumn('backups', 'extension')) {
                $table->string('extension', 10)->nullable()->after('size');
            }
            if (!Schema::hasColumn('backups', 'description')) {
                $table->text('description')->nullable()->after('extension');
            }
            if (!Schema::hasColumn('backups', 'status')) {
                $table->enum('status', ['completed', 'failed', 'pending'])->default('completed')->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->dropColumn(['name', 'filename', 'path', 'size', 'extension', 'description', 'status']);
        });
    }
};
