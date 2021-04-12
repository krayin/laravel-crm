<?php

namespace Webkul\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class Database extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(Attribute::class);
    }
}
