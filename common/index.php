<?php

ob_start();

require_once("../setting.php");


require_once("../library/portable-utf8.php");
    
require_once('../library/stemming/wordstemmer.php');
    
require_once('../library/stemming/search.php');

require_once("../library/Classifier/src/CategoryAnalyzer.php");
    
require_once('../library/Html2Text.php');

require_once('../library/simple_html_dom.php');

require_once('../library/whois.php');

require_once("../library/mr-clean-master/Scrubber/BaseScrubber.php");
require_once("../library/mr-clean-master/Scrubber/Boolean.php");
require_once("../library/mr-clean-master/Scrubber/Html.php");
require_once("../library/mr-clean-master/Scrubber/NullIfRepeated.php");
require_once("../library/mr-clean-master/Scrubber/Nullify.php");
require_once("../library/mr-clean-master/Scrubber/StripCssAttributes.php");
require_once("../library/mr-clean-master/Scrubber/StripPhoneNumber.php");

require_once("../library/mr-clean-master/MrClean.php");
require_once("../library/mr-clean-master/Sanitizer.php");

use UAParser_a\Parser_a;
require_once("../library/uap-php-master/Parser.php");
require_once("../library/uap-php-master/Command.php");
require_once("../library/uap-php-master/Exception.php");
require_once("../library/uap-php-master/Result.php");
require_once("../library/uap-php-master/Util.php");
require_once("../library/uap-php-master/resources/regexes.php");
require_once("../library/uap-php-master/AbstractParser.php");
require_once("../library/uap-php-master/UserAgentParser.php");
require_once("../library/uap-php-master/OperatingSystemParser.php");
require_once("../library/uap-php-master/DeviceParser.php");

    
require_once("../library/Text-Statistics-master/Maths.php");
require_once("../library/Text-Statistics-master/Pluralise.php");
require_once("../library/Text-Statistics-master/Resource.php");
require_once("../library/Text-Statistics-master/Syllables.php");
require_once("../library/Text-Statistics-master/Text.php");
require_once("../library/Text-Statistics-master/TextStatistics.php");
require_once("../library/Text-Statistics-master/resources/DaleChallWordList.php");
require_once("../library/Text-Statistics-master/resources/SpacheWordList.php");

require_once("../library/color-extractor-master/Color.php");
require_once("../library/color-extractor-master/ColorExtractor.php");
require_once("../library/color-extractor-master/Palette.php");
    
require_once("../library/mexitek-phpColors/Color.php");
    
require_once('../library/summary/TextRankFacade.php');
require_once('../library/summary/Tool/Graph.php');
require_once('../library/summary/Tool/Parser.php');
require_once('../library/summary/Tool/Score.php');
require_once('../library/summary/Tool/Summarize.php');
require_once('../library/summary/Tool/Text.php');
require_once('../library/summary/Tool/StopWords/StopWordsAbstract.php');
    

require_once('../library/summary/Tool/StopWords/English.php');
require_once('../library/summary/Tool/StopWords/French.php');
require_once('../library/summary/Tool/StopWords/German.php');
require_once('../library/summary/Tool/StopWords/Italian.php');
require_once('../library/summary/Tool/StopWords/Norwegian.php');
require_once('../library/summary/Tool/StopWords/Russian.php');
require_once('../library/summary/Tool/StopWords/Spanish.php');
    

require_once('../library/phrase-extractor/AbstractStopwordProvider.php');
require_once('../library/phrase-extractor/RakePlus.php');
require_once('../library/phrase-extractor/StopwordArray.php');
require_once('../library/phrase-extractor/StopwordsPatternFile.php');
require_once('../library/phrase-extractor/StopwordsPHP.php');

require_once('../library/Neural_Net/Neural_Net.php');

require_once("../library/Syllable/src/Syllable.php");
require_once("../library/Syllable/src/Source/Source.php");
require_once("../library/Syllable/src/Source/File.php");

require_once("../library/Syllable/src/Cache/Cache.php");
require_once("../library/Syllable/src/Cache/File.php");
require_once("../library/Syllable/src/Cache/Json.php");
require_once("../library/Syllable/src/Cache/Serialized.php");

require_once("../library/Syllable/src/Hyphen/Hyphen.php");
require_once("../library/Syllable/src/Hyphen/Text.php");
require_once("../library/Syllable/src/Hyphen/Dash.php");
require_once("../library/Syllable/src/Hyphen/Entity.php");
require_once("../library/Syllable/src/Hyphen/Soft.php");
require_once("../library/Syllable/src/Hyphen/ZeroWidthSpace.php");

require_once("../common.php");

$manipulation = new manipulation();

$verifier = new verifier();

$generator = new generator_one();

$crawler = new crawler();

$files = new files();

$q = mysqli($_GET['q']);

$action = mysqli($_GET['action']);

if($user->login_status == 1 && $action == "setting") {

	require_once("auth/setting.php");

}
else if($action  == "validate") {

	require_once("auth/validate.php");

}
else if($action == "privacy") {

    require_once('pages/privacy.php');

}
else if($action == "tos") {

    require_once('pages/tos.php');

}
else if($action == "ddos") {

    require_once('pages/ddos.php');

}
else if($action == "redirect") {

    require_once('pages/redirect.php');

}
else {

	require_once("auth/auth.php");

}

?>
