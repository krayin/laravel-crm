<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveJobTitleFromAttributes extends Migration
{
    /**
     * Store attributes for restoration in down()
     */
    private $attributes = [];

    /**
     * Run the migrations.
     */
    public function up()
    {
        // Store existing attributes before deletion
        $this->attributes = DB::table('attributes')
            ->whereIn('code', ['job_title', 'emails', 'organization_id'])
            ->where('entity_type', 'persons')
            ->get()
            ->toArray();

        DB::table('attributes')
            ->whereIn('code', ['job_title', 'emails', 'organization_id'])
            ->where('entity_type', 'persons')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Only insert if the attribute doesn't already exist
        foreach ($this->attributes as $attribute) {
            DB::table('attributes')
                ->where('code', $attribute->code)
                ->where('entity_type', $attribute->entity_type)
                ->doesntExist()
                && DB::table('attributes')->insert((array) $attribute);
        }
    }
}