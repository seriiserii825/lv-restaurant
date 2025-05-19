<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait TSeeder {
    public function truncateTable($tableName)
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table to clear existing data
        DB::table($tableName)->truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

