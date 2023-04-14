<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->longText('intermediate_cert_raw')->nullable();
            $table->longText('root_cert_raw')->nullable();
            $table->longText('private_key_raw')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('intermediate_cert_raw');
            $table->dropColumn('root_cert_raw');
            $table->dropColumn('private_key_raw');
        });
    }
};
