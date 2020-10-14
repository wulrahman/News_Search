<?php
    

$page_content = array(0);

if(isset($_GET['page_content'])) {
    
    $page_content_merge = explode(",", urldecode($_GET['page_content']));
    $page_content_merge = array_filter($page_content_merge);
    
    if(count($page_content_merge)> 0) {
        $page_content = array_merge($page_content, $page_content_merge);
    }
    
}


//print_r($page_content);
$page = intval($_GET['page']);

$limit = intval($_GET['limit']);

if($page == 0) {
    
	$page=1;
    
}

$style_type = array(1=>1, 2=>1, 3=>2, 4=>2, 5=>1, 6=>2, 7=>2, 1=>1, 2=>1, 3=>2, 4=>2, 5=>1, 6=>2, 7=>2);

if($limit >= array($style_type)) {
    $limit = array($style_type);
}

$start = (($page-1)*$limit);


if(isset($_GET['q'])) {
    
    $q = $_GET['q'];
    
    foreach(explode(" ",$q) as $p) {

        if(!$verifier->space($p)) {

            $match[] = '('.$p.'*)';

        }

    }

    $matchs = implode(' ', $match);
    
    $main_query = $get_content_article->get_content_search_fulltext($matchs, $page, $limit);

    $main_query = $main_query['results'];

    //print_r($main_query);


}
else if(isset($_GET['category'])) {

    $category = intval($_GET['category']);

    $main_query = $get_content_article->get_category_content($category, $limit, $start);

}
else {

    $main_query = $get_content_article->get_sort_content($limit, $start);
    
}

$type_array_limit = array();

foreach($style_type as $key => $type) {

    for($i = 0; $i < $type; $i++) {
        $type_array_limit[] =$key;
    }

}

$array = array();

foreach($main_query as $key => $row) {

    $tags = unserialize(json_decode($row["tags"]));

    $tags = array_values(array_filter($tags));

    $published = date("F j, Y",strtotime($row["timestamp"]));

    $title = $manipulation->limit_text(strip_tags($row["title"]),15);

    $id = $row["id"];

    $thumb = $setting['main_url'].'/main/'.$row["thumb_large_url"];

    $url = $setting['main_url'].'/?action=view&id='.$id;
    
    $author_row = $get_content_article->get_author_name($row["author"]);

    $author = $manipulation->limit_text(strip_tags($author_row["name"]),4);

    $type_array = $type_array_limit[$key];
    
    $array[] = array('published'=>$published, 'title'=>$title, 'thumb'=>$thumb, 'url'=>$url, 'author'=>$author, 'type_array'=>$type_array);
    
}

echo json_encode($array);

?>
