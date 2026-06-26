<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->string('dni')->primary();
            $table->string('name');
            $table->longText('observation')->nullable();
            $table->longText('question1')->nullable();
            $table->longText('question2')->nullable();
            $table->longText('question3')->nullable();
            $table->string('education_level');
            $table->string('pos_to_apply');
            $table->string('search_company');
            $table->string('email');
            $table->boolean('in_charge')->default(false);
            $table->text('anwers')->nullable();
            $table->boolean('played')->default(false);
            $table->integer('win')->default(0);
            $table->integer('type');
            $table->string('date_of_birth');
            $table->integer('age');
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
        Schema::dropIfExists('workers');
    }
}
