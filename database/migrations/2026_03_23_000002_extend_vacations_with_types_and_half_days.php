<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacations', function (Blueprint $table): void {
            $table->string('type')->default('urlaub')->after('user_id');
            $table->string('start_day_portion')->default('full')->after('end_date');
            $table->string('end_day_portion')->default('full')->after('start_day_portion');
            $table->unsignedInteger('day_units')->default(0)->after('days');
        });

        DB::table('vacations')->update([
            'type' => 'urlaub',
            'start_day_portion' => 'full',
            'end_day_portion' => 'full',
            'day_units' => DB::raw('days * 2'),
        ]);
    }

    public function down(): void
    {
        Schema::table('vacations', function (Blueprint $table): void {
            $table->dropColumn(['type', 'start_day_portion', 'end_day_portion', 'day_units']);
        });
    }
};
