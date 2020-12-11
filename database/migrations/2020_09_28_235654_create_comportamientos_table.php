<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComportamientosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comportamientos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('estudiante_id')->unsigned();
            $table->integer('tipo_comportamiento_id')->unsigned();
            $table->string('titulo');
            $table->string('descripcion');
            $table->date('fecha');
            $table->string('emisor');
            $table->string('multimedia', 5000)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('estudiante_id')->references('id')->on('estudiantes');
            $table->foreign('tipo_comportamiento_id')->references('id')->on('tipo_comportamientos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comportamientos');
    }
}
