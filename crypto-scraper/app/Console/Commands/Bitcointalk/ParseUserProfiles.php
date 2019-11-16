<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Models\Pg\Bitcointalk\UserProfile;

class ParseUserProfiles extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_user_profiles {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses all user profiles from DB.';

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
        parent::handle();
        
        $userProfiles = UserProfile::getAllUnParsed();
        if (count($userProfiles)) {
            foreach ($userProfiles as $userProfile) {
                $parsed = $this->call("bitcointalk:parse_user_profile", [
                    "url" => $userProfile->getAttribute(UserProfile::COL_URL),
                    "verbose" => $this->verbose,
                    "dateTime" => $this->dateTime
                ]);

                if ($parsed) {
                    $userProfile->setAttribute(UserProfile::COL_PARSED, true);
                    $userProfile->save();
                }
            }
            return 1;
        } else {
            $this->printRedLine("No unparsed user profiles found!");
            return 0;
        }
    }
}
