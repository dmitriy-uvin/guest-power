<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlexaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alexa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('platform_id');
            $table->float('rank');
            $table->string('country');
            $table->integer('country_rank');

            $table
                ->foreign('platform_id')
                ->references('id')
                ->on('platforms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alexa', function (Blueprint $table) {
            $table->dropForeign('alexa_platform_id_foreign');
        });
        Schema::dropIfExists('alexa');
    }
}
