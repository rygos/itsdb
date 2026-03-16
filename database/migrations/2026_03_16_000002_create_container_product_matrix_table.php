<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('container_product_matrix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_matrix_id')->constrained('product_matrices')->cascadeOnDelete();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_matrix_id', 'container_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('container_product_matrix');
    }
};
