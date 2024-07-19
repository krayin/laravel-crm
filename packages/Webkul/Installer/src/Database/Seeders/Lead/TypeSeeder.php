<?php

namespace Webkul\Installer\Database\Seeders\Lead;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('lead_types')->delete();

        $now = Carbon::now();

        $defaultLocale = $parameters['locale'] ?? config('app.locale');

        DB::table('lead_types')->insert([
            [
                'id'         => 1,
                'name'       => trans('installer::app.seeders.lead.type.new-business', [], $defaultLocale),
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 2,
                'name'       => trans('installer::app.seeders.lead.type.existing-business', [], $defaultLocale),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
