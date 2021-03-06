<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMozDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moz_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('platform_id');
            $table->integer('pa')->nullable();
            $table->integer('da')->nullable();
            $table->double('rank')->nullable();
            $table->integer('links_in')->nullable();
            $table->integer('equity')->nullable();
            $table->timestamps();

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
        Schema::table('moz_data', function (Blueprint $table) {
            $table->dropForeign('moz_data_platform_id_foreign');
        });
        Schema::dropIfExists('moz_data');
    }
}
