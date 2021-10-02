<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsertExpectedClosedDateColumnInAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('attributes')->insert([
            [
                'id'              => '7',
                'code'            => 'expected_close_date',
                'name'            => 'Expected Close Date',
                'type'            => 'date',
                'entity_type'     => 'leads',
                'lookup_type'     => NULL,
                'validation'      => NULL,
                'sort_order'      => '8',
                'is_required'     => '0',
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
