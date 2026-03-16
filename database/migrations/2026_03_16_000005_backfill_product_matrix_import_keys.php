<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            $rows = DB::table('product_matrices')
                ->orderBy('id')
                ->get();

            $grouped = [];

            foreach ($rows as $row) {
                $key = sha1(implode('|', [
                    mb_strtolower(trim((string) $row->category)),
                    mb_strtolower(trim((string) $row->function_name)),
                    mb_strtolower(trim((string) $row->product)),
                ]));

                $grouped[$key][] = $row;
            }

            foreach ($grouped as $importKey => $duplicates) {
                $keeper = end($duplicates);
                $keeperId = $keeper->id;

                $containerIds = DB::table('container_product_matrix')
                    ->whereIn('product_matrix_id', array_map(fn ($item) => $item->id, $duplicates))
                    ->pluck('container_id')
                    ->unique()
                    ->values()
                    ->all();

                DB::table('product_matrices')
                    ->where('id', $keeperId)
                    ->update([
                        'import_key' => $importKey,
                    ]);

                DB::table('container_product_matrix')
                    ->whereIn('product_matrix_id', array_map(fn ($item) => $item->id, $duplicates))
                    ->delete();

                foreach ($containerIds as $containerId) {
                    DB::table('container_product_matrix')->insert([
                        'product_matrix_id' => $keeperId,
                        'container_id' => $containerId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $duplicateIds = array_filter(
                    array_map(fn ($item) => $item->id, $duplicates),
                    fn ($id) => $id !== $keeperId
                );

                if (!empty($duplicateIds)) {
                    DB::table('product_matrices')
                        ->whereIn('id', $duplicateIds)
                        ->delete();
                }
            }
        });
    }

    public function down()
    {
        DB::table('product_matrices')->update([
            'import_key' => null,
        ]);
    }
};
