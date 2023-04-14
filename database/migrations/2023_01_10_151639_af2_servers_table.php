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
            $table->longText('docker_compose_raw')->nullable();
            $table->longText('env_raw')->nullable();
            $table->longText('server_cert_raw')->nullable();
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
            $table->dropColumn('docker_compose_raw');
            $table->dropColumn('env_raw');
            $table->dropColumn('server_cert_raw');
        });
    }
};
