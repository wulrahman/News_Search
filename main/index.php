<?php

require_once("../setting.php");
    
require_once('../library/stemming/wordstemmer.php');
    
require_once('../library/stemming/search.php');

require_once("../library/portable-utf8.php");

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

$path  = "../library/Syllable";

$manipulation = new manipulation();

$verifier = new verifier();

$crawler = new crawler($path);

$files = new files();

$generator = new generator_one();
    
$get_content_article = new get_content_article();
    
$q = mysqli($_GET['q']);

$action = mysqli($_GET['action']);

if($action == "curl") {
    
    require_once('pages/curl.php');
    
}
else if($action == "getcontent") {
    
    require_once('pages/getcontent.php');
    
}
else if($action == "analysis") {
    
    require_once('pages/analysis.php');
    
}
else if($action == "category") {
    
    require_once('pages/category.php');
    
}
else if($action == "post") {
    
    require_once('pages/post.php');
    
}
else if($action == "json") {
    
    require_once('pages/json.php');
    
}

else if($action == "model") {
    
    require_once('pages/model.php');
    
}

else if($action == "analysis") {
    
    require_once('pages/analysis.php');
    
}

else if($action == "test_model") {
    
    require_once('pages/test_model.php');
    
}
else if($action == "view") {
    
    require_once('pages/view.php');
    
}
else if($action == "404") {
    
    require_once('../common/pages/404.php');
    
}
else if(isset($_GET['q']) && !$verifier->space($_GET['q'])) {
    
    require_once('pages/search.php');
    
}
else {

    require_once('pages/main.php');
    
}

?>
