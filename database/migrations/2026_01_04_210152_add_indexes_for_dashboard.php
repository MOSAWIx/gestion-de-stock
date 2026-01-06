<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {

    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->index('name');
            $table->index('reference');
            $table->index('quantite');
            $table->index('stock_min');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('facture_items', function (Blueprint $table) {
            $table->index('facture_id');
            $table->index('produit_id');
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['reference']);
            $table->dropIndex(['quantite']);
            $table->dropIndex(['stock_min']);
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('facture_items', function (Blueprint $table) {
            $table->dropIndex(['facture_id']);
            $table->dropIndex(['produit_id']);
        });
    }
};
