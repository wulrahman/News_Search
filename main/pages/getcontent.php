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

$event_main_query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `timestamp`, `hash_id` FROM `events` WHERE `event` = 'Get Content' AND (`done` !=`jobs` AND `done` < `jobs` OR `done` IS NULL) ORDER BY `timestamp` ASC");

    //"`timestamp` + INTERVAL 1 HOUR >= now() AND"
$event_count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

if($event_count > 0) {
        
    $trained_data['category'] = unserialize(file_get_contents('../library/AI/category_ai_test.txt'));    
            
    $trained_data['sentiment'] = unserialize(file_get_contents('../library/AI/sentiment_ai_test.txt'));
            
    while($event_main_row = mysqli_fetch_object($event_main_query)) {
    
        $event_id = $event_main_row->id;


        $view_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SQL_CALC_FOUND_ROWS * FROM `news` WHERE `hash_id`='".$event_main_row->hash_id."' AND `publish` IS NULL");

        $view_total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if ($view_total_count > 0) {

            $content_main_row = mysqli_fetch_object($view_query);

            $parse = parse_url($content_main_row->source_url);

            $url = mysqli($manipulation->fix_url($content_main_row->source_url, $parse));

            $array = $crawler->url_info(strtolower($crawler->redirection($url)), 1, "", "", 1);

            if($array['status'] == 200) {

                $array = $crawler->site_info($array);

                $array = $crawler->itemscope($array);

                $array['robot'] = $crawler->url_info("http://".$domain['url']."/robots.txt");

                //$array['getheading']= $manipulation->get_headings_tag($array['response']);
                
                $meta = implode(" ", $array['p']);

                unset($array['response']);

                $text = filter_var($meta, FILTER_SANITIZE_STRING);

                $new_get_array = get_text_information_data($text, $trained_data);    

                $array = array_merge($new_get_array, $array);

                $palette = Palette::fromFilename($content_main_row->thumb_large_url);

                // $palette is an iterator on colors sorted by pixel count
                foreach($palette as $color => $count) {
                    // colors are represented by integers
                    $array_color["main_image"]["color"][Color::fromIntToHex($color)] = $count;
                }

                $main_image_colour = array_search(max($array_color["main_image"]["color"]), $array_color["main_image"]["color"]);

                $array["main_image"]["color"] = $main_image_colour;

                $tags = mysqli(json_encode(serialize($array['results']['phrases'])));

                $text_highlights = stripslashes(implode(" ", $array['results']['summary']['highlights']));

                $text_summary = stripslashes(implode(" ", $array['results']['summary']['summary']));

                $array_new = mysqli(json_encode(serialize($array)));

                $publish  = '1';

                if(count($array['results']['phrases']) == 0 || $verifier->space($text_highlights)) {
                    $publish  = '0';
                }


                $randomNumber_training_set = rand(1,10);

                $training_set = 0;

                if(($verifier->space($array['results']['category']['category']) || $verifier->space($array['results']['category']['sentiment']) || $randomNumber_training_set ==5) && (!$verifier->space($array['content_news']) || $verifier->space($text_highlights))) {
                    $training_set = 1;
                }

                $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `categorys`  WHERE `name` ='".$array['results']['category']['category']."'  ORDER BY `cat_order` ASC LIMIT 0,1");

                $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                if($total_count > 0) {

                    $category_row = mysqli_fetch_object($main_query);

                    $category_id = $category_row->id;

                }
                else {

                    mysqli_query($setting['Lid'],"INSERT INTO `categorys` (`name`) VALUES ('".$array['results']['category']['category']."')");

                    $category_id = mysqli_insert_id($setting["Lid"]);

                }

                $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `Sentiment`  WHERE `sentiment` ='".$array['results']['sentiment']['sentiment']."'  ORDER BY `id` ASC LIMIT 0,1");

                $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                if( $total_count > 0) {

                    $sentiment_row = mysqli_fetch_object($main_query);

                    $sentiment_id = $sentiment_row->id;

                }
                else {

                    mysqli_query($setting['Lid'],"INSERT INTO `Sentiment` (`sentiment`) VALUES ('".$array['results']['sentiment']['sentiment']."')");

                    $sentiment_id = mysqli_insert_id($setting["Lid"]);

                }

                mysqli_query($setting["Lid"],"UPDATE `news` SET `tags`='".$tags."',`publish`='".$publish."',`readability`='".$array['readability']['average']."', `sentiment`='".$sentiment_id."',`category`='".$category_row->id."', `summary`='".$text_summary."', `highlights`='".$text_highlights."', `cotent`='".mysqli($array['content_news'])."', `image_colour`='".$main_image_colour."',`response`='".$array_new."', `published`=NOW(), `training_set` ='".$training_set."' WHERE `hash_id` = '".$event_main_row->hash_id."'");

                $content_array = $array;

                foreach($content_array['results']['phrases'] as $key => $tag) {

                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `tags`  WHERE `tag` ='".mysqli($key)."'  ORDER BY `id` ASC LIMIT 0,1");

                    $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                    if( $total_count > 0) {

                        $tag_row = mysqli_fetch_object($main_query);

                        $tag_id = $tag_row->id;

                    }
                    else {

                        mysqli_query($setting['Lid'],"INSERT INTO `tags` (`tag`) VALUES ('".mysqli($key)."')");

                        $tag_id = mysqli_insert_id($setting["Lid"]);

                    }

                    mysqli_query($setting['Lid'],"INSERT INTO `map_tag` (`tag`, `article`, `score`) VALUES ('".$tag_id."', '".$content_main_row->id."', '".$tag."')");



                }

                unlink($content_array['results']);

                unlink($content_array['content_news']);


                foreach($content_array['a'] as $key => $link) {

                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `links`  WHERE `href` ='".mysqli($link["href"])."'  ORDER BY `id` ASC LIMIT 0,1");

                    $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                    if( $total_count == 0) {

                        mysqli_query($setting['Lid'],"INSERT INTO `links` (`href`, `title`, `article`) VALUES ('".mysqli($link["href"])."', '".mysqli($link["title"])."', '".$content_main_row->id."')");

                    }

                }

                unlink($content_array['a']);

                foreach($content_array['img'] as $key => $image) {

                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `image`  WHERE `src` ='".mysqli($image["src"])."'  ORDER BY `id` ASC LIMIT 0,1");

                    $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                    if( $total_count == 0) {

                        mysqli_query($setting['Lid'],"INSERT INTO `image` (`src`, `alt`, `width`, `height`, `article`) VALUES ('".mysqli($image["src"])."', '".mysqli($image["alt"])."', '".mysqli($image["width"])."', '".mysqli($image["height"])."', '".$content_main_row->id."')");

                    }

                }

                unlink($content_array['img']);

                foreach($content_array as $key => $content) {

                    if(!is_string($content)) {

                        $content = json_encode(serialize($content));

                    }

                    mysqli_query($setting['Lid'],"INSERT INTO `content` (`content`, `type`,`article`) VALUES ('".mysqli($content)."', '".mysqli($key)."', '".$content_main_row->id."')");


                }

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
