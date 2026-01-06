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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('reference')->unique();

            $table->integer('quantite')->default(0);

            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->decimal('prix_vente', 10, 2)->nullable();

            $table->text('description')->nullable();

            // Foreign key vers Category
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->integer('stock_min')->default(10);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
