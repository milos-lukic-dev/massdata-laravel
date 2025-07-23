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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('order_date');
            $table->string('channel');
            $table->string('sku')->index();
            $table->string('item_description')->nullable();
            $table->string('origin');
            $table->string('so_num');
            $table->double('cost');
            $table->double('shipping_cost');
            $table->double('total_price');
            $table->timestamps();

            $table->foreign('sku')->references('sku')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
