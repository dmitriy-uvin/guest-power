<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMozTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moz', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('platform_id');
            $table->unsignedBigInteger('pa')->nullable();
            $table->unsignedBigInteger('da')->nullable();
            $table->double('rank')->nullable();
            $table->unsignedBigInteger('links_in')->nullable();
            $table->unsignedBigInteger('equity')->nullable();

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
        Schema::table('moz', function (Blueprint $table) {
            $table->dropForeign('moz_platform_id_foreign');
        });
        Schema::dropIfExists('moz');
    }
}
