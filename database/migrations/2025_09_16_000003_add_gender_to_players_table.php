<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female'])->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            if (Schema::hasColumn('players', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};



