<?php
require_once ROOT . '/application/functions.php';

abstract class DeckImageFetcher {

    const SAVE_DIR = '/temp';

    protected $xml_document = null;
    protected $url = null;

    public function __construct($url) {
        $this->url = $url;

        ob_start();
        $this->xml_document = new DOMDocument();
        $this->xml_document->loadHTMLFile($url);
        ob_clean();
    }

    public abstract function fetchCards();

    /**
     * Currently unused (Saves card-images from a given url)
     * @param $url
     * @return string
     * @throws Exception
     */
    public static function fetchImage($url) {

        // Init curl
        $ch = curl_init($url);

        // Set curl options
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

        // Execute curl
        $raw = curl_exec($ch);

        // Generate random filename
        $filename = sprintf(
            '%s/%s/%s.jpg',
            ROOT,
            static::SAVE_DIR,
            uniqid('mtgcard_', true)
        );

        // Remove file if it exists
        if (file_exists($filename)) {
            unlink( $filename);
        }

        // Open filestream
        $fp = fopen(
            $filename,
            'x'
        );

        // Write file and close filestream
        fwrite($fp, $raw);
        fclose($fp);

        // Make sure file exists
        if (!file_exists($filename)) {
            throw new Exception('File could not be saved');
        }

        return $filename;
    }

    /**
     * Filter out land cards from result
     *
     * @param $cards
     * @return array
     */
    protected static function filterCards($cards) {
        return array_filter($cards, function ($v, $k) {
            switch ($v->name) {
                case 'Island':
                case 'Mountain':
                case 'Plains':
                case 'Forest':
                case 'Swamp':
                    return false;
                default:
                    return true;
            }
        }, ARRAY_FILTER_USE_BOTH);
    }

}