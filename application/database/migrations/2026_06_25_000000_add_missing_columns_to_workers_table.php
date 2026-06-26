<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingColumnsToWorkersTable extends Migration
{
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->integer('countHelpViewPath')->default(0);
            $table->integer('countHelpAddTime')->default(0);
            $table->tinyInteger('emailsSended')->default(0);
            $table->integer('id_selectoras')->nullable();
        });
    }

    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn(['countHelpViewPath', 'countHelpAddTime', 'emailsSended', 'id_selectoras']);
        });
    }
}
