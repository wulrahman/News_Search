<?php

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

// don't let user kill the script by hitting the stop button 
ignore_user_abort(true);

// don't let the script time out 
set_time_limit(0);

// start output buffering
ob_start();

echo "Data Mining";

$page_content = array(0);

//error_reporting(E_ALL);

$event_main_query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `timestamp`, `hash_id` FROM `events` WHERE `event` = 'Get Content' AND (`done` !=`jobs` OR `done` < `jobs` OR `done` IS NULL) ORDER BY RAND() LIMIT 1");

    //"`timestamp` + INTERVAL 1 HOUR >= now() AND"
$event_count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

if($event_count > 0) {

    $tage_extraction = new tage_extraction($path);

    $color_class = new colorTests();

    $words = $tage_extraction->get_word_list('../library/AI/words_ai_test.txt');

    $word_array = $words['words'];

    $word_array_i = $words['wordsi'];
    
    $outputs_key = $words['outputs_key'];
        
    $trained_data['category'] = unserialize(file_get_contents('../library/AI/category_ai_test.txt'));    
            
    $trained_data['sentiment'] = unserialize(file_get_contents('../library/AI/sentiment_ai_test.txt'));

    $trained_data['suffex'] = json_decode(unserialize(file_get_contents('../library/AI/tags_ai_suffex.txt')));
            
    while($event_main_row = mysqli_fetch_object($event_main_query)) {
    
        $event_id = $event_main_row->id;

        $view_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SQL_CALC_FOUND_ROWS * FROM `news` WHERE `hash_id`='".$event_main_row->hash_id."' AND `publish` IS NULL");

        $view_total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if ($view_total_count > 0) {

            $content_main_row = mysqli_fetch_object($view_query);

            $parse = parse_url($content_main_row->source_url);

            $url = mysqli($manipulation->fix_url($content_main_row->source_url, $parse));

            $array = $crawler->url_info(strtolower($crawler->redirection($url)), 1, "", "", 0);

            if($array['status'] == 200) {

                $array = $crawler->site_info($array);

                $array = $crawler->itemscope($array);

                $url_main = $manipulation->main_domain($array['url']);

                $array['robot'] = $crawler->url_info("http://".$url_main['url']."/robots.txt");
                
                $meta = implode(" ", $array['p']);
                
                unset($array['response']);

                $array = $crawler->get_image_title($array);

                if($verifier->space($array['site_name'])) {
            
                    $array['site_name'] = strtoupper($url_main['main']);
            
                }

                if($verifier->space($array['content_id'])) {
            
                    $array['content_id'] = md5($meta);
              
                }

                $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `site`  WHERE `url` ='".$url_main['url']."' LIMIT 0,1");

                $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                if($total_count > 0) {

                    $site_row = mysqli_fetch_object($main_query);

                    $site_id = $site_row->id;

                }
                else {

                    mysqli_query($setting['Lid'],"INSERT INTO `site` (`name`, `url`) VALUES ('".$array['site_name']."', '".$url_main['url']."')");

                    $site_id = mysqli_insert_id($setting["Lid"]);

                }


                $text = filter_var($meta, FILTER_SANITIZE_STRING);


                
                $array = array_merge($new_get_array, $array);


                if(!$verifier->space($array['new_main_image'])) {

                    $type = substr($array['new_main_image'], strrpos($array['new_main_image'], '.') + 1);

                    $array_type = array("gif", "jpeg", "png", "jpg");

                    if (!in_array($type, $array_type)) {

                        $type = 'png';

                    }

                    $image_url = $array['new_main_image'];

                    $thumb = file_get_contents($image_url);

                    $src = "feed/thumb/".$hash_id.".".$type;

                    $temp = "feed/thumb/".$hash_id.".".$type;

                    file_put_contents($temp, $thumb);

                    $sizes = getimagesize($temp);

                    $array_image = $files->getthumbimage($src, $temp, $size, 1, $news_thumb);

                    $array_image_large = $files->getthumbimage($src, $temp, $size, 1, $news_thumb_large);

                    unlink($temp);

                    $thumb_url = $news_thumb["thumb_dir"].$array_image['thumb'];

                    $thumb_url_large = $news_thumb_large["thumb_dir"].$array_image_large['thumb'];


                    $palette = Palette::fromFilename($thumb_url_large);

                    foreach($palette as $color => $count) {
    
                        $array_color["main_image"]["color"][Color::fromIntToHex($color)] = $count;
                        
                    }
                    
                    $main_image_colour = $array_color["main_image"]["color"];

                    arsort($main_image_colour);

                    foreach($main_image_colour as  $key => $color) {

                        $color_difference_array[] = $color_class->colorDiff($setting['compare_colour'], $key);     

                        
                    }  
                    
                    $color_difference = array_sum($color_difference_array)/count($color_difference_array);     

                    $array["main_image"]["color"] = mysqli(json_encode(serialize($main_image_colour)));


                }


                
                $tags = mysqli(json_encode(serialize($array['results']['phrases'])));

                $text_highlights = stripslashes(implode(" ", $array['results']['summary']['highlights']));

                $text_summary = stripslashes(implode(" ", $array['results']['summary']['summary']));

                $array_new = mysqli(json_encode(serialize($array)));


                $publish  = '1';

                if(count($array['results']['phrases']) == 0 || $verifier->space($text_highlights)) {

                    $publish  = '0';

                }


                $training_set = 0;

                if(($verifier->space($array['results']['category']['category']) || $verifier->space($array['results']['category']['sentiment'])) || (!$verifier->space($array['page_category']))) {

                    $training_set = 1;

                }

                $index = $crawler->insert_map_data($array);

                mysqli_query($setting["Lid"],'UPDATE `news` SET `tags`="'.$tags.'",`publish`="'.$publish.'",
                `readability`="'.$array['readability']['average'].'", `sentiment`="'.$index['sentiment_id'].'",
                `category`="'.$index['category_id'].'", `summary`="'.$text_summary.'", `highlights`="'.$text_highlights.'", 
                `cotent`="'.mysqli($array['content_news']).'", `image_colour`="'.$array["main_image"]["color"].'",
                `response`="'.$array_new.'", `published`=NOW(), `training_set` ="'.$training_set.'", 
                `image_color_difference` = "'.$color_difference.'", `title`= "'.mysqli($array['new_title']).'", 
                `thumb_url` = "'.$thumb_url.'", `thumb_large_url` = "'.$thumb_url_large.'", `site` ="'.$site_id.'", 
                `page_category` = "'.$index['page_category_id'].'", `page_type` = "'.$index['page_type_id'].'", `page_tags` = "'.$array['page_tags'].'", 
                `page_location` = "'.$array['page_location'].'", `page_audience` = "'.$array['page_audience'].'" 
                WHERE `hash_id` = "'.$event_main_row->hash_id.'"');



                if($training_set = 1) {

                    mysqli_query($setting['Lid'],"INSERT INTO `events` (`event`, `hash_id`, `jobs`) VALUES ('Label Data', '".$event_main_row->hash_id."', '1')");

                }

                mysqli_query($setting["Lid"],"UPDATE `events` SET `done` = '1' WHERE `events`.`Id` = '".$event_id."'");

                echo $content_main_row->id." Article Id Done";
                
            }
            else {

                mysqli_query($setting["Lid"],"DELETE FROM `events` WHERE `hash_id` = '".$event_main_row->hash_id."'");

            }


        }
        else {

             mysqli_query($setting["Lid"],"DELETE FROM `events` WHERE `hash_id` = '".$event_main_row->hash_id."'");

        }
        
    }

    
}
        

// now force PHP to output to the browser... 
$size = ob_get_length(); 

header("Content-Length: $size"); 

header('Connection: close'); 

ob_end_flush(); 

ob_flush(); 

flush(); // yes, you need to call all 3 flushes!

if (session_id()) {
    
    session_write_close();
    
}
 
//print("<pre>".print_r($array,true)."</pre>");

?>
<head>
  <meta http-equiv="refresh" content="5">
</head>