<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_matrices', function (Blueprint $table) {
            $table->string('import_key')->nullable()->after('id');
        });

        Schema::table('product_matrices', function (Blueprint $table) {
            $table->unique('import_key');
        });
    }

    public function down()
    {
        Schema::table('product_matrices', function (Blueprint $table) {
            $table->dropUnique(['import_key']);
            $table->dropColumn('import_key');
        });
    }
};
