<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('player_scores', function (Blueprint $table) {
            $table->integer('position')->nullable()->after('points');
        });
    }

    public function down()
    {
        Schema::table('player_scores', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
