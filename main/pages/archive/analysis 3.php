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


$content_main_row->source_url ="https://www.bbc.co.uk/news/live/uk-politics-53077588";

        $parse = parse_url($content_main_row->source_url);

        $url = mysqli($manipulation->fix_url($content_main_row->source_url, $parse));

        $array = $crawler->url_info(strtolower($crawler->redirection($url)), 1, "", "", 1);

        if($array['status'] == 200) {

            $array = $crawler->site_info($array);

            $array = $crawler->itemscope($array);
            
            $domain =  $manipulation->main_domain($url);

            $array['robot'] = $crawler->url_info("http://".$domain['url']."/robots.txt");
            
            //$get_headings = $manipulation->get_headings_tag($array['response']);

            $meta = implode(" ", $array['p']);
            
            unset($array['response']);

        }

 
        $text = filter_var($meta, FILTER_SANITIZE_STRING);


        $new_get_array = get_text_information_data($text);    

        $cleaner = new MrClean\MrClean();

        $array = array_merge($new_get_array, $array);


   
    
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

