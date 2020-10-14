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

        $array[] = array('source_url' => urlencode($manipulation->htmlstring($result->url))); 

    }

}


$data = json_decode(json_encode($array));

foreach($data as $key => $value) { 
    
    $hash_id = mysqli(md5($value->source_url));

    $count = array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT COUNT(`id`) FROM `news` WHERE `hash_id`='".$hash_id."'")));

    if ($count == 0) {

        $source = mysqli($manipulation->htmlstring(urldecode($value->source_url)));

        mysqli_query($setting['Lid'],"INSERT INTO `news` (`hash_id`, `source_url`) VALUES ('".$hash_id."', '".$source."')");

        mysqli_query($setting['Lid'],"INSERT INTO `events` (`event`, `hash_id`, `jobs`) VALUES ('Get Content', '".$hash_id."', '1')");

    }

}

echo  $i;

?>


<!--<head>
  <meta http-equiv="refresh" content="1">
</head>-->
