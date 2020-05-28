<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

use DOMDocument;
use DOMXPath;

class Utils {
    /**
     * Uses curl to get DOM from a url.
     * @deprecated 
     *
     * @param string $url
     * @return string
     */
    public static function getContentFromURL($url) {
        sleep(1);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        $data = curl_exec($curl);
        
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return ($httpcode >= 200 && $httpcode < 300) ? $data : "";
    }

    /**
     * Turns plain DOM string into DOMXPath.
     *
     * @param string $data Plain DOM string
     * @return DOMXPath DOMXPath instance
     */
    public static function getDOMXPath($data) {
        $doc = new DOMDocument();
        $doc->loadHTML($data);
        return new DOMXPath($doc);
    }

    public static function cleanText(string $text) {
        $ascii = iconv("UTF-8", "UTF-8//TRANSLIT", $text);
        return str_replace(["\r", "\n", "\t"], ' ', $ascii);
    }
}
   