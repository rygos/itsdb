<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_matrices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('position')->default(0);
            $table->text('category')->nullable();
            $table->text('function_name')->nullable();
            $table->text('product')->nullable();
            $table->text('short_description')->nullable();
            $table->text('synonyms')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_matrices');
    }
};
