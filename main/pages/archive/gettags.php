<?php

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

$event_main_query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `timestamp` FROM `events` WHERE `timestamp` + INTERVAL 1 HOUR >= now() AND `event` = 'Tags Update' AND `done` !=`jobs` AND `done` < `jobs` ORDER BY `timestamp` DESC");

$event_count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

$main_query = mysqli_query($setting["Lid"],'SELECT SQL_CALC_FOUND_ROWS `id`, `hash_id`, `tag`,`color`, `publications` FROM `tags`');

$jobs_total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

if ($event_count == 0) {

    mysqli_query($setting['Lid'],"INSERT INTO `events` (`event`, `jobs`) VALUES ('Tags Update', '".$jobs_total_count."')");

    $event_id = mysqli_insert_id($setting["Lid"]);
    
}
else if($event_count > 0) {
    
    $event_main_row = mysqli_fetch_object($event_main_query);
    
    $event_id = $event_main_row->id;
    
    if($event_count > 1) {
        $event_delete_query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `timestamp` FROM `events` WHERE `timestamp` + INTERVAL 1 HOUR >= now() AND `event` = 'Tags Update' AND `done` !=`jobs` AND `done` < `jobs` AND  `Id` NOT IN (".$event_main_row->id.") ORDER BY `timestamp` DESC");
        while($event_delete_row = mysqli_fetch_object($event_delete_query)) {

            mysqli_query($setting['Lid'],"DELETE FROM `events` WHERE `Id` = '".$event_main_row->id."'");
            mysqli_query($setting['Lid'],"DELETE FROM `tags` WHERE `event` = '".$event_main_row->id."'");

        }
        
    }
    
}


while($row = mysqli_fetch_object($main_query)) {
    
    $exist_query_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `publications`, `true_publications` FROM `Popularity` WHERE `tag_id`='".$row->id."' AND `event`='".$event_id."' ORDER BY `Popularity`.`id` DESC");

    $exist_total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
    
    if($exist_total_count == 0) {

        $query_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `publications`, `true_publications` FROM `Popularity` WHERE `tag_id`='".$row->id."' ORDER BY `Popularity`.`id` DESC");

        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if ($total_count > 0) {

            $row_content = mysqli_fetch_object($query_content);

            $change_publications = $row->publications-$row_content->true_publications;

        }
        else {

            $change_publications = $row->publications;

        }

        mysqli_query($setting['Lid'],"INSERT INTO `Popularity` (`tag_id`,`true_publications`, `publications`, `event`) VALUES ('".$row->id."', '".$row->publications."', '".$change_publications."', '".$event_id."')");

        mysqli_query($setting['Lid'],"UPDATE `events` SET `done` = `done` +1 WHERE `events`.`Id` = '".$event_id."';");
        
    }
        
}

//while($row = mysqli_fetch_object($main_query)) {

//$sql = "UPDATE notification SET yes = 1 WHERE and timestamp >= now() - INTERVAL 1 HOUR"


?>