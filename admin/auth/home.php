<?php
   
use UAParser_a\Parser_a;

function tz_offset_to_name($offset)
{
        $abbrarray = timezone_abbreviations_list();
        foreach ($abbrarray as $abbr)
        {
                foreach ($abbr as $city)
                {
                        if ($city['offset'] == $offset)
                        {
                                return $city['timezone_id'];
                        }
                }
        }

        return FALSE;
}



function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
                                    : DateTimeZone::listIdentifiers();

    if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

        $time_zone = '';
        $tz_distance = 0;

        //only one identifier?
        if (count($timezone_ids) == 1) {
            $time_zone = $timezone_ids[0];
        } else {

            foreach($timezone_ids as $timezone_id) {
                $timezone = new DateTimeZone($timezone_id);
                $location = $timezone->getLocation();
                $tz_lat   = $location['latitude'];
                $tz_long  = $location['longitude'];

                $theta    = $cur_long - $tz_long;
                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat))) 
                + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                $distance = acos($distance);
                $distance = abs(rad2deg($distance));
                // echo '<br />'.$timezone_id.' '.$distance; 

                if (!$time_zone || $tz_distance > $distance) {
                    $time_zone   = $timezone_id;
                    $tz_distance = $distance;
                } 

            }
        }
        return  $time_zone;
    }
    return 'unknown';
}


if($user->admin == 1) {

	require_once('include/header.php');

	require_once('include/main_header.php');

	require_once('include/main_nav.php');
    
    
    $total_view_implode = implode(", ", array_splice($array_view, 0, 12));
    
    $date_range_end = date('Y-m-d', strtotime('today - 30 days'));
    
    $today_range_end = date('Y-m-d', strtotime('today + 1 days'));
    
    $date_range = $generator->DateRangeArray($date_range_end, $today_range_end);
    
    $new_date = array();
    
    $impression_query = mysqli_query($setting["Lid"], "SELECT COUNT(`id`) AS `hits1`, date(`timestamp`) AS `date` FROM `views` GROUP BY CAST(`timestamp` AS DATE)");
    
    while($impression_row = mysqli_fetch_array($impression_query)) {
        $array_impression[$impression_row['date']] = $impression_row['hits1'];
        $array_impression2[$impression_row['date']] = $impression_row['hits1'];
    }
    
           
    $map_query = mysqli_query($setting["Lid"],"SELECT COUNT(`temp`.`id`) AS `hits1`, date(`timestamp`) as `date` FROM 
    (SELECT '1' as `hits`, `timestamp`, `id` FROM `user_geo_location`) AS `temp`   
    GROUP BY CAST(`temp`.`timestamp` AS DATE)");
    
    while($map_row = mysqli_fetch_array($map_query)) {
        
        $array_vistor[$map_row['date']] = $map_row['hits1'];
        
    }
    
    //print_r($array_vistor);
    
    $mysql_main_quary =  mysqli_query($setting["Lid"], "SELECT `sentiment`, DATE(`published`) as `date` FROM `news`");

    while($row_data = mysqli_fetch_array($mysql_main_quary)) {

        if($row_data['sentiment'] == 2) {

            $type = "positive";

        }
        else if($row_data['sentiment'] == 1) {

            $type = "negative";

        }

        $date = $row_data['date'];

        if($type == "negative" || $type == "positive") {
            
            if(array_key_exists($type, $new_date[$date])) {

                $new_date[$date][$type]+=1;

            }
            else {

                $new_date[$date][$type] = 1;

            }

        }
        
        
    }
            
    $clicks_query = mysqli_query($setting["Lid"], "SELECT date(`timestamp`) as `date` FROM `clicks` GROUP BY date(`timestamp`)");
    
        //$clicks_query = mysqli_query($setting["Lid"], "SELECT `count`, date(`timestamp`) as `date` FROM `clicks`");
    
    while($clicks_row = mysqli_fetch_array($clicks_query)) {
            
        $count =array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT COUNT(`id`) as `count` FROM `clicks` WHERE date(`timestamp`) = '".$clicks_row['date']."'")));
        
        $array_clicks[$clicks_row['date']] =  $count;        
        
        
    }
    
    foreach($date_range as $key => $date) {
        
        if(array_key_exists($date, $new_date)) {
            
            $complete_date[$date] = $new_date[$date];

            if(array_key_exists("positive", $complete_date[$date])) {

                $positive_date["positive"][] = $complete_date[$date]["positive"];

            }
            else {

                $positive_date["positive"][] = 0;

            }

            if(array_key_exists("negative", $complete_date[$date])) {

                $negative_date["negative"][] = $complete_date[$date]["negative"];

            }
            else {

                $negative_date["negative"][] = 0;

            }
            
            $sum_sentiment_data[] = $complete_date[$date]["positive"] - $complete_date[$date]["negative"];
            
            
        }
        
        if(array_key_exists($date, $array_vistor)) {
            
            $vistor_range[] = $array_vistor[$date];
            
        }
        
        if(array_key_exists($date, $array_clicks)) {
            
            $click_range[] = $array_clicks[$date];
            
        }
        
        
        if(array_key_exists($date, $array_impression2)) {
            
            $impression_range[] = $array_impression2[$date];
            
        }
        
    }
        
    $recent_viewers_sum = array_sum($vistor_range);
    
    $recent_view_sum = array_sum($impression_range);
    
    $view_array_sum = array_sum($array_vistor);
    
    $impression_array_sum = array_sum($array_impression);
    
    $recent_click_sum = array_sum($click_range);
    
    $total_impression_implode = implode(", ", array_splice($array_impression, 0, 12));
    
    $total_view_implode = implode(", ", array_splice($array_vistor, 0, 12));
    
    $sum_sentiment_implode = implode(", ", array_splice($sum_sentiment_data, 0, 12));
        
    $sum_of_sentiment = (array_sum($positive_date["positive"]) - array_sum($negative_date["negative"]));
    
	?>

    <script>
    
       var sentiment_positive = [];
        <?php foreach (array_slice($positive_date["positive"], 0, 12) as $key => $item) : ?>
        sentiment_positive.push(['<?php echo $key?>', <?php echo $item?>]);
        <?php endforeach; ?>
    
        var dash_widget_data = [];
        <?php foreach (array_slice($impression_range, 0, 12) as $key => $item) : ?>
        dash_widget_data.push(['<?php echo $key?>', <?php echo $item?>]);
        <?php endforeach; ?>
    
        var sentiment_negative = [];
        <?php foreach (array_slice($negative_date["negative"], 0, 12) as $key => $item) : ?>
        sentiment_negative.push(['<?php echo $key?>', <?php echo $item?>]);
        <?php endforeach; ?>
    
        var stats_line_data = [];
        <?php foreach (array_slice($click_range, 0, 12) as $key => $item) : ?>
        stats_line_data.push(['<?php echo $key?>', <?php echo $item?>]);
        <?php endforeach; ?>
    
        var status_bar_data = [];
        <?php foreach (array_slice($impression_range, 0, 12) as $key => $item) : ?>
        status_bar_data.push(['<?php echo $key?>', <?php echo $item?>]);
        <?php endforeach; ?>
        
        var stats_bar_2_admin = [];
        <?php foreach (array_slice($vistor_range, 0, 12) as $key => $item) : ?>
        stats_bar_2_admin.push(['<?php echo $key?>', <?php echo $item?>]);
        <?php endforeach; ?>
        
        
        
    </script>

        <?php
        
    //https://stackoverflow.com/questions/8154564/retrieve-rows-less-than-a-day-old?answertab=active#tab-top
        
    //print_r($row_map);
    
               
    $map_query = mysqli_query($setting["Lid"],'SELECT SQL_CALC_FOUND_ROWS `country_code`, DATE(`timestamp`) AS `date` FROM `user_geo_location` WHERE `timestamp` > timestampadd(hour, -24, now()) ORDER BY `timestamp` ASC');
    
    $main_count_map_24_hours=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

    
    $map_query_30_minutes = mysqli_query($setting["Lid"],'SELECT SQL_CALC_FOUND_ROWS `id` FROM `user_geo_location` WHERE `timestamp` > timestampadd(minute, -30, now()) ORDER BY `timestamp` ASC');

    $main_count_map_30_miutes=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
    
    while($row_maps = mysqli_fetch_object($map_query)) {
                
        $country_code_row[] = $row_maps->country_code;
        
        
        if(!isset($country_codes[$row_maps->country_code])) {
            $country_codes[$row_maps->country_code] = 1;
        }
        else {
            $country_codes[$row_maps->country_code]+=1;
        }
        
    }
    
    ?>
        
    <script>
            var mapData = {
                <?php 
                foreach($country_codes as $key => $value) {
                    echo '"'.$key.'":'.$value.',';
                }
                ?>
            };
    </script>

    <!-- Main Menu area End-->
    <!-- Start Status area -->
    <div class="notika-status-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter"><?=$view_array_sum?></span></h2>
                            <p>Total Website Traffics</p>
                        </div>
                        <div class="sparkline-bar-stats1"><?=$total_view_implode?></div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter"><?=$impression_array_sum?></span></h2>
                            <p>Website Impressions</p>
                        </div>
                        <div class="sparkline-bar-stats2"><?=$total_impression_implode?></div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                    <div class="wb-traffic-inner notika-shadow sm-res-mg-t-30 tb-res-mg-t-30 dk-res-mg-t-30">
                        <div class="website-traffic-ctn">
                            <h2><span class="counter"><?=$sum_of_sentiment?></span></h2>
                            <p>Sentiment</p>
                        </div>
                        <div class="sparkline-bar-stats3"><?=$sum_sentiment_implode?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Status area-->
    <!-- Start Sale Statistic area-->
    <div class="sale-statistic-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                    <div class="sale-statistic-inner notika-shadow mg-tb-30">
                        <div class="curved-inner-pro">
                            <div class="curved-ctn">
                                <h2>Sentiment Statistics</h2>
                                <p>Here you will find the most recent news sentiment statistics, Please note this is only intended to be used as an indicator that measures economic sentiment using AI based word based text classification.</p>

                            </div>
                        </div>
                        <div id="curved-line-chart" class="flot-chart-sts flot-chart"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
                    <div class="statistic-right-area notika-shadow mg-tb-30 sm-res-mg-t-0">
                        <div class="past-day-statis">
                            <h2>For The Past 30 Days</h2>
                            <p>The below shows the most recent - 30 day - user statistics.</p>
                        </div>
						<div class="dash-widget-visits"></div>
                        <div class="past-statistic-an">
                            <div class="past-statistic-ctn">
                                <h3><span class="counter"><?=$recent_view_sum?></span></h3>
                                <p>Page Views</p>
                            </div>
                            <div class="past-statistic-graph">
                                <div class="stats-bar"></div>
                            </div>
                        </div>
                         <div class="past-statistic-an">
                            <div class="past-statistic-ctn">
                                <h3><span class="counter"><?=$recent_click_sum?></span></h3>
                                <p>Total Clicks</p>
                            </div>
                            <div class="past-statistic-graph">
                                <div class="stats-line"></div>
                            </div>
                        </div>
                        <div class="past-statistic-an">
                            <div class="past-statistic-ctn">
                                <h3><span class="counter"><?=$recent_viewers_sum?></span></h3>
                                <p>Site Visitors</p>
                            </div>
                            <div class="past-statistic-graph">
                                <div class="stats-bar-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Sale Statistic area-->

    <!-- Start Email Statistic area-->
    <div class="realtime-statistic-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 tb-res-ds-n dk-res-ds">
                    <div class="recent-post-wrapper notika-shadow sm-res-mg-t-30">
                        <div class="recent-post-ctn">
                            <div class="recent-post-title">
                                <h2>Recent Posts</h2>
                            </div>
                        </div>
                        <div class="recent-post-items">
                    
                            <?php
                    
                            if($setting['user']->model !== null) {

                                $model_output = $get_content_article->get_content_user_model(6, 1);

                                //print_r($model_output['results']);

                                foreach($model_output as $key => $row) {

                                    $description = $manipulation->limit_text(strip_tags($row['summary']),5);

                                    $title = $manipulation->limit_text(strip_tags($row['title']),4);

                                    $author_row = $get_content_article->get_author_name($row['author']);

                                    $author = $manipulation->limit_text(strip_tags($author),3);

                                    ?>

                                      <div class="recent-post-signle rct-pt-mg-wp">
                                        <a href="?<?=$setting["admin_url"]?>/?type=editnews&id=<?=$row['id']?>">
                                            <div class="recent-post-flex">
                                                <div class="recent-post-img">
                                                    <img src="img/post/2.jpg" alt="" />
                                                </div>
                                                <div class="recent-post-it-ctn">
                                                    <h2><?=$author_row["name"]?></h2>
                                                    <p><?=$title?></p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <?php
                                        
                                }
                                
                            }
                            else {

                                $query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `title`,`id`, `category`, date(`timestamp`) as `date`, `sentiment`, `summary`, `author` FROM `news` ORDER BY `id` DESC LIMIT 7");

                                $count=array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));

                                if($count > 0) { 

                                    while ($row = mysqli_fetch_object($query)) {

                                    $description = $manipulation->limit_text(strip_tags($row->summary),5);

                                    $title = $manipulation->limit_text(strip_tags($row->title),4);

                                    $author_row = $get_content_article->get_author_name($row->author);

                                    $author = $manipulation->limit_text(strip_tags($author),3);


                                    ?>

                                    <div class="recent-post-signle rct-pt-mg-wp">
                                        <a href="?<?=$setting["admin_url"]?>/?type=editnews&id=<?=$row->id?>">
                                            <div class="recent-post-flex">
                                                <div class="recent-post-img">
                                                    <img src="img/post/2.jpg" alt="" />
                                                </div>
                                                <div class="recent-post-it-ctn">
                                                    <h2><?=$author_row ["name"]?></h2>
                                                    <p><?=$title?></p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <?php
                                    }

                                }
                                
                            }

                            ?>
                            <div class="recent-post-signle">
                                <a href="#">
                                    <div class="recent-post-flex rc-ps-vw">
                                        <div class="recent-post-line rct-pt-mg">
                                            <p><a href="<?=$setting["admin_url"]?>/?type=news">View All</a></p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                    <div class="recent-items-wp notika-shadow sm-res-mg-t-30">
                        <div class="rc-it-ltd">
                            <div class="recent-items-ctn">
                                <div class="recent-items-title">
                                    <h2>Training Set</h2>
                                </div>
                            </div>
                            <div class="recent-items-inn">
                                <table class="table table-inner table-vmiddle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th style="width: 60px">Sentiment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `title`,`id`, `category`, date(`timestamp`) as `date`, `sentiment`, `summary` FROM `news` WHERE `training_set` ='1' ORDER BY `id` DESC LIMIT 7");

                                        $count=array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));

                                        if($count > 0) { 

                                            while ($row = mysqli_fetch_object($query)) {

                                            $description = $manipulation->limit_text(strip_tags($row->summary),5);

                                            $title = $manipulation->limit_text(strip_tags($row->title),6);
                                                
                                            $sentiment_row = $get_content_article->get_sentiment_name($row->sentiment);
                                                
                                            ?>
                                        
                                            <tr>
                                                <td class="f-500 c-cyan"><a href="<?=$setting["admin_url"]?>/?type=editnews&id=<?=$row->id?>"><?=$row->id?></a></td>
                                                <td><?=$title?></td>
                                                <td class="f-500 c-cyan"><?=$sentiment_row['sentiment']?></td>
                                            </tr>


                                            <?php
                                            }
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                    <div class="realtime-wrap notika-shadow sm-res-mg-t-30">
                        <div class="realtime-ctn">
                            <div class="realtime-title">
                                <h2>Realtime Visitors</h2>
                            </div>
                        </div>
                        <div class="realtime-visitor-ctn">
                            <div class="realtime-vst-sg">
                                <h4><span class="counter"><?=$main_count_map_24_hours?></span></h4>
                                <p>Visitors last 24h</p>
                            </div>
                            <div class="realtime-vst-sg">
                                <h4><span class="counter"><?=$main_count_map_30_miutes?></span></h4>
                                <p>Visitors last 30m</p>
                            </div>
                        </div>
                        <div class="realtime-map">
                            <div class="vectorjsmarp" id="world-map"></div>
                        </div>
                        
                        <?php
                                            
                        $map_query = mysqli_query($setting["Lid"],'SELECT SQL_CALC_FOUND_ROWS * FROM `user_geo_location` ORDER BY `timestamp` DESC LIMIT 0, 2');
    
                        $parser_a = new Parser_a();

                        while($row_maps = mysqli_fetch_object($map_query)) { 
                                
                            $result = $parser_a->parse($row_maps->user_agent);

                            //print_r($result);
                            
                            ?>

                            <div class="realtime-country-ctn ">
                                <h5><?=date( "F	j, H:i:s", strtotime($row_maps->timestamp))?> (<?=$manipulation->time_elapsed_string($row_maps->timestamp)?>)</h5>
                                <div class="realtime-ctn-bw">
                                    <div class="realtime-ctn-st">
                                        <span><img src="img/country/1.png" alt=""></span>
                                       <span><?=$row_maps->country?></span>
                                    </div>
                                    <div class="realtime-bw">
                                        <span><?=$result->ua->family?></span>
                                    </div>
                                    <div class="realtime-bw">
                                        <span><?=$result->os->family?></span>
                                    </div>
                                </div>
                            </div><?php
                        
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- End Email Statistic area-->
    <?php 
        
    require_once("include/main_footer.php");

	require_once("include/footer.php");

}
else {

	require_once('../common/pages/404.php');

}

?>

