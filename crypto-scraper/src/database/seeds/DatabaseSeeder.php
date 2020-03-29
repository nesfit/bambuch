<?php
/**
 * Project: BitInfoCharts parser
 * Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz
 */

use Illuminate\Database\Seeder;
use App\Models\Pg\Category;
use App\Models\Pg\Source;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $categories = Category::all();
        if (sizeof($categories) === 0) {
            DB::table(Category::TABLE)->insert(Category::CATEGORIES);
        }
        
        $sources = Source::all();
        if (sizeof($sources) === 0) {
            DB::table(Source::TABLE)->insert(Source::SOURCES);
        }
    }
}
