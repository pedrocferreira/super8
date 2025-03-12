<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->string('registration_code')->unique()->nullable()->after('status');
            $table->boolean('registration_open')->default(true)->after('registration_code');
        });
    }

    public function down()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['registration_code', 'registration_open']);
        });
    }
}; 