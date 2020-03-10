<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Runners;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use App\Console\Constants\BitcointalkCommands;
use App\Models\Pg\Bitcointalk\UserProfile;

class UserProfiles extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::RUN_USER_PROFILES .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs '. BitcointalkCommands::PARSE_USER_PROFILE .' on every unparsed user profile.';

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
        
        if($this->option("force")) {
            $this->printCyanLine("Force update!");
            UserProfile::setParsedToAll(false);
        }
        
        $userProfiles = UserProfile::getAllUnParsed();
        if (count($userProfiles)) {
            foreach ($userProfiles as $userProfile) {
                $parsed = $this->call(BitcointalkCommands::PARSE_USER_PROFILE, [
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

    protected function loadDataFromUrl(string $url): array {
        return [];
    }
}
