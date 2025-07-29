<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipment_reports', function (Blueprint $table) {
            // Ajouter les champs manquants
            $table->string('reporter_ip')->nullable();
            $table->text('user_agent')->nullable();
            
            // Modifier contact_info pour être de type json
            $table->json('contact_info')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_reports', function (Blueprint $table) {
            $table->dropColumn(['reporter_ip', 'user_agent']);
            $table->string('contact_info')->nullable()->change();
        });
    }
};
