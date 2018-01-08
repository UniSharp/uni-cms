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
            $table->string('slug')->nullable()->unique();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('widgets', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('page');
            $table->string('type');
            $table->unsignedInteger('sort');
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('translatable');
            $table->string('lang');
            $table->string('key');
            $table->text('value')->default('');
            $table->timestamps();

            $table->unique(['translatable_type', 'translatable_id', 'lang','key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('nodes');
    }
}
