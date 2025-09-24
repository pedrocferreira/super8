<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->foreignId('season_id')->nullable()->after('id')->constrained('seasons')->nullOnDelete();
            $table->enum('category', ['male', 'female', 'mixed'])->default('mixed')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (Schema::hasColumn('tournaments', 'season_id')) {
                $table->dropConstrainedForeignId('season_id');
            }
            if (Schema::hasColumn('tournaments', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};



