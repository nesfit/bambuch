<?php
/**
 * Project: BitInfoCharts parser
 * Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz
 */

use Illuminate\Database\Seeder;
use App\Models\Pg\Category;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $categories = DB::table('categories')->get();
        if (sizeof($categories) == 0) {
            DB::table('categories')->insert(Category::CATEGORIES);
        }
    }
}
