<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();

            // produit concerné
            $table->foreignId('produit_id')
                  ->constrained('produits')
                  ->onDelete('cascade');

            // type de mouvement
            $table->enum('type', ['entree', 'sortie', 'ajustement']);

            // quantité déplacée
            $table->integer('quantite');

            // stock avant et après
            $table->integer('stock_avant');
            $table->integer('stock_apres');

            // raison du mouvement
            $table->string('motif')->nullable();

            // utilisateur (حتى إلا كان واحد)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
