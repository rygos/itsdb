<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('container_import_aliases', function (Blueprint $table) {
            $table->id();
            $table->text('source_name');
            $table->foreignId('container_id')->nullable()->constrained('containers')->nullOnDelete();
            $table->boolean('ignore_on_import')->default(false);
            $table->timestamps();

            $table->unique(['source_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('container_import_aliases');
    }
};
