<?php

{{MigrationNamespace}}
use Illuminate\Database\Schema\Blueprint;

class {{$className}} extends {{MigrationClass}}
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create({{$tableName}}, function (Blueprint $table) {
            {{$columns}}
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists({{$tableName}});
    }

}
