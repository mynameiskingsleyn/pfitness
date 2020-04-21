<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZipcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zipcodes', function (Blueprint $table) {
            //$table->id();
            $table->string('zip')->primary();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamps();
            $table->index(['zip','latitude','longitude']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zipcodes');
    }
}
