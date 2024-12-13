<?php

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;

require __DIR__ . '/vendor/autoload.php';

include 'WordGest.php';
include 'GridGest.php';

$wordGest = new WordGest();
$gridGest = new GridGest();

$wordSize = 0;


//$size = 6;
//
//$wordGest->createFilteredArray("l", $size);
//$wordGest->filterArrayByLetterPos("t", 3);
//$wordGest->filterArrayByLetterPos("e", 2);
//
//$wordGest->filterArrayByLetterNotInWord("i");
//$wordGest->filterArrayByLetterNotInWord("r");
//$wordGest->filterArrayByLetterNotInWord("s");
//$wordGest->filterArrayByLetterNotInWord("a");
////$wordGest->filterArrayByLetterInWordBadPosition("r", 2);
////
////$wordGest->filterArrayByLetterInPosition("e", 7);
////$wordGest->filterArrayByLetterInPosition("e", 7);
//
//var_dump($wordGest->getFilteredWords());




$browserFactory = new BrowserFactory();

// starts headless Chrome
$browser = $browserFactory->createBrowser([
    'headless' => false, // disable headless mode
    'keepAlive' => true
]);

try {
    // creates a new page and navigate to an URL
    $page = $browser->createPage();

    echo "Chargement de la page...\n";
    $page->navigate('https://motus.absolu-puzzle.com/')->waitForNavigation(Page::LOAD, 30000);

    // On lui applique le cookie require de consentement
    $coockieVal = "%5Bnull%2Cnull%2Cnull%2C%5B%22CQJeFwAQJeFwAEsACBFRBTFoAP_gAEPgAAqIINJD7C7FbSFCwH5zaLsAMAhHRsAAQoQAAASBAmABQAKQIAQCgkAQFASgBAACAAAAICRBIQIECAAAAUAAQAAAAAAEAAAAAAAIIAAAgAEAAAAIAAACAIAAEAAIAAAAEAAAmAgAAIIACAAAgAAAAAAAAAAAAAAAAgCAAAAAAAAAAAAAAAAAAQOhSD2F2K2kKFkPCmwXYAYBCujYAAhQgAAAkCBMACgAUgQAgFJIAgCIFAAAAAAAAAQEiCQAAQABAAAIACgAAAAAAIAAAAAAAQQAABAAIAAAAAAAAEAQAAIAAQAAAAIAABEhCAAQQAEAAAAAAAQAAAAAAAAAAABAAA%22%2C%222~70.89.93.108.122.149.196.236.259.311.313.323.358.415.449.486.494.495.540.574.609.864.981.1029.1048.1051.1095.1097.1126.1205.1276.1301.1365.1415.1449.1514.1570.1577.1598.1651.1716.1735.1753.1765.1870.1878.1889.1958.1960.2072.2253.2299.2373.2415.2506.2526.2531.2568.2571.2575.2624.2677.2778~dv.%22%2C%222058723D-96C3-490E-A997-8A0243EC3D4A%22%5D%5D";
    $page->evaluate('document.cookie = "FCCDCF=' . $coockieVal . '; expires=Thu, 18 Dec 2026 12:00:00 UTC";');
    $page->navigate('https://motus.absolu-puzzle.com/')->waitForNavigation(Page::LOAD, 30000);

   // On attend le chargement de la page
    $page->evaluate('document.cookie')->waitForResponse(50000);
    echo "Page chargée\n";

    $wordSize = intval($page->getCookies()->findOneBy("name", "motus_nb_letters")->getValue());
    echo "wordSize : " . $wordSize;

    echo "Récupération de la grille...\n";
    $grilleMotus = $page->evaluate('document.querySelector(".motus-grille").innerHTML');
    $motusArray = $gridGest->formatGrid($grilleMotus->getReturnValue());
    echo "Grille récupérée\n";


    $firstLetter = $motusArray[0][0]["letter"];

//    -----------------------------------------------

    $wordGest->createFilteredArray($firstLetter, $wordSize);

    $randomWord = $wordGest->getRandomFilteredWord();
    echo "Mot trouvé : " . $randomWord . "\n";
    echo "Ecriture du mot...\n";
    for ($i = 0; $i < strlen($randomWord); $i++) {
        $page->keyboard()->press($randomWord[$i]);
        $page->keyboard()->release($randomWord[$i]);
        sleep(1);
    }

    $page->evaluate('document.getElementById("13").click()');



} catch (Exception $e) {
    var_dump($e);
}
