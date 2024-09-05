<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
                    'code'            => 'user_id',
                    'name'            => trans('installer::app.seeders.attributes.persons.sales-owner'),
                    'type'            => 'lookup',
                    'entity_type'     => 'persons',
                    'lookup_type'     => 'users',
                    'validation'      => null,
                    'sort_order'      => '5',
                    'is_required'     => '0',
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
    public function down(): void {}
};
