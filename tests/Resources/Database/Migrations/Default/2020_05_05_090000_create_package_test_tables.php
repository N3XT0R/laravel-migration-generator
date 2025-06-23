<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create(
            'fields_test',
            static function (Blueprint $table) {
                $table->bigInteger('id', true)->unsigned()->primary();
                $table->smallInteger('small_int')->nullable();
                $table->mediumInteger('medium_int')->unique();
                $table->tinyInteger('tiny_int')->default(1)->comment('my tiny int');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('any_date')->index('testi');
                $table->double('double_value');
                $table->float('float_value', 6);
                $table->decimal('decimal_value', 2, 1)->unsigned();
                $table->string('string');
                $table->char('char', 5);
                $table->json('json');
                $table->jsonb('jsonb');
                $table->boolean('boolean');
            }
        );

        Schema::create(
            'foreign_table',
            static function (Blueprint $table) {
                $table->bigInteger('id', true)->unsigned();
                $table->bigInteger('fields_test_id')->unsigned()->nullable();
                $table->foreign('fields_test_id')->references('id')->on('fields_test')->onDelete('SET NULL')->onUpdate(
                    'CASCADE'
                );
            }
        );

        Schema::create(
            'abc',
            static function (Blueprint $table) {
                $table->bigInteger('id', true)->unsigned();
                $table->bigInteger('fields_test_id')->unsigned()->nullable();
                $table->foreign('fields_test_id')->references('id')->on('fields_test')->onDelete('SET NULL')->onUpdate(
                    'CASCADE'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('abc');
        Schema::dropIfExists('foreign_table');
        Schema::dropIfExists('fields_test');
    }
};