<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->unsignedTinyInteger('permission_administration')->default(0)->after('last_login_at');
            $table->unsignedTinyInteger('permission_product_matrix')->default(0)->after('permission_administration');
            $table->unsignedTinyInteger('permission_compose')->default(0)->after('permission_product_matrix');
            $table->unsignedTinyInteger('permission_hours')->default(0)->after('permission_compose');
            $table->unsignedTinyInteger('permission_customers')->default(0)->after('permission_hours');
            $table->unsignedTinyInteger('permission_projects')->default(0)->after('permission_customers');
            $table->unsignedTinyInteger('permission_calendar')->default(0)->after('permission_projects');
        });

        DB::table('users')->update([
            'permission_administration' => 3,
            'permission_product_matrix' => 3,
            'permission_compose' => 3,
            'permission_hours' => 3,
            'permission_customers' => 3,
            'permission_projects' => 3,
            'permission_calendar' => 3,
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'permission_administration',
                'permission_product_matrix',
                'permission_compose',
                'permission_hours',
                'permission_customers',
                'permission_projects',
                'permission_calendar',
            ]);
        });
    }
};
