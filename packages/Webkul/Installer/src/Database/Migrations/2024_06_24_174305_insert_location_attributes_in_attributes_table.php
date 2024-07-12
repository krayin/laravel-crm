<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();
        
        DB::table('attributes')
            ->insert([
                [
                    'code'            => 'aisle',
                    'name'            => trans('installer::app.seeders.locations.aisle'),
                    'type'            => 'text',
                    'entity_type'     => 'locations',
                    'lookup_type'     => NULL,
                    'validation'      => NULL,
                    'sort_order'      => '1',
                    'is_required'     => '1',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'bay',
                    'name'            => trans('installer::app.seeders.locations.bay'),
                    'type'            => 'text',
                    'entity_type'     => 'locations',
                    'lookup_type'     => NULL,
                    'validation'      => NULL,
                    'sort_order'      => '2',
                    'is_required'     => '0',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'shelf',
                    'name'            => trans('installer::app.seeders.locations.shelf'),
                    'type'            => 'text',
                    'entity_type'     => 'locations',
                    'lookup_type'     => NULL,
                    'validation'      => NULL,
                    'sort_order'      => '3',
                    'is_required'     => '0',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'bin',
                    'name'            => trans('installer::app.seeders.locations.bin'),
                    'type'            => 'text',
                    'entity_type'     => 'locations',
                    'lookup_type'     => NULL,
                    'validation'      => NULL,
                    'sort_order'      => '4',
                    'is_required'     => '0',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'warehouse_id',
                    'name'            => trans('installer::app.seeders.locations.warehouse'),
                    'type'            => 'lookup',
                    'entity_type'     => 'locations',
                    'lookup_type'     => 'warehouses',
                    'validation'      => NULL,
                    'sort_order'      => '5',
                    'is_required'     => '1',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ],
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            //
        });
    }
};
