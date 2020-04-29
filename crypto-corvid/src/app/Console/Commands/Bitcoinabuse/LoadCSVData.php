<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcoinabuse;

use App\Console\Base\Common\CryptoParser;
use App\Console\Base\Common\GraylogTypes;
use App\Console\Base\KafkaClient\ProducerFeatures;
use App\Console\Constants\Bitcoinabuse\BitcoinabuseCommands;
use App\Console\Constants\Common\CommonKafka;
use App\Console\Constants\Common\CryptoCurrency;
use App\Models\Kafka\ParsedAddress;
use App\Models\Pg\Category;
use Illuminate\Support\Facades\Storage;

class LoadCSVData extends CryptoParser {
    use ProducerFeatures;
    
    protected $signature = BitcoinabuseCommands::LOAD_CSV_DATA . ' {verbose=1} {past=1d} {dateTime?}';
    protected $description = BitcoinabuseCommands::LOAD_CSV_DATA_DESC;
    
    const TMP_CSV_FILE = 'bcs_tmp.csv';
    const DOWNLOAD_URL = BitcoinabuseCommands::BITCOINABUSE_URL . '/api/download/';
    const REPORTS_URL = BitcoinabuseCommands::BITCOINABUSE_URL . '/reports/';
    
    public function handle() {
        $this->outputTopic = CommonKafka::SCRAPE_RESULTS_TOPIC;
        $this->serviceName = BitcoinabuseCommands::LOAD_CSV_DATA;

        parent::handle();

        $this->initProducer();

        $past = $this->argument('past');
        $this->loadData($past);
        $this->infoGraylog("All work done", GraylogTypes::INFO);
    }
    
    private function loadData(string $past) {
        $token = env('BITCOIN_ABUSE_TOKEN', '');
        if ($token === '') {
            $this->errorGraylog('BITCOIN_ABUSE_TOKEN not found!');
            exit(1);
        }
        $url = self::DOWNLOAD_URL . $past . '?api_token=' . $token;

        $data = $this->getPageContent($url);
        Storage::disk('local')->put(self::TMP_CSV_FILE, $data);

        // MAKE SURE "/statics" IS STILL THE VALID PATH!
        $handle = fopen('storage/statics/' . self::TMP_CSV_FILE, "r");
        // skip CSV header
        fgetcsv($handle);
        while($row = fgetcsv($handle)) {
            // id, address, type_id, type_other, abuser, description, from_country, from_country_code, created_at           
            list(, $address, $type_id, , $owner, $description) = $row;
            $url = self::REPORTS_URL . $address;
            $parsedAddress = new ParsedAddress(
                $owner,
                $url,
                $description,
                BitcoinabuseCommands::BITCOINABUSE_URL,
                $address,
                CryptoCurrency::BTC["code"],
                $this->mapCategory($type_id)
            );
            $tsvData = $parsedAddress->createTSVData();
            $this->kafkaProduce($tsvData);
        }
        fclose($handle);
    }

    private function mapCategory(string $type_id): string {
        switch ($type_id) {
            case "1": return Category::CAT_13;
            case "2": return Category::CAT_10;
            case "3": return Category::CAT_5;
            case "4": return Category::CAT_11;
            case "5": return Category::CAT_14;
            case "99": return Category::CAT_1;
            default: return Category::CAT_1;
        }
    }
}