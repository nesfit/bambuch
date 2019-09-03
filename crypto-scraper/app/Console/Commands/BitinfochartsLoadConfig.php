<?php
/**
 * Project: BitInfoCharts parser
 * Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz
 */

namespace App\Console\Commands;

use DateTime;
use App\Console\Utils;
use App\Console\Config;
use Illuminate\Support\Facades\Artisan;

error_reporting(E_ALL ^ E_WARNING);

class BitinfochartsLoadConfig extends GlobalCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitinfocharts:load {verbose=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse crypto addresses from bitinfocharts.com';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {        
        $this->verbose = $this->argument("verbose");
        $pages = Config::getPages();

        $start  = new DateTime();

        $this->line("<fg=green>Starting with output verbosity: ". $this->verbose .".</>");

        $this->parsePages($pages);
        
        $end = new DateTime();        
        $diff = $start->diff($end);
        $this->line("<fg=green>All work done in: ". $diff->format( '%H:%I:%S' ) .".</>");
    }

    /**
     * Core function. Parses configs, pages and inserts data into database.
     *
     * @param array $pages Configs with url and cryptocurrency data
     * @return void
     */
    private function parsePages($pages) {
        foreach ($pages as $page) {
            for ($i=1; $i < $page['maxPage']; $i++) {
                $url = Config::createPageUrl($page['path'], $i);
                $this->call("bitinfocharts:parse", [
                    "url" => $url,
                    "verbose" => $this->verbose
                ]);
            }
        }
    }
}
