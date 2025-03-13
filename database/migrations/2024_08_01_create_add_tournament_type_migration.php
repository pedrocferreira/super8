<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alterar o enum de tipos de torneio para incluir o novo tipo
        DB::statement("ALTER TABLE tournaments MODIFY COLUMN type ENUM('super_8_individual', 'super_12_fixed_pairs', 'super_12_selected_pairs')");
    }

    public function down(): void
    {
        // Retornar à definição original
        DB::statement("ALTER TABLE tournaments MODIFY COLUMN type ENUM('super_8_individual', 'super_12_fixed_pairs')");
    }
}; 