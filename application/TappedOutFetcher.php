<?php
require_once ROOT . '/application/DeckImageFetcher.php';

class TappedOutFetcher extends DeckImageFetcher {

    public function fetchCards($include_lands = false) {
        $container = array();
        while (empty($container)) {
            $container = getElementsByClass($this->xml_document, 'div', 'board-container');
        }

        $cards = getElementsByClass($container[0], 'li', 'member');

        $ret_cards = array();
        foreach ($cards as $card) {
            $card_html = $this->xml_document->saveHTML($card);

            // Find image url in card element
            preg_match('/data-image="([^"]*)"/', $card_html,  $image_url);
            $image_url = array_pop($image_url);
            $image_url = substr($image_url, 2);

            // Find card quantity in card element
            preg_match('/data-qty="([^"]*)"/', $card_html, $card_amount);
            $card_amount = array_pop($card_amount);

            // Find card name in card element
            preg_match('/data-name="([^"]*)"/', $card_html, $card_name);
            $card_name = array_pop($card_name);

            $deck_card = new DeckCard();
            $deck_card->image_url = $image_url;
            $deck_card->amount = $card_amount;
            $deck_card->name = $card_name;

            $ret_cards[] = $deck_card;
        }

        $cards = getElementsByClass($container[0], 'img', 'commander-img');
        foreach ($cards as $card) {
            $card_html = $this->xml_document->saveHTML($card);

            preg_match('/data-card-img="([^"]*)"/', $card_html, $image_url);
            $image_url = array_pop($image_url);
            $image_url = substr($image_url, 2);

            $deck_card = new DeckCard();
            $deck_card->image_url = $image_url;
            $deck_card->amount = 1;
            $deck_card->name = '';

            $ret_cards[] = $deck_card;
        }

        if (!$include_lands) {
            $ret_cards = self::filterCards($ret_cards);
        }

        return $ret_cards;
    }

}