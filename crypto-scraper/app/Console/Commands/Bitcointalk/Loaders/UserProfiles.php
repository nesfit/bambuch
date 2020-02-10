<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Loaders;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Models\Pg\Bitcointalk\UserProfile;

class UserProfiles extends BitcointalkParser {
    use UrlValidations;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::LOAD_USER_PROFILES .' {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads user profile urls from a topic page url.';

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

        if (self::topicPageValid($this->url)) {
            $userProfiles = $this->loadUserProfiles($this->url);
            $this->saveUserProfiles($userProfiles);
            return 1;
        } else {
            $this->printRedLine('Invalid topic page url: ' . $this->url);
            return 0;
        }
    }

    private function loadUserProfiles(string $url): array {
        return $this->getLinksFromPage($url, 'action=profile');
    }

    private function saveUserProfiles(array $userProfiles): void {
        $profilesCount = count($userProfiles);
        $progressBar = $this->output->createProgressBar($profilesCount);
        foreach ($userProfiles as $key => $page) {
            if (!UserProfile::exists($page)) {
                $topicPage = new UserProfile();
                $topicPage->setAttribute(UserProfile::COL_URL, $page);
                $topicPage->setAttribute(UserProfile::COL_PARSED, false);
                $topicPage->save();
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        print("\n");
    }

    private static function topicPageValid(string $url): bool {
        return self::pageEntityValid('topic', $url);
    }
}
