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
        Schema::table('cart_items', function (Blueprint $table) {
            // Add the order_id column
            $table->foreignId('order_id')->nullable()->after('product_id'); // Make it nullable if you want to allow cart items without orders
            
            // If you want the foreign key constraint to ensure data integrity
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Remove the foreign key and the column
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
