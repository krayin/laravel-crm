<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
                    'code'            => 'name',
                    'name'            => trans('installer::app.seeders.attributes.warehouses.name'),
                    'type'            => 'text',
                    'entity_type'     => 'warehouses',
                    'lookup_type'     => null,
                    'validation'      => null,
                    'sort_order'      => '1',
                    'is_required'     => '1',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'description',
                    'name'            => trans('installer::app.seeders.attributes.warehouses.description'),
                    'type'            => 'textarea',
                    'entity_type'     => 'warehouses',
                    'lookup_type'     => null,
                    'validation'      => null,
                    'sort_order'      => '2',
                    'is_required'     => '0',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'contact_name',
                    'name'            => trans('installer::app.seeders.attributes.warehouses.contact-name'),
                    'type'            => 'text',
                    'entity_type'     => 'warehouses',
                    'lookup_type'     => null,
                    'validation'      => null,
                    'sort_order'      => '3',
                    'is_required'     => '1',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'contact_emails',
                    'name'            => trans('installer::app.seeders.attributes.warehouses.contact-emails'),
                    'type'            => 'email',
                    'entity_type'     => 'warehouses',
                    'lookup_type'     => null,
                    'validation'      => null,
                    'sort_order'      => '4',
                    'is_required'     => '1',
                    'is_unique'       => '1',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'contact_numbers',
                    'name'            => trans('installer::app.seeders.attributes.warehouses.contact-numbers'),
                    'type'            => 'phone',
                    'entity_type'     => 'warehouses',
                    'lookup_type'     => null,
                    'validation'      => 'numeric',
                    'sort_order'      => '5',
                    'is_required'     => '0',
                    'is_unique'       => '1',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'contact_address',
                    'name'            => trans('installer::app.seeders.attributes.warehouses.contact-address'),
                    'type'            => 'address',
                    'entity_type'     => 'warehouses',
                    'lookup_type'     => null,
                    'validation'      => null,
                    'sort_order'      => '6',
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
