<?php

foreach($_POST as $item => $key){

	$N_POST[$item] = mysqli($key);

}

$_POST = array_replace($_POST, $N_POST);

foreach($_GET as $item => $key){

	$N_GET[$item] = mysqli($key);

}

$_GET = array_replace($_GET, $N_GET);

function mysqli( $string ) {

	global $setting;

	return mysqli_real_escape_string($setting["Lid"],$string);

}

function email_system($email, $subject, $message) {

	global $setting;

	$body .= "<html>
	<head>
	<title>".$subject."</title>
	</head>
	<body>";

	$body .= $message;

	$body .= "</body></html>";

	$headers = "MIME-Version: 1.0" . "\r\n";

	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	//$headers .= 'Cc: myboss@example.com' . "\r\n";

   	$headers .= 'From: <'.$setting["no_reply"].'>' . "\r\n";

	mail($email, $subject, $body, $headers);

}


class get_content_article {

    function viewed_check($id) {

        global $setting;

        $query = mysqli_query($setting['Lid'],  "(SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `identifier` IN ('".implode("','", $setting['user']->array_identifiers)."') AND `clicks`.`article`='".intval($id)."')");

        $row_main_model = mysqli_fetch_row($query);

        if($row_main_model[0] > 0) {
            
            return true;

        }
        else {

            return false;

        }

    }
    
    function get_data_user_model($limit = null) {
        
        global $setting, $page_content, $model_output;
        
        if(count($model_output)==0) {
        
            $Neural_Net = new Neural_Net($setting['learning_rate'] , $setting['activation_fun']);

            if($limit == null) {

                $query = mysqli_query($setting['Lid'],"(SELECT  `published`, `sentiment`, `author`, `readability`, `id`, `category`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`article` AND `identifier` IN ('".implode("','", $setting['user']->array_identifiers)."')) AS `count` FROM `news` WHERE `id` NOT IN (".implode(",", $page_content).") AND `publish` = 1  ORDER BY `news`.`id` DESC)");

            }
            else {

                $query = mysqli_query($setting['Lid'],"(SELECT  `published`, `sentiment`, `author`, `readability`, `id`, `category`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`article` AND `identifier` IN ('".implode("','", $setting['user']->array_identifiers)."')) AS `count` FROM `news` WHERE `id` NOT IN (".implode(",", $page_content).") AND `publish` = 1  ORDER BY `news`.`id` DESC LIMIT 0, ".$limit.")");

            }


            $count=array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));

            if($count > 0) { 

                while ($row_main_model = mysqli_fetch_array($query)) {

                    $clicks = $row_main_model['count'];

                    if($clicks == 0) {

                        $time = strtotime($row_main_model['published']);

                        $data_input = array(intval($row_main_model['sentiment']), intval($row_main_model['author']),  intval($row_main_model['category']), intval($row_main_model['readability']));

                        $data_input = $Neural_Net->arrayTranspose($data_input);

                        $prediction = $Neural_Net->forward($data_input, $setting['user']->model['weights'], $setting['user']->model['bias']);

                        $model_output[$row_main_model['id']] = $prediction['output_layour']['z'][0][0];

                    }

                }


            }

            arsort($model_output);
        
        }
        
        return $model_output;
        
    }
    
    function get_content_user_model($limit) {

        global $setting, $page_content;

        $map = array_filter($this->get_data_user_model(50));
        
        foreach($page_content as $key => $content) {
            
            unset($map[$content]);
            
        }

        $count = COUNT($map);

        $map_key = array_keys($map);

        if($count > 0) {

            for ($i = max(0, $start); $i <= min($start + ($limit - 1), $count); $i++) {
                
                if(intval($map_key[$i]) > 0) {

                    $array[] = $this->get_content($map_key[$i]);
                    
                    $page_content[] = $map_key[$i];
                        
                }

            }

        }

        return $array;

    }
    
    
    function get_category_name ($id) {
        
        global $setting, $page_content;
        
        $main_query_category = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `categorys`  WHERE `id` ='".$id."'  ORDER BY `cat_order` ASC LIMIT 0,1");
            
        $total_count_category=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
    
        if($total_count_category > 0) {
        
            return $category_row = mysqli_fetch_array($main_query_category);
            
        }
        else {
        
            return false;
            
        }
        
    }
    
    function get_sentiment_name ($id) {
        
        global $setting, $page_content;
        
        $main_query_sentiment = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `Sentiment`  WHERE `id` ='".$id."'  ORDER BY `id` ASC LIMIT 0,1");
            
        $total_count_sentiment=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
    
        if( $total_count_sentiment > 0) {
        
            return $sentiment_row = mysqli_fetch_array($main_query_sentiment);
            
        }
        else {
        
            return false;
            
        }
        
    }
    
    function get_author_name ($id) {
        
        global $setting, $page_content;
        
        $main_query_author = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `author`  WHERE `id` ='".$id."'  ORDER BY `id` ASC LIMIT 0,1");
            
        $total_count_author=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
    
        if( $total_count_author > 0) {
        
            return $author_row = mysqli_fetch_array($main_query_author);
            
        }
        else {
        
            return false;
            
        }
        
    }
    
    function get_sql_relevance($q) {

        global $setting, $page_content;

        $lib_search = new Libs_Search($q);

        $stemmedmatchs = $lib_search->GetSearchQueryString();
        
        $relevance = "SELECT `news`.`id`,
            ((MATCH(`news`.`description`) AGAINST ('".$stemmedmatchs."'))
             + (MATCH(`news`.`title`) AGAINST ('".$stemmedmatchs."'))
             + (MATCH(`news`.`description`) AGAINST ('".$stemmedmatchs."' IN BOOLEAN MODE))
             + (MATCH(`news`.`title`) AGAINST ('".$stemmedmatchs."' IN BOOLEAN MODE))
             + (MATCH(`news`.`description`) AGAINST ('".$stemmedmatchs."' IN NATURAL LANGUAGE MODE))
             + (MATCH(`news`.`title`) AGAINST ('".$stemmedmatchs."' IN NATURAL LANGUAGE MODE))) AS `relevance` FROM `news` WHERE `news`.`publish` = '1' AND `news`.`id` NOT IN (".implode(",", $page_content).") HAVING Relevance > 0";
        
        $query = mysqli_query($setting["Lid"], $relevance);
        
        while($row = mysqli_fetch_array($query)) {
            $map[$row["id"]] = $row["relevance"];
        }
        

        arsort($map);
        
        return $map;

    }

    function get_popular_content($duration = "100 DAY") {
        
        global $setting, $page_content, $return_array_popular;
        
        if(count($return_array_popular) == 0) {

            $main_query = mysqli_query($setting["Lid"], "SELECT SQL_CALC_FOUND_ROWS id,
            (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`article` AND `timestamp` > NOW() - INTERVAL ".$duration.") AS `click_count`
            FROM `news` WHERE `id` NOT IN(".implode(",", $page_content).") AND `publish` = 1");

            $main_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

            if($main_count > 0) {

                while($popular_query = mysqli_fetch_array($main_query)) {

                    $return_array_popular[$popular_query['id']] = $popular_query['click_count'];

                }

            }
            
        }
        
        return $return_array_popular;
            
        
    }
    
    function get_popular_user_preload($limit) {

        global $setting, $page_content;
        
        $map = array_filter($this->get_popular_content());
        
        foreach($page_content as $key => $content) {
            
            unset($map[$content]);
            
        }

        $count = COUNT($map);

        $map_key = array_keys($map);

        if($count > 0) {

            for ($i = max(0, $start); $i <= min($start + ($limit - 1), $count); $i++) {
                
                if(intval($map_key[$i]) > 0) {

                    $array[] = $this->get_content($map_key[$i]);
                    
                    $page_content[] = $map_key[$i];
                    
                }

            }

        }

        return $array;

    }
    
    function get_content($id) {
        
        global $setting, $page_content;
        
        $return_array = array();

        $content_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `id` = '".$id."' AND `publish` = '1' LIMIT 0,1");
        
        $main_count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
        
        if($main_count > 0) {
            
            while($row = mysqli_fetch_array($content_query)) {
                
               if(empty($row['author']) || $row['author'] =="") {
                            
                    $row['author'] = "Admin";
                            
                }
            
                $return_array = $row;
                
                $page_content[] = $row['id'];
                
            }
            
        }
        
        return $return_array;
            
        
    }
    
    function get_category_content($category, $number_of_items, $start_from = 0) {
        
        global $setting, $page_content;
        
        $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `category` ='".$category."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT ".$start_from.",".$number_of_items);

        $main_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if($main_count > 0) {
            
            while($row = mysqli_fetch_array($main_query)) {
                
                if(empty($row['author']) || $row['author'] =="") {
                            
                    $row['author'] = "Admin";
                            
                }
            
                $return_array[] = $row;
                
                $page_content[] = $row['id'];
                
            }
            
        }
        
        return $return_array;
            
        
    }
    
    function get_sort_content($number_of_items, $start_from = 0, $order_by = "`timestamp`") {
        
        global $setting, $page_content;
 
        $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `id` NOT IN (".implode(",", $page_content).")  AND `publish` = '1' ORDER BY ".$order_by." DESC LIMIT ".$start_from.", ".$number_of_items);
                          
        $main_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
        
        if($main_count > 0) {
            
             while($row = mysqli_fetch_array($main_query)) {
                 
                if(empty($row['author']) || $row['author'] =="") {
                            
                    $row['author'] = "Admin";
                            
                }
            
                $return_array[] = $row;
                
                $page_content[] = $row['id'];
            
             }
            
        }
        
        return $return_array;
            
        
    }
    
    function get_relevent_content ($id, $number_of_items, $start_from = 0) {
        
        global $setting, $page_content;
        
        $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `publish` = '1' AND `id` = '".$id."' ORDER BY `timestamp` DESC LIMIT 0,1");

        $main_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
        
        if($main_count > 0) {
            
            $row = mysqli_fetch_object($main_query);
        
            $tags = array_keys(unserialize(json_decode($row->tags)));
            
            $similar_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`, MATCH(`tags`) AGAINST('".implode(" ", $tags)."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`tags`) AGAINST('".implode(" ", $tags)."' IN NATURAL LANGUAGE MODE) AND `id` NOT IN (".implode(",", $page_content).")GROUP BY `id` HAVING Relevance > 0  ORDER BY `relevance` DESC LIMIT ".$start_from.", ".$number_of_items);

            while($similar_row = mysqli_fetch_object($similar_query)) {

                $main_similar_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$similar_row->id."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");

                $total_similar_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
                
                if($total_similar_count > 0) {

                    $row_similar_content = mysqli_fetch_array($main_similar_content);
                    
                    if(empty($row_similar_content['author']) || $row_similar_content['author'] =="") {
                            
                        $row_similar_content['author'] = "Admin";
                            
                    }
                    
                    $return_array[] = $row_similar_content;
                    
                    $page_content[] = $row_similar_content['id'];
                    
                    
                }

                
            }
            
        }
        
        return $return_array;
            
        
    }
    
    function get_content_search_fulltext($q, $page, $limit) {

        global $setting, $page_content;

        $start = ($page-1) * $limit;

        $map = array_filter($this->get_sql_relevance($q));

        $count = COUNT($map);

        $array['count'] = $count;

        $map_key = array_keys($map);

        if($count > 0) {

            for ($i = max(0, $start); $i <= min($start + ($limit - 1), $count); $i++) {
                
                if(intval($map_key[$i]) > 0) {

                    $array['results'][] = $this->get_content($map_key[$i]);
                    
                }

            }

        }

        return $array;

    }
    
}

class files {

    function getthumbimage($src, $tmp_src, $size, $unlink = 1, $custom = array()) {

        global $setting;
        
        $generator = new generator_one();
        
        $verifier = new verifier();

        $file = file_get_contents($tmp_src);

        $array = array("gif", "jpeg", "png", "jpg");

        $type = pathinfo($src, PATHINFO_EXTENSION);

        if(!$verifier->space($type)) {

             $type = strtolower(substr($src, strrpos($src, '.') + 1));

             if (!in_array($type, $array)) {

                 $type = 'png';

             }

        }

        if (array_search(strtolower($type), $array)) {

            $setting["alp"] = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

            $salt = ($generator->randomurl($setting["alp"]).$generator->randomurl($setting["alp"]).$generator->randomurl($setting["alp"])).date('mdYhis', time());

            $setting = array_replace($setting, $custom);

            $type = array_keys($setting["image_types"], $type);

            $imgt = "Image".$setting["typeset"];

            $dir = $setting["dir_sub"].$setting["image_dir"].$salt.".".$setting["typeset"];

            $tempdir = $setting["dir_sub"].$setting["temp_dir"].$salt.".".$setting["typeset"];

            $thumbdir = $setting["dir_sub"].$setting["thumb_dir"].$salt.".".$setting["typeset"];

            $file = file_get_contents($tmp_src);

            $old = imagecreatefromstring($file);

            $imgt($old, $tempdir);

            $sizes = getimagesize($tempdir);

            $width = $sizes[0];

            $height = $sizes[1];

            if ($width > $setting["getmaxwidth"] && $height > $setting["getmaxheight"]) {

                if ($width > $height && $height > $setting["maxheight"]) {

                    $heightn = intval($height * $setting["maxwidth"] / $width);

                    $widthn = $setting["maxwidth"];

                }
                else if ($width > $maxwidth) {

                    $widthn = intval($width * $setting["maxheight"] / $height);

                    $heightn = $setting["maxheight"];

                }
                else {

                    $widthn = $width;

                    $heightn = $height;

                }

                if($heightn < $height && $widthn < $width) {

                    $height = $heightn;

                    $width = $widthn;

                }

                $imgt(self::image_resize($width, $height, $old, $sizes, $imgt), $thumbdir);

            }
            else {

                $imgt(self::image_resize($width, $height, $old, $sizes, $imgt), $thumbdir);

            }

            $array['thumb'] = $salt.".".$setting["typeset"];

            $array['width'] = $width;

            $array['height'] = $height;

            if($unlink == 1) {

                unlink($tempdir);

            }

        }
        else {

            $array['error'][] = "The following file is unsupported, please try another image.";

        }

        return $array;

    }

    function image_resize($width, $height, $old, $sizes, $imgt) {

        $thumbt = imagecreatetruecolor($width, $height);

        $backgroundColor = imagecolorallocate($thumbt , 255, 255, 255);

        imagefill($thumbt, 0, 0, $backgroundColor);

        imagecopyresized($thumbt, $old, 0, 0, 0, 0, $width, $height, $sizes[0], $sizes[1]);

        ob_start();

        $imgt($thumbt);

        $thumb = ob_get_contents();

        ob_end_clean();

        imagedestroy($thumbt);

        return imagecreatefromstring($thumb);

    }

    function mime_content_type_image($filename) {

        global $setting;

        $headers = get_headers($filename, 1);

        if (array_search($headers["Content-Type"], $setting["image_types"])) {

            return $headers["Content-Type"];

        }
        else if (function_exists('finfo_open')) {

            $finfo = finfo_open(FILEINFO_MIME);

            $mimetype = finfo_file($finfo, $filename);

            finfo_close($finfo);

            return $mimetype;

        }

    }

}


class user_data {
    
    function getUser($id = 0) {
        
        $generator = new generator_one();

        global $setting;

        if($id > 0) {

            $query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `users` WHERE `id`='".$id."'");

            $count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

            if ($count > 0) {

                $user = mysqli_fetch_object($query);

            }

            if($user->color == "") {

                $user->color = $generator->random_color();

                mysqli_query($setting["Lid"],"UPDATE `users` SET `color` = '".$user->color."' WHERE `users`.`id` = '".$user->id."';");

            }

        }
        else if (isset($_COOKIE["username"])) {

            $password = filter_var($_COOKIE['code'], FILTER_SANITIZE_STRING);

            $query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `users` WHERE `id`='".intval($_COOKIE['userid'])."' AND `password`='".mysqli($password)."'");

            $count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

            if ($count > 0) {

                $user = mysqli_fetch_object($query);

                if ($user->banned == 1) {

                    $user->error='<div class="module_notice">Your account has been blocked for violating one of our term of use and for misusing our site.</div>';

                    $user->login_status = 0;

                    $user->admin = 0;

                }
                else if ($user->active == 0) {

                    $user->error='<div class="module_notice">Your account is inactive, please validate your account via the validation email.</div>';

                    $user->login_status = 0;

                    $user->admin = 0;

                }
                else {

                    $user->login_status = 1;


                }

            }
            else {

                $user->login_status = 0;

                $user->admin = 0;

            }

        }
        else {

            $user->login_status = 0;

            $user->admin = 0;

        }

        if($user->icon == "") {

            $user->icon = $setting["user_icon"];

        }
    
        if($user->login_status == 0) {

            $user->id = null;

        }
        
        $row_count == 0;
        
        if(isset($_COOKIE["_user_id"])) {
            
            $user->cookie_id = filter_var($_COOKIE['_user_id'], FILTER_SANITIZE_STRING);
            
            $row_count = mysqli_fetch_row(mysqli_query($setting["Lid"],'SELECT COUNT(`id`) FROM `user_geo_location` WHERE `identifier`="'.$user->cookie_id.'"'))['0'];

        }

        if($row_count == 0) {
            
            $user->cookie_id = null;
            
        }
        
        $train_model_count = 0;
        
        if($user->login_status == 1) {

            $model_query = mysqli_query($setting['Lid'],"SELECT `model` FROM `user_model` WHERE `user` = '".$user->id."' ORDER BY `timestamp` DESC LIMIT 1");

        }
        else if($user->cookie_id !== null){

            $model_query = mysqli_query($setting['Lid'],"SELECT `model` FROM `user_model` WHERE `identifier` = '".$user->cookie_id."' ORDER BY `timestamp` DESC LIMIT 1");

        }
        
        $train_model_count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if($train_model_count > 0) {
            
            $model_row = mysqli_fetch_object($model_query);
        
            $user->model = unserialize(json_decode($model_row->model));
        }
        else {
            $user->model = null;
        }
        
        if($user->login_status == 1) {

            $identifier_query = mysqli_query($setting["Lid"], "SELECT `identifier` FROM `user_geo_location` WHERE `user` = '".$user->id."'");

            while($identifier = mysqli_fetch_array($identifier_query)) {

                $user->array_identifiers[] = $identifier['identifier'];

            }
            
        }
        else {

            $user->array_identifiers[] = $user->cookie_id;

        }
        
        self::log_user($user);

        return $user;

    }



    function log_user($user) {

        global $setting;
        
        $manipulation = new manipulation();
        
        $generator = new generator_one();
        
        //http://talkerscode.com/webtricks/get-address-longitude-and-latitude-using-php-and-google-map-api.php
        $ip=$manipulation->getRealIpAddr();
        
        $row_count = 0;

        if($user->cookie_id !== null && $user->login_status == 1) {
            
             mysqli_query($setting["Lid"],'UPDATE `user_geo_location` SET `timestamp`=now(), `user` = "'.$user->id.'" WHERE `identifier`="'.$user->cookie_id.'"');
            
        }
        else if($user->cookie_id !== null && $user->login_status == 0) {
            
             mysqli_query($setting["Lid"],'UPDATE `user_geo_location` SET `timestamp`=now() WHERE `identifier`="'.$user->cookie_id.'"');
            
        }
        else {

            $user_location = file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip);
            
            $user_location = unserialize($user_location);
            
            $user->cookie_id = $generator->randomurl($setting["alp"]).microtime();
        
            $user->cookie_id = md5($user->cookie_id);

            $user_location_content = mysqli(json_encode(serialize($user_location)));
            
            if($user->login_status == 1) {
                
                mysqli_query($setting["Lid"],"INSERT INTO `user_geo_location`(`ip`, `user_agent`, `country`, `data`, `timestamp`,  `country_code`, `identifier`, `user`) VALUES ('".$ip."','".$_SERVER['HTTP_USER_AGENT']."','".mysqli($user_location['geoplugin_countryName'])."', '".$user_location_content."', now(), '".mysqli($user_location['geoplugin_countryCode'])."', '".$user->cookie_id."', '".$user->id."')");
                
            }
            else {
                
                mysqli_query($setting["Lid"],"INSERT INTO `user_geo_location`(`ip`, `user_agent`, `country`, `data`, `timestamp`,  `country_code`, `identifier`) VALUES ('".$ip."','".$_SERVER['HTTP_USER_AGENT']."','".mysqli($user_location['geoplugin_countryName'])."', '".$user_location_content."', now(), '".mysqli($user_location['geoplugin_countryCode'])."', '".$user->cookie_id."')");
                
            }

            setcookie("_user_id", $user->cookie_id, 0, "/");

            setcookie("_user_id", $user->cookie_id, 0, "/", ".".$setting["domain"]);

        }

        //print_r($user_location);
        
        if($user->login_status == 1) {

            mysqli_query($setting["Lid"],'INSERT INTO `views`(`ip`, `user_agent`, `url`, `user`, `identifier`) VALUES ("'.$ip.'","'.$_SERVER['HTTP_USER_AGENT'].'","'.$_SERVER['REQUEST_URI'].'","'.$user->id.'", "'.$user->cookie_id.'")');
            
        }
        else {
            
            mysqli_query($setting["Lid"],'INSERT INTO `views`(`ip`, `user_agent`, `url`, `identifier`) VALUES ("'.$ip.'","'.$_SERVER['HTTP_USER_AGENT'].'","'.$_SERVER['REQUEST_URI'].'", "'.$user->cookie_id.'")');
            
        }
        
        $ddos = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"], "SELECT COUNT(`id`) FROM `views` WHERE `timestamp` > (NOW() - INTERVAL 30 SECOND) AND `ip` = '".$ip."'")));

        if ($ddos > 200) {

            header("location: ".$setting["site_url"]."/ddos");

        }

    }

    function username( $id ) {

        global $setting;

        $query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `username` FROM `users` WHERE `id`='".$id."'");

        $count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if ($count > 0) {

            $row = mysqli_fetch_object($query);
            return $row->username;

        }

    }
    

}
    

class crawler {
    
    public  $html_to_array;

    function url_info($url, $type = 0, $username = "", $password = "", $request_header = 0, $post = 0, $string = "") {
        
        $curl = curl_init();

        global $setting, $html_to_array;
        
        //$html_to_array = new DOMDocument();
        
        $manipulation = new manipulation();

        $cleaner = new MrClean\MrClean();

        $Start = microtime(true);

        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";

        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";

        $header[] = "Cache-Control: max-age=0";

        $header[] = "Connection: keep-alive";

        $header[] = "Keep-Alive: 300";

        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";

        $header[] = "Accept-Language: en-us,en;q=0.5";

        $header[] = "Pragma: ";

        $header[] = "User-Agent: ".$setting["bot"]."";

        curl_setopt($curl, CURLOPT_BINARYTRANSFER,1);

        curl_setopt($curl, CURLOPT_AUTOREFERER, false);

        curl_setopt($curl, CURLOPT_REFERER, 'http://google.com');

        if($type == 2) {

            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            curl_setopt($curl, CURLOPT_USERPWD,  "".$username.":".$password."");

        }

        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        //curl_setopt($curl, CURLOPT_HEADER, 1);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        if(request_header == 1) {

            curl_setopt($curl, CURLOPT_HEADER, TRUE);

        }

        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($curl, CURLOPT_VERBOSE, 1);

        curl_setopt($curl, CURLOPT_USERAGENT, ''.$setting["robot"].'');

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 400);

        curl_setopt($curl, CURLOPT_TIMEOUT, 400);

        $cookie_file = "cookie.txt";

        curl_setopt($curl, CURLOPT_COOKIESESSION, true);

        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);

        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if($post == 1) {

            curl_setopt($curl, CURLOPT_POSTFIELDS, $string);

        }

        $text = curl_exec($curl);

        $array['header'] = curl_getinfo($url, CURLINFO_HEADER_OUT);

        $array['response'] = rtrim($text);

        $array['status'] = mysqli(curl_getinfo($curl, CURLINFO_HTTP_CODE));

        $array['error'] = mysqli(curl_error($curl));

        $array['curl_info'] = curl_getinfo($curl);

        if(request_header == 1) {

            list($rawHeader, $response) = explode("\r\n\r\n", $text, 2);

            $cutHeaders = explode("\r\n", $rawHeader);

            foreach ($cutHeaders as $row) {

                $cutRow = explode(":", $row, 2);

                $array[$cutRow[0]] = trim($cutRow[1]);

            }

        }

        if (curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) != $url) {

            $url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

        }

        $End = microtime(true);

        $time = $End - $Start;	

        $array['time'] = $time;

        $array['url'] = mysqli($url);

        if($type == 1) {
            
            $array['response'] = $manipulation->convert_encoding($text);

            $matches = array('@<script[^>]*?>.*?<\/script>@si', '@<style[^>]*?>.*?<\/style>@si', '@<noscript[^>]*?>.*?<\/noscript>@si');
            
            $array['response'] = preg_replace($matches, ' ', $array['response']);
            
            $array['response'] = $cleaner->scrubbers(['strip_css_attributes'])->scrub($array['response']);
            
            $html = $array['response'];

            $html_to_array->loadHTML($html);

            $html_array_path = new DOMXpath($html_to_array);

        }        

        if($array['response'] === false) {

            trigger_error(curl_error($curl));

        }

        curl_close($curl);

        return $array;

    }

    function site_info(&$array) {

        global $setting, $html_to_array, $html_array_path;
        
        $manipulation = new manipulation();
        
        $verifier = new verifier();

        if($array['status'] == 200) {

            $meta = array('1' => 'charset', '2' => 'itemprop', '3' => 'http-equiv', '4'=> 'property', '5' => 'name');

            $array['content'] = mysqli( $manipulation->indextext($array['response']));

            $nodes = $html_to_array->getElementsByTagName('title');

            $array['title'] = $nodes->item(0)->nodeValue;

            $matchs = $html_to_array->getElementsByTagName('meta');

            foreach($matchs as $match) {

                foreach ($meta as $key => $names) {

                    $name = mysqli($match->getAttribute($names));

                    if (!empty($name) && (!$verifier->space($name))) {

                        $content = mysqli($match->getAttribute('content'));

                        $array[$name] = mysqli( $manipulation->indextext($content));

                    }

                }

            }

            $titleheaders = self::content($array);

            $array = array_merge($titleheaders, $array);
            
        }

        $array = array_filter($array);

        array_multisort($array, SORT_STRING);

        return $array;

    }

    function content($array) {

        global $html_to_array;
        
        $manipulation = new manipulation();
        
        $verifier = new verifier();

        $main_parse = parse_url($array['curl_info']["url"]);

        $patterns_all = array("title", "h1", "h2", "h3", "h4", "h5", "h6", "h7", "h8", "h9", "p", "span", "div", "img", "a", "article", "code", "pre", "figure", "figcaption");

        foreach($patterns_all as $key => $pattern) {
        
            //$nodes = $html_to_array->getElementsByTagName($pattern);

            //echo $nodes->item(0)->nodeValue;
            
            $array[$pattern] = array();

            $clone = $html_to_array->cloneNode(True);

            $matchs = $clone->getElementsByTagName($pattern);
                        
            foreach($matchs as $subkey => $match) {
                
                if($pattern == "img") {

                    if( $manipulation->indextext($match->getAttribute("src")) !== "") {

                        $array[$pattern][$subkey]["src"] = mysqli($manipulation->fix_url($match->getAttribute("src"), $main_parse));
                        
                        $array_one = self::url_info($array[$pattern][$subkey]["src"]);
                        
                        $im = imagecreatefromstring($array_one['response']);
                        $width = imagesx($im);
                        $height = imagesy($im);

                        if( $manipulation->indextext($match->getAttribute("alt")) !== "") {

                            $array[$pattern][$subkey]["alt"] = mysqli( $manipulation->indextext($match->getAttribute("alt")));

                        }

                        //$array[$pattern][$subkey]["width"] = mysqli( $manipulation->indextext($match->getAttribute("width")));
                            
                        $array[$pattern][$subkey]["width"] = $width;


                        //$array[$pattern][$subkey]["height"] = mysqli( $manipulation->indextext($match->getAttribute("height")));
                            
                        $array[$pattern][$subkey]["height"] = $height;
                        
                        //$array[$pattern][$subkey]["type"] = $type;
                        
                    }

                    $match->nodeValue = "";

                }
                else if($pattern == "a") {

                    if( $manipulation->indextext($match->getAttribute("href")) !== "") {

                        $array[$pattern][$subkey]["href"] = mysqli($manipulation->fix_url($match->getAttribute("href"), $main_parse));

                        if( $manipulation->indextext($match->nodeValue) !== "") {

                            $array[$pattern][$subkey]["title"] = mysqli( $manipulation->indextext($match->nodeValue));

                        }

                    }

                    $match->nodeValue = "";

                }
                else {

                    $content =  $manipulation->indextext($match->nodeValue);

                    if(!empty($content) && !$verifier->space($content) && !$verifier->check_content($patterns, $array, $content) && (str_word_count($content) > 1)) {

                        if($pattern == "p" || $pattern == "div" || $pattern == "span" || $pattern == "article" ) {
                            
                            if(preg_match('/[\p{P}\p{N}]$/u', $content)) {

                                $array[$pattern][] = mysqli( $manipulation->indextext($content));
                                
                            }

                        }
                        else {

                           $array[$pattern][] = mysqli( $manipulation->indextext($content));

                        }

                        $match->nodeValue = "";

                    }

                }

            }

        }

        return $array;

    }

    function itemscope(&$array) {
        
        $manipulation = new manipulation();

        $main_parse = parse_url($array["url"]);

        $html = str_get_html($array['response']);

        $match = $html->find('[itemscope]');

        foreach($match as $key => $itemscope) {
            
            if (!empty($itemscope)) {

                //$array["itemscope"][$key][$itemscope->itemprop]["outertext"] = mysqli($itemscope->outertext);

                //$array["itemscope"][$key][$itemscope->itemprop]["plaintext"] = mysqli($itemscope->plaintext);

                $itemtype =  $manipulation->indextext($itemscope->itemtype);

                $itemprop =  $manipulation->indextext($itemscope->itemprop);

                if($itemtype !== "") {

                    if( $manipulation->indextext($itemprop) !== "") {

                        $array["itemscope"][$key]["itemprop"] = mysqli($itemprop);

                    }
                    else {

                        $parse_url = parse_url($itemtype);

                        $array["itemscope"][$key]["itemprop"] = mysqli(strtolower(substr($parse_url["path"], 1)));

                    }

                    $array["itemscope"][$key]["itemtype"] = mysqli($itemtype);


                }

                if(empty($array[$key]["itemprop"]) && $itemprop !== "") {

                    $array["itemscope"][$key]["itemprop"] = mysqli($itemprop);

                }

                foreach($itemscope->find('[itemprop]') as $subkey => $itemprop) {
                    
                    if (!empty($itemprop)) {

                        $itemprop_1 =  $manipulation->indextext($itemprop->itemprop);

                        $itemptype =  $manipulation->indextext($itemprop->itemtype);

                        $outertext =  $manipulation->indextext($itemprop->outertext);

                        if($itemptype !== "") {

                            if($itemprop_1 !== "") {

                                $array["itemscope"][$key][$itemprop_1]["itemprop"] = mysqli($itemprop_1);

                            }
                            else {

                                $parse_url = parse_url($itemptype);

                                $array["itemscope"][$key][$itemprop_1]["itemprop"] = mysqli(strtolower(substr( $manipulation->indextext($parse_url["path"]), 1)));

                            }

                            $array["itemscope"][$key][$itemprop_1]["itemtype"] = mysqli($itemptype);


                        }

                        if(empty($array[$key][$itemprop_1]["itemprop"]) && $itemprop_1 !== "") {

                            $array["itemscope"][$key][$itemprop_1]["itemprop"] = mysqli($itemprop_1);

                        }

                        if($outertext  !== "") {

                            $array["itemscope"][$key][$itemprop_1]["plaintext"] = mysqli($outertext);

                        }

                        if($itemptype !== "") {

                            $array["itemscope"][$key][$itemprop_1]["itemtype"] = mysqli($itemptype);

                        }

                        if( $manipulation->indextext($itemprop->src) !== "") {

                            $itemsrc = $itemprop->src;

                        }
                        else if( $manipulation->indextext($itemprop->find('[itemprop="image"]')[0]) !== "") {

                            $itemsrc = $itemprop->find('[itemprop="image"]')[0]->scr;

                        }
                        else if( $manipulation->indextext($itemprop->find('[src]')[0]) !== ""){

                            $itemsrc = $itemprop->find('[src]')[0]->scr;

                        }

                        $array["itemscope"][$key][$itemprop_1]["scr"] = mysqli( $manipulation->indextext($manipulation->fix_url($itemsrc, $main_parse)));


                        if( $manipulation->indextext($itemprop->href) !== "") {

                            $itemhref = $itemprop->href;

                        }
                        else if( $manipulation->indextext($itemprop->find('[itemprop="url"]')[0]) !== ""){

                            $itemhref = $itemprop->find('[itemprop="url"]')[0]->href;

                        }
                        else if( $manipulation->indextext($itemprop->find('[href]')[0]) !== "") {

                            $itemhref = $itemprop->find('[href]')[0]->href;

                        }

                        $array["itemscope"][$key][$itemprop_1]["href"] = mysqli( $manipulation->indextext($manipulation->fix_url($itemhref, $main_parse)));



                        if($itemprop->tag == "meta") {

                            $array["itemscope"][$key][$itemprop_1]["content"] = mysqli( $manipulation->indextext($itemprop->content));

                        }


                        if($outertext !== "" && $array["itemscope"][$key][$itemprop_1]["plaintext"] !== $outertext) {

                            $array["itemscope"][$key][$itemprop_1]["outertext"] = mysqli($outertext);

                        }
                        
                    }

                }

                $match[$key]->outertext = "";
                
            }

        }

        return $array;

    }

    function redirection($url) {

        $headers = get_headers($url);

        foreach($headers as $header) {

            if (preg_match('/^Location: (.+?)$/m', $header, $match)) {

                $parse = parse_url($url);

                $url=trim($match[1]);

                if (substr($match[1], 0, 1) == "/") {

                    $url=$parse['scheme']."://".$parse['host'].trim($match[1]);

                }

            }

        }

        return $url;

    }
    
    
}

class manipulation {

    function indextext($string) {

        $cleaner = new MrClean\MrClean();

        $array = array('@<script[^>]*?>.*?<\/script>@si',
        '@<noscript[^>]*?>.*?<\/noscript>@si',
        '@<header[^>]*?>.*?<\/header>@si',
        '@<nav[^>]*?>.*?<\/nav>@si',
        '@<style[^>]*?>.*?<\/style>@si',
        '@<link rel[^<>]*?>@si',
        '@<footer[^>]*?>.*?<\/footer>@si',
        '@<![\s\S]*?--[ \t\n\r]*>@si',
        '/&[a-z]{1,6};/',
        '/&nbsp;/',
        '@\s\s+@',
        '@\s+@',
        '@<!--sphider_noindex-->.*?<!--\/sphider_noindex-->@si',
        '@<!--.*?-->@si',
        '/(<|>)\1{2}/si',
        '@<head[^>]*?>.*?<\/head>@si'
        );

        $string = preg_replace($array, ' ', $string);

        $string = filter_var($string, FILTER_SANITIZE_STRING);

        $string  = $cleaner->scrubbers(['strip_tags'])->scrub($string);
        $string = $cleaner->scrubbers(['trim'])->scrub($string);

        return $string;

    }
    
    function convert_encoding($string, $encoding = 'UTF-8') {

        $string = html_entity_decode($string);

        $array = utf8_split($string);

        foreach($array as $key => $main) {

            if(utf8_decode($main) != $main) {

                $main = mb_convert_encoding($main, 'HTML-ENTITIES', $encoding);

            }

            $array[$key] = $main;

        }

        $string = implode("", $array);

        $string = utf8_trim(utf8_clean($string));

        return $string;

    }

    function bd_nice_number($n) {

        // first strip any formatting;
        $n = (0+str_replace(",","",$n));

        // is this a number?
        if(!is_numeric($n)) return false;

        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),1).' trillion';

        else if($n>1000000000) return round(($n/1000000000),1).' billion';

        else if($n>1000000) return round(($n/1000000),1).' million';

        else if($n>1000) return round(($n/1000),1).' thousand';

        return number_format($n);

    }

    function limit_text( $string, $limiter ) {

        $count = str_word_count($string, 2);

        $key = array_keys($count);

        $length = strlen($string);

        $word_count = str_word_count($string);

        $ratio = $length/$word_count;

        if($ratio != 0) {

            $new_word_count = $length/$ratio;

            $difference = $word_count/$new_word_count;

            $limiters = round($difference * $limiter);

        }

        if($limiters < $limiter) {

            $limiter = $limiters;

        }

        if (count($count) > $limiter) {

            $string = trim(substr($string, 0, $key[$limiter])).'&#8230;';

        }

        return $string;

    }

    function limiter($string, $limit, $arrays) {

        foreach($arrays as $array) {

            $string = implode($array,array_splice(explode($array,$string),0,$limit));

        }

        return $string;
    }


    function time_elapsed_string( $timestamp ) {
                
        $timestamp = strtotime($timestamp);
        
        $time = $timestamp;

        $etime = abs(time() - $time);

        if ($etime < 1) {

            return '0 seconds';

        }

        $array = array( 12 * 30 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
            );

        foreach ($array as $secs => $string) {

            $dtime = $etime / $secs;

            if ($dtime >= 1) {

                $rtime = round($dtime);
                return $rtime . ' ' . $string . ($rtime > 1 ? 's' : '') . ' ago';

            }

        }
  
    }

    function time_Ago($timestamp) { 

        $time = strtotime($timestamp);

        // Calculate difference between current 
        // time and given timestamp in seconds 
        $diff     = abs(time() - $time); 

        // Time difference in seconds 
        $sec     = $diff; 

        // Convert time difference in minutes 
        $min     = round($diff / 60 ); 

        // Convert time difference in hours 
        $hrs     = round($diff / 3600); 

        // Convert time difference in days 
        $days     = round($diff / 86400 ); 

        // Convert time difference in weeks 
        $weeks     = round($diff / 604800); 

        // Convert time difference in months 
        $mnths     = round($diff / 2600640 ); 

        // Convert time difference in years 
        $yrs     = round($diff / 31207680 ); 

        // Check for seconds 
        if($sec <= 60) { 
            return $sec." seconds ago"; 
        } 

        // Check for minutes 
        else if($min <= 60) { 

            if($min==1) { 
                return "one minute ago"; 
            } 
            else { 
                return $min." minutes ago"; 
            } 

        }
        else if($hrs <= 24) { 

            if($hrs == 1) {  
                return "an hour ago"; 
            } 
            else { 
                return $hrs." hours ago"; 
            } 

        }
        else if($days <= 7) { 

            if($days == 1) { 
                return "Yesterday"; 
            } 
            else { 
                return $days." days ago"; 
            } 

        } 
        else if($weeks <= 4.3) { 

            if($weeks == 1) { 
                return "a week ago"; 
            } 
            else { 
                return $weeks." weeks ago"; 
            } 

        } 
        else if($mnths <= 12) { 

            if($mnths == 1) { 
                return "a month ago"; 
            } 
            else { 
                return $mnths." months ago"; 
            } 

        } 
        else { 

            if($yrs == 1) { 
                return "one year ago"; 
            } 
            else { 
                return $yrs." years ago"; 

            } 

        } 

    } 

    function main_domain($url) {

        global $extension;

        $parse = parse_url($url);

        $url = $parse['host'];

        if (substr($url,0 , 4) == 'www.') {

            $url = substr($url, 4);

        }

        $domain = explode(".", $url);

        $count = count($domain);
        
        //print_r($domain);
        
        if($count <= 2) {
            
            $extention["1"] = $domain[$count-1];
            
            $main_host = $domain[$count-2];
            
        }
        else {
            
            $extention["1"] = $domain[$count-1];

            $extention["2"] = $domain[$count-2];
            
            $domain_extension_1 = ".".$extention["1"];
            
            $domain_extension_2 = ".".$extention["2"].".".$extention["1"];

            if (in_array($domain_extension_1, $extension)) {
                
                $main_host = $domain[$count-2];
                
                $domain_extension = $domain_extension_1;
                
                if($count >= 3) {
                    
                    $sub_domain = array_slice($domain, 0, ($count-3));

                }

            }
            else if (in_array($domain_extension_2, $extension)) {
                
                $main_host = $domain[$count-3];
                
                $domain_extension = $domain_extension_2;
                
                if($count >= 4) {
                    
                    $sub_domain = array_slice($domain, 0, ($count-4));

                }


            }
            else  {
                
                $domain_1 = "http://".$domain[$count-2]."".$domain_extension_1;
                
                $http_response_header= file_get_contents("http://".$domain_1, false);
                
                if($http_response_header == false) {
                    
                    $main_host = $domain[$count-3];
                    
                    $domain_extension = $domain_extension_2;
                    
                    $sub_domain = array_slice($domain, 0, ($count-4));
                    
                }
                else {
                    
                    $main_host = $domain[$count-2];
                    
                    $domain_extension = $domain_extension_1;
                    
                    $sub_domain = array_slice($domain, 0, ($count-3));

                    
                }
                
                
            }
            
            
        }
        
        
        $array['url'] = $main_host.$domain_extension;

        if($array['url'] == "") {

            $array['url'] = $domain;

        }

        $array['sub'] = $sub_domain;

        $array['main'] = $main_host;

        $array['extension'] = $domain_extension;

        return $array;

    }
    
    function fix_url($match, $parse) {
        
        $verifier = new verifier();

        //$path = pathinfo($parse['path']);

        //$parse['path'] = $path['dirname'];
        
        if(!empty($match) && !preg_match('/mailto|data|http:\/\/|https:\/\//i', $match)) {
                            
            $parse_match = parse_url($match);            

            if($verifier->space($parse_match['host'])) {
            
                $parse_match['path'] = self::removeLastDir($parse_match['path']);
                                
                $path_explode = explode("/", $parse_match['path']);

                strpos($path_explode['0'], ".");

                if(strpos($path_explode['0'], ".") > 0) {

                    $http_response_header = file_get_contents("http://".$path_explode['0'], false);

                    if($http_response_header != false) {

                        $parse_match['host'] = $path_explode['0'];

                        $parse_match['path'] = str_replace($path_explode['0'], "",  $match);

                    }

                }
                
            }
            
            
            if(!$verifier->space($parse['path'])) {

                if(substr($match, 0, 1) == '/') {

                    $match = substr($match, 1);

                }

                $match = $parse['path']."/".$match;

            }
                                    
            //https://www.phpliveregex.com/
            
            if(preg_match('/www.|\/\/|:\/\//i', $match)) {
                
                $match = str_replace(array(':', '//'), "",  $match);

                if(preg_match("/".$parse['host']."/i", $match)) {

                    $match = $parse['scheme']."://".$match;

                }
                else {

                    $match = "http://".$match;

                }

            }
            else {

                if(substr($match, 0, 1) == '/') {

                    $match = substr($match, 1);

                }

                $match = $parse['scheme']."://".$parse['host']."/".$match;
                
            }

        }

        $match = filter_var($match, FILTER_SANITIZE_URL);

        return $match;

    }
    
    function removeLastDir($path) {
	
        if(substr($path, 0, 1) == '/') {

            $path = substr($path,0, -1);

        }
        
        $paths = array_filter(explode("/", $path));
            
        $count_extention = array_count_values($paths);
        
        $sum_extention = $count_extention["."] + $count_extention[".."];
        
        $remove_limit = $sum_extention*2;
        
        $paths = array_splice($paths, $remove_limit, count($paths));
                        
        return implode("/", $paths);
    }
    
    function getRealIpAddr() {

        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        if($ipaddress = "::1") {
            $ipaddress = "2a02:c7f:724d:7200:2d00:212d:ab11:1343
    ";
        }

        return $ipaddress;
    }
    
    function htmlstring( $html ) {

        $replace=array('<','>');

        $to=array('&lt;','&gt;');

        return htmlspecialchars(str_ireplace($replace,$to, $html));

    }
    
    
    function get_headings_tag($html) {

        $headings = array(
            'h1' => array(),
            'h2' => array(),
            'h3' => array(),
            'h4' => array(),
            'h5' => array(),
            'h6' => array(),
        );

        $pattern = "<(h[1-6]{1})(.+)?>(.*)</h[1-6]{1}(?:[^>]*)>";

        preg_match_all("#{$pattern}#iUs",$html, $matches);

        $sizes = isset($matches[1]) ? $matches[1] : array();

        foreach($sizes as $id => $size) {

            $headings[strtolower($size)][] = strip_tags(trim($matches[3][$id]));

        }

        return $headings;

    }
    
    function sum_multi_dimentional($data) {
        
        foreach ($data as $key => $sub_array) {

          foreach ($sub_array as $id => $value) {

            $data_1[$id] += $value;

          }
            
        }
    
        return $data_1;
    
    }
    
    function probaility($data) {
            
        $data_sum = array_sum($data);
        
        foreach($data as $key => $count) {
            $array[$key] = ((($count)+1)/($data_sum));
        }
        
        return $array;
        
    }
    
    function search_engine_query_string($url = false) {

        if(!$url && !$url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false) {
            return '';
        }

        $parts_url = parse_url($url);
        
        $query = isset($parts_url['query']) ? $parts_url['query'] : (isset($parts_url['fragment']) ? $parts_url['fragment'] : '');
        
        if(!$query) {
            return '';
        }
        
        parse_str($query, $parts_query);
        
        return isset($parts_query['q']) ? $parts_query['q'] : (isset($parts_query['p']) ? $parts_query['p'] : '');

    }


}

class generator_one {

    function random_color_part() {

        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);

    }

    function random_color() {

        return random_color_part() . random_color_part() . random_color_part();

    }

    function DateRangeArray($from, $to) {

        $array=array();

        $from = mktime(1, 0, 0, substr($from, 5, 2), substr($from, 8, 2), substr($from, 0, 4));

        $to = mktime(1, 0, 0, substr($to, 5, 2), substr($to, 8, 2), substr($to, 0, 4));

        if ($to >= $from) {

            array_push($array, date('Y-m-d',$from));

            while ($from < $to) {

                $from+= 86400;

                array_push($array, date('Y-m-d',$from));

            }

        }

        return $array;

    }

    function randomurl( $alphabet ) {

        $pass = array();

        $alphaLength = strlen($alphabet) - 1;

        for ($i = 0; $i < 8; $i++) {

            $new = rand(0, $alphaLength);

            $pass[] = $alphabet[$new];

        }

        return implode($pass);

    }



}

class verifier {

    function validate_email($email){

        if(preg_match("/^[_.\da-z-]+@[a-z\d][a-z\d-]+\.+[a-z]{2,6}/i",$email)){

            if(checkdnsrr(array_pop(explode("@",$email)),"MX")){

                return true;

            }
            else{

                return false;

            }

        }
        else {

            return false;

        }

    }


    function space($string) {

        $patterns = array('1' => '/\s\s+/i', '2' => '/[^a-zA-Z0-9 -]/', '3' => '/[^[:alpha:]]/', '4' => '/[^a-zA-Z]+/');

        $string = preg_replace($patterns, ' ', $string);

        if($string == "") {

            return true;

        }
        else if(str_ireplace(" ","",preg_replace('/\s+/', '', $string))=="") {

            return true;

        }
        else {

            return false;

        }

    }
    
    
    function mime_content_types($filename) {

        global $setting;

        $headers = get_headers($filename, 1);

        if (array_search($headers["Content-Type"], $setting["game_types"])) {

            return $headers["Content-Type"];

        }
        else if (function_exists('finfo_open')) {

            $finfo = finfo_open(FILEINFO_MIME);

            $mimetype = finfo_file($finfo, $filename);

            finfo_close($finfo);

            return $mimetype;

        }

    }
    
    function check_content(&$patterns, &$array, $string) {

        foreach($patterns as $key => $pattern) {

            $arrays = $array[$pattern];

            foreach($arrays as $keys => $match) {

                if($match == $string) {

                    return true;

                    break;

                }

            }

        }

    }
    
}

//functions needed 

function get_text_information_data ($text, $trained_data) {
            
    global $Sentiments, $Categorys, $setting, $category_array;

    $value_paragraph = 2;

    $new_ai = new AI_WAHEED;
    
    $paragraphs = $new_ai->breakLongText($new_ai->sentenceCase($text));

    $count_paragraphs = round((count($paragraphs)/$value_paragraph), 0, PHP_ROUND_HALF_UP);

    for ($i = 0; $i <= $count_paragraphs ;$i++) {

         $setence_number = (($i)*$value_paragraph);

         $new_array_sentence = array_slice($paragraphs, ($setence_number), ($value_paragraph));

         $new_paragraphs[$i] = '<p class="post_p">'.implode(" ", $new_array_sentence)." </p>";


    }

    $array['content_news'] = implode(" ", $new_paragraphs);


    $api = new TextRankFacade();
    // English implementation for stopwords/junk words:

    $stopWords = new English();
    $api->setStopWords($stopWords);

    // Array of the sentences from the most important part of the text:
    $result_highlights = $api->getHighlights($text);
    $array['results']['summary']['highlights'] = $result_highlights;
    $text_highlights = stripslashes(implode(" ", $array['results']['summary']['highlights']));


    // Array of the most important sentences from the text:
    $result_summary = $api->summarizeTextBasic($text);
    $array['results']['summary']['summary'] = $result_summary;     

    $rake_text = preg_replace('/[^a-z0-9]+/i', ' ', $text);
    // Note: en_US is the default language.
    $rake = RakePlus::create($rake_text,  $setting['stopword_2']);
    $array['results']['phrases'] = $phrase_scores = $rake->sortByScore('asc')->scores();

    // start of sentiment anaysis//
    //https://blog.cambridgespark.com/50-free-machine-learning-datasets-sentiment-analysis-b9388f79c124

    $train_system = $setting["training_set"];

    $sentimentAnalysisOfSentence = array();
    
    
    //'/(?<=[.?!])\s+(?=[a-z])/i'
            
        
    $label_score_category = $new_ai->long_text_prediction($trained_data['category'], $text);
                
    $label_score_sentiment = $new_ai->long_text_prediction($trained_data['sentiment'], $text);

    
    $array['results']['sentiment']['sentiment']  = array_keys($label_score_sentiment, max($label_score_sentiment))['0'];
    
    
    $array['results']['category']['category'] = array_keys($label_score_category, max($label_score_category))['0'];
    
    $textStatistics = new TextStatistics;
    $text_content = strip_tags($array['content_news']);

    $array['readability']['fleschKincaidGradeLevel'] = $textStatistics->fleschKincaidGradeLevel($text_content);
    $array['readability']['gunningFogScore'] = $textStatistics->gunningFogScore($text_content);
    $array['readability']['smogIndex'] = $textStatistics->smogIndex($text_content);
    $array['readability']['spacheReadabilityScore'] = $textStatistics->spacheReadabilityScore($text_content);
    $array['readability']['automatedReadabilityIndex'] = $textStatistics->automatedReadabilityIndex($text_content);
    $array['readability']['colemanLiauIndex'] = $textStatistics->colemanLiauIndex($text_content);
    $array['readability']['daleChallReadabilityScore'] = $textStatistics->daleChallReadabilityScore($text_content);
    
    $array['readability']["average"] = round(array_sum($array['readability'])/count($array['readability']), 1);

    return $array;
    
}

class colorTests {
    public function hexColorToDec($color) {
    // Stole it from: http://www.anyexample.com/p...
        if($color[0] == '#') {
            $color = substr($color, 1);
        }

        if(strlen($color) == 6) {
            list($r, $g, $b) = array($color[0].$color[1],
            $color[2].$color[3],
            $color[4].$color[5]);
        }
        elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        }
        else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array($r, $g, $b);
    }

    public function colorDiff($c1, $c2) {
        $c1 = $this->hexColorToDec($c1);
        $c2 = $this->hexColorToDec($c2);

        return max($c1[0],$c2[0]) - min($c1[0],$c2[0]) +
        max($c1[1],$c2[1]) - min($c1[1],$c2[1]) +
        max($c1[2],$c2[2]) - min($c1[2],$c2[2]);
    }

    public function brightDiff($c1, $c2) {
        $c1 = $this->hexColorToDec($c1);
        $c2 = $this->hexColorToDec($c2);
        $BR1 = (299 * $c1[0] + 587 * $c1[1] + 114 * $c1[3]) / 1000;
        $BR2 = (299 * $c2[0] + 587 * $c2[1] + 114 * $c2[3]) / 1000;

        return abs($BR1-$BR2);
    }

    public function lumDiff($c1, $c2) {
        $c1 = $this->hexColorToDec($c1);
        $c2 = $this->hexColorToDec($c2);
        $L1 = 0.2126 * pow($c1[0]/255, 2.2) +
        0.7152 * pow($c1[1]/255, 2.2) +
        0.0722 * pow($c1[2]/255, 2.2);

        $L2 = 0.2126 * pow($c2[0]/255, 2.2) +
        0.7152 * pow($c2[1]/255, 2.2) +
        0.0722 * pow($c2[2]/255, 2.2);

        if($L1 > $L2) {
            return ($L1+0.05) / ($L2+0.05);
        }
        else {
            return ($L2+0.05) / ($L1+0.05);
        }
    }

    public function pythDiff($c1, $c2) {
        $c1 = $this->hexColorToDec($c1);
        $c2 = $this->hexColorToDec($c2);
        $RD = $c1[0] - $c2[0];
        $GD = $c1[1] - $c2[1];
        $BD = $c1[2] - $c2[2];

        return sqrt($RD * $RD + $GD * $GD + $BD * $BD) ;
    }
}


//USER

$user_data= new user_data;

$setting["user"] = $user=$user_data->getUser();


?>
