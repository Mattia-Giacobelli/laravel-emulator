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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('region', ['us', 'jp', 'eu', 'au', 'korea', 'asia'])->nullable();
            $table->enum('category', [
                'nintendo-switch', 
                'nintendo-64', 
                'nintendods', 
                'nintendods-cart', 
                'dendy', 
                'tetris'
            ])->nullable();
            $table->text('description')->nullable();
            $table->string('file_path'); // Es: 'roms/abc123.nes'
            $table->timestamps();

            // Indici per ottimizzazione query
            $table->index(['title', 'region']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
