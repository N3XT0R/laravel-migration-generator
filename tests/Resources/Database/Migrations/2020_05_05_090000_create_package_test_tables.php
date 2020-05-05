<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageTestTables extends Migration
{

    public function up(): void
    {
        Schema::table(
            'fields_test',
            static function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->smallInteger('small_int')->nullable();
                $table->mediumInteger('medium_int')->unique();
                $table->tinyInteger('tiny_int')->default(1)->comment('my tiny int');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('fields_test');
    }
}