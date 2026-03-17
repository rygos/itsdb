<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_kinds', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('operating_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->foreignId('server_kind_id')->nullable()->after('type')->constrained('server_kinds')->nullOnDelete();
            $table->foreignId('operating_system_id')->nullable()->after('server_kind_id')->constrained('operating_systems')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('operating_system_id');
            $table->dropConstrainedForeignId('server_kind_id');
        });

        Schema::dropIfExists('operating_systems');
        Schema::dropIfExists('server_kinds');
    }
};
