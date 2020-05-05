<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePackageTestTables extends Migration
{

    public function up(): void
    {
        Schema::create(
            'fields_test',
            static function (Blueprint $table) {
                $table->bigInteger('id', true)->unsigned();
                $table->smallInteger('small_int')->nullable();
                $table->mediumInteger('medium_int')->unique();
                $table->tinyInteger('tiny_int')->default(1)->comment('my tiny int');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('any_date');
                $table->double('double_value', 4, 2);
                $table->float('float_value', 6, 3);
                $table->decimal('decimal_value', 2, 1)->unsigned();
                $table->string('string');
                $table->char('char', 5);
                $table->boolean('boolean');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('fields_test');
    }
}