<?php
define('ROOT', dirname(__DIR__));

include_once ROOT . '/vendor/fpdf/fpdf.php';
include_once ROOT . '/application/DeckCard.php';
include_once ROOT . '/application/TappedOutFetcher.php';

if (isset($_GET['deck_url'])) {
    set_time_limit ( 0);

    $errors = array();

    // Make sure url is right
    $tapped_regex = '/^(http+s?:\/\/)?tappedout.net\/mtg-decks\/+(.*)$/';
    if (!preg_match($tapped_regex, trim($_GET['deck_url']))) {
        $errors[] = 'Incorrect format of url. Needs to be https://tappedout.net/mtg-decks/your-deck';
    }

    if (!count($errors)) {
        $fetcher = new TappedOutFetcher(trim($_GET['deck_url']));
        $cards = $fetcher->fetchCards();

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial');

        $padding_x = 10;
        $padding_y = 10;

        $x = 0;
        $y = 0;

        while ($card = next($cards)) {
            $image_url = sprintf('http://%s', $card->image_url);

            for ($j = 0; $j < $card->amount; $j++) {
                $pdf->Image(
                    $image_url,
                    $x + $padding_x,
                    $y + $padding_y,
                    DeckCard::CARD_WIDTH,
                    DeckCard::CARD_HEIGHT
                );

                $x += DeckCard::CARD_WIDTH;
                if ($x >= DeckCard::CARD_WIDTH * 3) {
                    $y += DeckCard::CARD_HEIGHT;
                    $x = 0;
                }

                if ($y >= DeckCard::CARD_HEIGHT * 3) {
                    $pdf->AddPage();
                    $y = 0;
                }
            }
        }

        $pdf->Output('cards.pdf', 'I');
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>MTG Proxy Deck Builder</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="wrapper">
            <div class="lds-roller" id="loader">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>

            <?php if (isset($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="error">
                        <?php echo $error; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <form action="" method="GET" class="deck-search" onsubmit="document.getElementById('loader').classList.add('visible');">
                <input type="text" name="deck_url" placeholder="Url to tappedout"><!--
                --><button type="submit">Get PDF</button>
            </form>
        </div>
    </body>
</html>