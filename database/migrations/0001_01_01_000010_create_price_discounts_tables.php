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
        Schema::create('price_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_id')->index();
            $table->integer('qty');
            $table->double('discount')->nullable();
            $table->timestamps();

            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_discounts');
    }
};
