<?php

use Kalnoy\Nestedset\NestedSet;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniCMSTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('node');
            NestedSet::columns($table);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('widgets', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('page');
            $table->unsignedInteger('sort');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('nodes');
    }
}
