<?php

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

$page = 1;

$limit = 100000000;

$start = round((($page-1)*($limit)));

//$url = "https://webhose.io/search?token=".$setting["webhose_key"]."&format=json&q=".urlencode($q)."%20language%3A(english)%20performance_score%3A%3E0%20(site_type%3Anews%20OR%20site_type%3Ablogs)&size=".$limit."";

//$data = url_info($url);

//$json = json_decode($data['response']);

//foreach ($json->posts as $result) {

//    $array[] = array('image_width' => "194px", 'image_url' => urlencode(htmlstring($result->thread->main_image)), 'source_url' => urlencode(htmlstring($result->url)), 'title' => urlencode($result->title), 'summary' => urlencode($result->text), 'publish_date' => urlencode(htmlstring($result->published)), 'source' => urlencode(htmlstring(indextext($value->source))), 'author' => urlencode(htmlstring($result->thread->site)));

//}

//https://www.google.co.uk/search?q=best+news+api&rlz=1C1CHBF_en-GBGB756GB756&oq=best+news+api&aqs=chrome..69i57j0l2.2764j0j4&sourceid=chrome&ie=UTF-8


$content = array('bbc-news', 'the-wall-street-journal-api', 'usa-today', 'the-guardian-uk', 'business-insider', 'techcrunch', 'mtv-news');
                 
foreach($content as $source) { 
    
    $url = "https://newsapi.org/v1/articles?source=".$source."&apiKey=".$setting["newsapi_key"];
    
    $data = $crawler->url_info($url);

    $json = json_decode($data['response']);
    
    foreach ($json->articles as $result) {

        $array[] = array('image_width' => "194px", 'image_url' => urlencode($manipulation->htmlstring($result->urlToImage)), 'source_url' => urlencode($manipulation->htmlstring($result->url)), 'title' => urlencode($result->title), 'summary' => urlencode($result->description), 'publish_date' => urlencode($manipulation->htmlstring($result->publishedAt)), 'source' => urlencode($manipulation->htmlstring($manipulation->indextext($value->source))), 'author' => urlencode($manipulation->htmlstring($result->author))); 

    }

}


$data = json_decode(json_encode($array));

foreach($data as $key => $value) { 
    
    $hash_id = mysqli(md5($value->source_url));

    $count = array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT COUNT(`id`) FROM `news` WHERE `hash_id`='".$hash_id."'")));

    if ($count == 0) {

        $source = mysqli($manipulation->htmlstring(urldecode($value->source_url)));

        $width = mysqli(urldecode($value->image_width));

        $published = mysqli(urldecode($value->publish_date));

        $title = mysqli(urldecode($value->title));

        $description = mysqli(urldecode($value->summary));

        $author = mysqli(urldecode($value->author));

        $type = substr($value->image_url, strrpos($value->image_url, '.') + 1);

        $array = array("gif", "jpeg", "png", "jpg");

        if (!in_array($type, $array)) {

            $type = 'png';

        }

        if(!$verifier->space(urldecode($value->image_url))) {

            $image_url = urldecode($value->image_url);

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

        }

        if($verifier->space($author)) {

            $domain = $manipulation->main_domain($source);

            if(!$verifier->space($domain['main'])) {

                $author = $domain['main'];

            }
            else {

                $author ="admin";
            }


        }

        $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `author`  WHERE `name` ='".$author."'  ORDER BY `id` ASC LIMIT 0,1");

        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if( $total_count > 0) {

            $author_row = mysqli_fetch_object($main_query);

            $author_id = $author_row->id;

        }
        else {

            mysqli_query($setting['Lid'],"INSERT INTO `author` (`name`) VALUES ('".$author."')");

            $author_id = mysqli_insert_id($setting["Lid"]);

        }

        mysqli_query($setting['Lid'],"INSERT INTO `news` (`hash_id`, `title`, `description`, `thumb_url`, `thumb_large_url`, `width`, `source_url`, `published`, `author`) VALUES ('".$hash_id."', '".$title."', '".$description."', '".$thumb_url."', '".$thumb_url_large."', '".$width."', '".$source."',  '".$published."', '".$author_id."')");

        mysqli_query($setting['Lid'],"INSERT INTO `events` (`event`, `hash_id`, `jobs`) VALUES ('Get Content', '".$hash_id."', '1')");

    }

}

echo  $i;

?>


<!--<head>
  <meta http-equiv="refresh" content="1">
</head>-->
