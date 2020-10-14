<?php



require_once("Scrubber/BaseScrubber.php");
require_once("Scrubber/Boolean.php");
require_once("Scrubber/Html.php");
require_once("Scrubber/NullIfRepeated.php");
require_once("Scrubber/Nullify.php");
require_once("Scrubber/ScrubberInterface.php");
require_once("Scrubber/StripCssAttributes.php");
require_once("Scrubber/StripPhoneNumber.php");

require_once("MrClean.php");
require_once("Sanitizer.php");


$cleaner = new MrClean\MrClean();
    
$text =  file_get_contents("https://github.com/search?q=mrclean");

$result  = $cleaner->scrubbers(['strip_tags'])->scrub($text);
$result = $cleaner->scrubbers(['trim'])
                            ->scrub($result);
print_r($result);