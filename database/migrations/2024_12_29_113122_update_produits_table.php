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
        Schema::table('produits', function (Blueprint $table) {
            $table->renameColumn('nom', 'title');
            $table->decimal('rating', 3, 2)->nullable()->after('image');
            $table->decimal('discount', 5, 2)->nullable()->after('rating');
            $table->string('image')->after('description')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->renameColumn('title', 'nom');
            $table->dropColumn('rating');
            $table->dropColumn('discount');
            $table->dropColumn('image');
        });
    }
};
