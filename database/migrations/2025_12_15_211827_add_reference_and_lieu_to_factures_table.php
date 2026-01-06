<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('factures', function (Blueprint $table) {
            if (!Schema::hasColumn('factures', 'reference')) {
                $table->string('reference')->nullable()->after('id');
            }

            if (!Schema::hasColumn('factures', 'lieu')) {
                $table->string('lieu')->default('Casa')->after('reference');
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('factures', function (Blueprint $table) {
            if (Schema::hasColumn('factures', 'reference')) {
                $table->dropColumn('reference');
            }

            if (Schema::hasColumn('factures', 'lieu')) {
                $table->dropColumn('lieu');
            }
        });
    }
};
