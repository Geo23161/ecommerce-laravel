<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('produits', function (Blueprint $table) {
        $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('produits', function (Blueprint $table) {
        $table->dropForeign(['seller_id']);
        $table->dropColumn('seller_id');
    });
}

};
