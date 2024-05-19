<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique()->nullable();
            $table->string('proudct_barcode')->unique()->nullable();
            $table->string('product_name');
            $table->string('product_description');
            $table->string('product_image')->nullable();
            $table->string('product_stock');
            $table->string('product_price');
            $table->string('product_category_id')->default('1');
            $table->string('product_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
