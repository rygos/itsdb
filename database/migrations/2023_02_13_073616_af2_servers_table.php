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
        Schema::table('servers', function (Blueprint $table) {
            $table->integer('cert_server_ok')->default(0);
            $table->integer('cert_intermediate_ok')->default(0);
            $table->integer('cert_root_ok')->default(0);
            $table->integer('cert_key_ok')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('cert_server_ok');
            $table->dropColumn('cert_intermediate_ok');
            $table->dropColumn('cert_root_ok');
            $table->dropColumn('cert_key_ok');
        });
    }
};
