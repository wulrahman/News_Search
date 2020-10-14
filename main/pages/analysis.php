<?php

// don't let user kill the script by hitting the stop button 
//ignore_user_abort(true); 

// don't let the script time out 
//set_time_limit(0); 

// start output buffering
//ob_start();


ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

$page_content = array(0);


//$content_main_row->source_url ="https://www.bbc.co.uk/news/world-53454558";

//$content_main_row->source_url ="https://news.samsung.com/global/samsungs-signage-shines-at-milans-first-digital-fashion-week";


//$content_main_row->source_url ="https://twitter.com/i/events/1287107043271143424";

//$content_main_row->source_url = "https://www.theguardian.com/business/2020/jul/22/tesco-staff-in-nearly-2000-stores-to-clean-shops-after-contractors-axed";



$content_main_row->source_url = 'https://www.cartoonnetwork.co.uk/games';


$tage_extraction = new tage_extraction($path);

$color_class = new colorTests();

$words = $tage_extraction->get_word_list('../library/AI/words_ai_test.txt');

$word_array = $words['words'];

$word_array_i = $words['wordsi'];

$outputs_key = $words['outputs_key'];
    
$trained_data['category'] = unserialize(file_get_contents('../library/AI/category_ai_test.txt'));    
        
$trained_data['sentiment'] = unserialize(file_get_contents('../library/AI/sentiment_ai_test.txt'));

$trained_data['suffex'] = json_decode(unserialize(file_get_contents('../library/AI/tags_ai_suffex.txt')));

$parse = parse_url($content_main_row->source_url);

//$url = $manipulation->main_domain($content_main_row->source_url);

$url = mysqli($manipulation->fix_url($content_main_row->source_url, $parse));

$array = $crawler->url_info(strtolower($crawler->redirection($url)), 1, "", "", 0);

if($array['status'] == 200) {

    $array = $crawler->site_info($array);

    $array = $crawler->itemscope($array);

    // $array['robot'] = $crawler->url_info("http://".$domain['url']."/robots.txt");
    
    $meta = implode(" ", $array['p']);

    unset($array['response']);

    $text = filter_var($meta, FILTER_SANITIZE_STRING);

    //$new_get_array = $crawler->get_text_information_data($text, $trained_data, $word_array, $path);    

    //$array = array_merge($new_get_array, $array);

    $array = $crawler->get_image_title($array);

    if($verifier->space($array['site_name'])) {

      $array['site_name'] = strtoupper($manipulation->main_domain($array['url'])['main']);

    }
  
    // if(!$verifier->space($array['new_main_image'])) {

    //     $type = substr($array['new_main_image'], strrpos($array['new_main_image'], '.') + 1);

    //     $array_type = array("gif", "jpeg", "png", "jpg");

    //     if (!in_array($type, $array_type)) {

    //         $type = 'png';

    //     }

    //     $image_url = $array['new_main_image'];

    //     $thumb = file_get_contents($image_url);

    //     $src = "feed/thumb/".$hash_id.".".$type;

    //     $temp = "feed/thumb/".$hash_id.".".$type;

    //     file_put_contents($temp, $thumb);

    //     $sizes = getimagesize($temp);

    //     $array_image = $files->getthumbimage($src, $temp, $size, 1, $news_thumb);

    //     $array_image_large = $files->getthumbimage($src, $temp, $size, 1, $news_thumb_large);

    //     unlink($temp);

    //     $thumb_url = $news_thumb["thumb_dir"].$array_image['thumb'];

    //     $thumb_url_large = $news_thumb_large["thumb_dir"].$array_image_large['thumb'];

    // }


    // $palette = Palette::fromFilename($content_main_row->thumb_large_url);

    // // $palette is an iterator on colors sorted by pixel count
    // foreach($palette as $color => $count) {
    //     // colors are represented by integers
    //     $array_color["main_image"]["color"][Color::fromIntToHex($color)] = $count;
    // }

    // $main_image_colour = $array_color["main_image"]["color"];

    // arsort($main_image_colour);

    // foreach($main_image_colour as  $key => $color) {

    //     $color_difference_array[] = $color_class->colorDiff($setting['compare_colour'], $key);     

        
    // }  
    
    // $color_difference = array_sum($color_difference_array)/count($color_difference_array);     

    // $array["main_image"]["color"] = mysqli(json_encode(serialize($main_image_colour)));

    // $tags = mysqli(json_encode(serialize($array['results']['phrases'])));

    // $text_highlights = stripslashes(implode(" ", $array['results']['summary']['highlights']));

    // $text_summary = stripslashes(implode(" ", $array['results']['summary']['summary']));

    // $array_new = mysqli(json_encode(serialize($array)));

    // $publish  = '1';

    // if(count($array['results']['phrases']) == 0 || $verifier->space($text_highlights)) {
    //     $publish  = '0';
    // }

}

   
    
/*
// now force PHP to output to the browser... 
$size = ob_get_length(); 

header("Content-Length: $size"); 

header('Connection: close'); 

ob_end_flush(); 

ob_flush(); 

flush(); // yes, you need to call all 3 flushes!

if (session_id()) session_write_close();
 
 

// everything after this will be executed in the background. 
// the user can leave the page, hit the stop button, whatever.
        
$classifier = new NaiveBayesClassifier();
    
//$classifier -> train($text, $category_array[$text_type]);
    
//echo $category_array[$text_type];

$category = $classifier -> classify($text);
$category);
*/

//$usable_image = array();
//
//foreach($array['img'] as $key => $image) {
//    
//    if (($image['width'] > 200) && ($image["height"] > 100)) {
//        
//        $usable_image[] = $image;
//        
//    }
//}

print("<pre>".print_r(array_filter($array),true)."</pre>");


?>
<!--<head>
  <meta http-equiv="refresh" content="1">
</head>-->

