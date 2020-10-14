<?php

$title="Cragglist";

$description="Don't compromise with quality, follow the latest and greatest market developments with our comprehensive real-time news feed.";

$keywords="cragglist, news, script, search, engine, realtime news, free";

$page_content = array(0);

require_once('include/header.php');
    
?>


<div class="super_container">


<?php

require_once('include/main_header.php');

?>

    <div class="home">
        
        <!-- Home Slider -->

        <div class="home_slider_container">
            <div class="owl-carousel owl-theme home_slider">
                
            <?php
                
            $limit = 3;
            
            $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `id` NOT IN (".implode(",", $page_content).")  AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,".$limit);

            $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

            if($total_count > 0) {
                
                $count_main_item = 0;
                
                $next_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `id` NOT IN (".implode(",", $page_content).")  AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,".$limit);
                
                while($item = mysqli_fetch_object($next_query)) {
                    
                    $item_array[] = $item->id;
                    
                    $page_content[] = $item->id;
                                            
                }
                            
                while($row = mysqli_fetch_object($main_query)) {

                    $days = date("F j, Y",strtotime($row->timestamp));

                    //$row->source_url;

                    $image_main_url = $setting['main_url'].'/main/'.$row->thumb_large_url;

                    $tags = unserialize(json_decode($row->tags)); ?>
                        
                
                        <!-- Slider Item -->
                        <div class="owl-item">
                            <div class="home_slider_background" style="background-image:url(<?=$image_main_url?>)"></div>
                            <div class="home_slider_content_container">
                                <div class="container">
                                    <div class="row">
                                        <div class="col">
                                            <div class="home_slider_content">
                                                <div class="home_slider_item_category trans_200"><a href="category.html" class="trans_200"><?=$row->category?></a></div>
                                                <div class="home_slider_item_title">
                                                    <a href="<?=$setting['main_url']?>/?action=view&id=<?=$row->id?>"><?=$row->title?></a>
                                                </div>
                                                <div class="home_slider_item_link">
                                                    <a href="<?=$setting['main_url']?>/?action=view&id=<?=$row->id?>" class="trans_200">Continue Reading
                                                        <svg version="1.1" id="link_arrow_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                             width="19px" height="13px" viewBox="0 0 19 13" enable-background="new 0 0 19 13" xml:space="preserve">
                                                            <polygon fill="#FFFFFF" points="12.475,0 11.061,0 17.081,6.021 0,6.021 0,7.021 17.038,7.021 11.06,13 12.474,13 18.974,6.5 "/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Similar Posts -->
                            <div class="similar_posts_container">
                                <div class="container">
                                    <div class="row d-flex flex-row align-items-end">

                                        <?php

                                           $similar_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`, `title`, `timestamp`, MATCH(`tags`) AGAINST('".implode(" ", $tags)."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`tags`) AGAINST('".implode(" ", $tags)."' IN NATURAL LANGUAGE MODE) AND `id` NOT IN (".implode(",", $page_content).")GROUP BY `id` HAVING Relevance > 0  ORDER BY `relevance` DESC LIMIT 0, 3");


                                           while($similar_row = mysqli_fetch_object($similar_query)) {

                                               $main_similar_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$similar_row->id."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");

                                               $total_similar_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                               if($total_similar_count > 0) {

                                                    $row_similar_content = mysqli_fetch_object($main_similar_content);

                                                    $tag_similar = unserialize(json_decode($row_similar_content->tags));

                                                    $tags_similar = array_values(array_filter($tags_similar));

                                                    $days_similar = date("F j, Y",strtotime($row_similar_content->timestamp));
                                                   
                                                    $title_similar  = limit_text(strip_tags($row_similar_content->title),8);

                                                    $image_similar_main_url = $setting['main_url'].'/main/'.$row_similar_content->thumb_large_url; ?>


                                                       <!-- Similar Post -->
                                                       <div class="col-lg-3 col-md-6 similar_post_col">
                                                           <div class="similar_post trans_200">
                                                               <a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_similar_content->id?>"><?=$title_similar?></a>
                                                           </div>
                                                       </div><?php

                                                    $page_content[] = $row_similar_content->id;

                                               }

                                           }

                                           ?>
                                    </div>
                                </div>
                                
                                <?php
                    
                                if(($count_main_item) == ($limit-1)) {
                                    
                                    $count_main_item = 0;
                                    
                                }
                    
                                $main_next_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `publish` = '1' AND `id` = '".$item_array[$count_main_item+1]."' ORDER BY `timestamp` DESC LIMIT 0, 1");
                            
                                $row_next = mysqli_fetch_object($main_next_query);
                    
                                ?>

                                <div class="home_slider_next_container">
                                    <div class="home_slider_next" style="background-image:url(images/home_slider_next.jpg)">
                                        <div class="home_slider_next_background trans_400"></div>
                                        <div class="home_slider_next_content trans_400">
                                            <div class="home_slider_next_title">next</div>
                                            <div class="home_slider_next_link"><?=$row_next->title?></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><?php
                    
                        $count_main_item++;
                                                    
                    }
                                        
                }
                                        
                ?>

            </div>

            <div class="custom_nav_container home_slider_nav_container">
                <div class="custom_prev custom_prev_home_slider">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         width="7px" height="12px" viewBox="0 0 7 12" enable-background="new 0 0 7 12" xml:space="preserve">
                        <polyline fill="#FFFFFF" points="0,5.61 5.609,0 7,0 7,1.438 2.438,6 7,10.563 7,12 5.609,12 -0.002,6.39 "/>
                    </svg>
                </div>
                <ul id="custom_dots" class="custom_dots custom_dots_home_slider">
                    <li class="custom_dot custom_dot_home_slider active"><span></span></li>
                    <li class="custom_dot custom_dot_home_slider"><span></span></li>
                    <li class="custom_dot custom_dot_home_slider"><span></span></li>
                </ul>
                <div class="custom_next custom_next_home_slider">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         width="7px" height="12px" viewBox="0 0 7 12" enable-background="new 0 0 7 12" xml:space="preserve">
                        <polyline fill="#FFFFFF" points="6.998,6.39 1.389,12 -0.002,12 -0.002,10.562 4.561,6 -0.002,1.438 -0.002,0 1.389,0 7,5.61 "/>
                    </svg>
                </div>
            </div>

        </div>
    </div>
    
    
    <?php
              
    //$duration = "100 HOUR";
    $duration = "100 DAY";

    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SUM(`count`) AS `total_sum`,`article`  FROM `clicks` WHERE `timestamp` > DATE_SUB(CURDATE(), INTERVAL ".$duration.") AND `type` = 'article' AND `article` NOT IN (".implode(",", $page_content).") GROUP BY `article` ORDER BY `total_sum` DESC LIMIT 0, 8");
                                        
    $count_array = 0;

    while($row = mysqli_fetch_object($main_query)) {

        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `id` =  '".$row->article."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
                
        if($total_count > 0) {
            
            $row_content = mysqli_fetch_object($main_content);

            //$tag_array[] = $tags = unserialize(json_decode($row_content->tags));
            
            foreach($tags as $key => $tag ) {
                $tag_array[] = $tag;
            }
            
            //print_r($tag_array[$count_array]);
                                        
            $count_array++;

                                            
        }
                                        
    }
    
    //$new_array_tags = array_intersect($tag_array['0'], $tag_array['1'], $tag_array['2'], $tag_array['3'], $tag_array['4'], $tag_array['5'], $tag_array['6'], $tag_array['7']);
     
    //print_r($tag_array);
    $pn = new proper_nouns();
    
    //echo implode(" ", $tag_array);
	echo "<pre>";
	$arr = $pn->get(implode(" ", $tag_array));
	print_r($arr);
    echo "</pre>";

    
    ?>


    <!-- Page Content -->

    <div class="page_content">
        <div class="container">
            <div class="row row-lg-eq-height">

                <!-- Main Content -->

                <div class="col-lg-9">
                    <div class="main_content">

                        <!-- Blog Section - Don't Miss -->

                        <div class="blog_section">
                            <div class="section_panel d-flex flex-row align-items-center justify-content-start">
                                <div class="section_title">Don't Miss</div>
                                <div class="section_tags ml-auto">
                                    <ul>
                                        <li class="active"><a href="category.html">all</a></li>
                                        <li><a href="category.html">style hunter</a></li>
                                        <li><a href="category.html">vogue</a></li>
                                        <li><a href="category.html">health & fitness</a></li>
                                        <li><a href="category.html">travel</a></li>
                                    </ul>
                                </div>
                                <div class="section_panel_more">
                                    <ul>
                                        <li>more
                                            <ul>
                                                <li><a href="category.html">new look 2018</a></li>
                                                <li><a href="category.html">street fashion</a></li>
                                                <li><a href="category.html">business</a></li>
                                                <li><a href="category.html">recipes</a></li>
                                                <li><a href="category.html">sport</a></li>
                                                <li><a href="category.html">celebrities</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="section_content">
                                <div class="grid clearfix">

                                    <?php


                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SUM(`count`) AS `total_sum`,`article`  FROM `clicks` WHERE `timestamp` > DATE_SUB(CURDATE(), INTERVAL ".$duration.") AND `type` = 'article' AND `article` NOT IN (".implode(",", $page_content).") GROUP BY `article` ORDER BY `total_sum` DESC LIMIT 0, 1");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->article."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            
                                            $tags_first = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $description = limit_text(strip_tags($row_content->summary),30);
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url; ?>

                                                <!-- Largest Card With Image -->
                                                <div class="card card_largest_with_image grid-item">
                                                    <img class="card-img-top" src="<?=$image_main_url?>" alt="https://unsplash.com/@cjtagupa">
                                                    <div class="card-body">
                                                        <div class="card-title"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                        <p class="card-text"><?=$description?></p>
                                                        <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>">Katy Liu</a><span> <?=$days?></span></small>
                                                    </div>
                                                </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                        
                                    ?>
                                    
                                    <?php

                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SUM(`count`) AS `total_sum`,`article`  FROM `clicks` WHERE `timestamp` > DATE_SUB(CURDATE(), INTERVAL ".$duration.") AND `type` = 'article' AND `article` NOT IN (".implode(",", $page_content).") GROUP BY `article` ORDER BY `total_sum` DESC LIMIT 0, 1");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->article."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_url; ?>

                                                <!-- Small Card NO Image -->
                                                
                                                <div class="card card_default card_small_no_image grid-item" style="position: absolute; left: 586px; top: 0px;">
                                                    <div class="card-body">
                                                        <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                        <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>">Katy Liu</a><span><?=$days?></span></small>
                                                    </div>
                                                </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                        
                                    ?>

                                                
                                    <?php

                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SUM(`count`) AS `total_sum`,`article`  FROM `clicks` WHERE `timestamp` > DATE_SUB(CURDATE(), INTERVAL ".$duration.") AND `type` = 'article' AND `article` NOT IN (".implode(",", $page_content).") GROUP BY `article` ORDER BY `total_sum` DESC LIMIT 0, 1");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->article."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);
                                            
                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_url; ?>

                                                <!-- Small Card With Background -->
                                                <div class="card card_default card_small_with_background grid-item">
                                                    <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                    <div class="card-body">
                                                        <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                        <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>">Katy Liu</a><span><?=$days?></span></small>
                                                    </div>
                                                </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                        
                                    ?>
                                    
                                    <?php

                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SUM(`count`) AS `total_sum`,`article`  FROM `clicks` WHERE `timestamp` > DATE_SUB(CURDATE(), INTERVAL ".$duration.") AND `type` = 'article' AND `article` NOT IN (".implode(",", $page_content).") GROUP BY `article` ORDER BY `total_sum` DESC LIMIT 0, 2");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->article."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_url; ?>
                                    
                                    
                                            <!-- Small Card With Image -->

                                            <div class="card card_small_with_image grid-item" style="position: absolute; left: 0px; top: 340px;">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="https://unsplash.com/@jakobowens1">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>">Katy Liu</a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                        
                                    ?>
                                    

                                    <?php

                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SUM(`count`) AS `total_sum`,`article`  FROM `clicks` WHERE `timestamp` > DATE_SUB(CURDATE(), INTERVAL ".$duration.") AND `type` = 'article' AND `article` NOT IN (".implode(",", $page_content).") GROUP BY `article` ORDER BY `total_sum` DESC LIMIT 0, 3");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->article."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url; ?>

                                                <!-- Default Card No Image -->

                                                <div class="card card_default card_default_no_image grid-item">
                                                    <div class="card-body">
                                                        <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                    </div>
                                                </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                        
                                    ?>

                                </div>
                            </div>
                        </div>

                        <!-- Blog Section - What's Trending -->

                        <div class="blog_section">
                            <div class="section_panel d-flex flex-row align-items-center justify-content-start">
                                <div class="section_title">What's Trending</div>
                                <div class="section_tags ml-auto">
                                    <ul>
                                        <li class="active"><a href="category.html">all</a></li>
                                        <li><a href="category.html">style hunter</a></li>
                                        <li><a href="category.html">vogue</a></li>
                                        <li><a href="category.html">health & fitness</a></li>
                                        <li><a href="category.html">travel</a></li>
                                    </ul>
                                </div>
                                <div class="section_panel_more">
                                    <ul>
                                        <li>more
                                            <ul>
                                                <li><a href="category.html">new look 2018</a></li>
                                                <li><a href="category.html">street fashion</a></li>
                                                <li><a href="category.html">business</a></li>
                                                <li><a href="category.html">recipes</a></li>
                                                <li><a href="category.html">sport</a></li>
                                                <li><a href="category.html">celebrities</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="section_content">
                                <div class="grid clearfix">
                                    
                                     <?php
                                    
                                    //$duration = "100 HOUR";
                                    $duration = "200 DAY";


                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`, `title`, `timestamp`, MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) AND `id` NOT IN (".implode(",", $page_content).") GROUP BY `id` HAVING Relevance > 0  ORDER BY `relevance` DESC LIMIT 0, 1");
                                    
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->id."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $description = limit_text(strip_tags($row_content->summary),30);
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url; ?>

                                               
                                            <!-- Large Card With Background -->
                                            <div class="card card_large_with_background grid-item">
                                                <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                <div class="card-body">
                                                    <div class="card-title"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>">Katy Liu</a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                    ?>
                                        
                                    
                                    <?php
                                   
                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`, `title`, `timestamp`, MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) AND `id` NOT IN (".implode(",", $page_content).") GROUP BY `id` HAVING Relevance > 0  ORDER BY `relevance` DESC LIMIT 0, 1");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->id."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $description = limit_text(strip_tags($row_content->summary),30);
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url; ?>

                                            <!-- Large Card With Image -->
                                            <div class="card grid-item card_large_with_image">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="">
                                                <div class="card-body">
                                                    <div class="card-title"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                    <p class="card-text"><?=$description?></p>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>">Katy Liu</a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                    ?>
                                    
                                    <?php

                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`, `title`, `timestamp`, MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) AND `id` NOT IN (".implode(",", $page_content).") GROUP BY `id` HAVING Relevance > 0  ORDER BY `relevance` DESC LIMIT 0, 1");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->id."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $description = limit_text(strip_tags($row_content->summary),30);
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url; ?>
                                    
                                            
                                            <!-- Default Card With Image -->
                                            <div class="card card_small_with_image grid-item">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>">Katy Liu</a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                    ?>
                                    
                                    <?php

                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`, `title`, `timestamp`, MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) AND `id` NOT IN (".implode(",", $page_content).") GROUP BY `id` HAVING Relevance > 0  ORDER BY `relevance` DESC LIMIT 0, 1");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->id."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $description = limit_text(strip_tags($row_content->summary),30);
                                           
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url; ?>
                                    
                                            <!-- Default Card With Background -->

                                            <div class="card card_default card_default_with_background grid-item">
                                                <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                </div>
                                            </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                    ?>
                                    
                                    
                                    <?php

                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`, `title`, `timestamp`, MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) AND `id` NOT IN (".implode(",", $page_content).") GROUP BY `id` HAVING Relevance > 0  ORDER BY `relevance` DESC LIMIT 0, 1");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->id."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $description = limit_text(strip_tags($row_content->summary),30);
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url; ?>

                                            <!-- Default Card No Image -->
                                            <div class="card card_default card_default_no_image grid-item">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                </div>
                                            </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                    ?>
                                    
                                    <?php

                                    $main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`, `title`, `timestamp`, MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`tags`) AGAINST('".implode(" ", $tags_first)."' IN NATURAL LANGUAGE MODE) AND `id` NOT IN (".implode(",", $page_content).") GROUP BY `id` HAVING Relevance > 0  ORDER BY `relevance` DESC LIMIT 0, 1");
                                        
                                    while($row = mysqli_fetch_object($main_query)) {

                                        $main_content = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS  * FROM `news` WHERE `id` =  '".$row->id."' AND `id` NOT IN (".implode(",", $page_content).") AND `publish` = '1' ORDER BY `timestamp` DESC LIMIT 0,1");
                                        
                                        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

                                        if($total_count > 0) {
                                            
                                            $row_content = mysqli_fetch_object($main_content);

                                            $tags = unserialize(json_decode($row_content->tags));
                                            
                                            $tags = array_values(array_filter($tags));
                                            
                                            $days = date("F j, Y",strtotime($row_content->timestamp));
                                            
                                            $description = limit_text(strip_tags($row_content->summary),30);
                                            
                                            $title = limit_text(strip_tags($row_content->title),15);

                                            $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url; ?>
                                            
                                            <!-- Default Card With Background -->

                                            <div class="card card_default card_default_with_background grid-item">
                                                <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_content->id?>"><?=$title?></a></div>
                                                </div>
                                            </div><?php
                                                    
                                            $page_content[] = $row_content->id;
                                            
                                        }
                                        
                                    }
                                    ?>


                                </div>
                                
                            </div>
                        </div>

                        <!-- Blog Section - Videos -->

                        <div class="blog_section">
                            <div class="section_panel d-flex flex-row align-items-center justify-content-start">
                                <div class="section_title">Most Popular Videos</div>
                            </div>
                            <div class="section_content">
                                <div class="row">
                                    <div class="col">
                                        <div class="videos">
                                            <div class="player_container">
                                                <div id="P1" class="player"
                                                     data-property="{videoURL:'2ScS5kwm7nI',containment:'self',startAt:0,mute:false,autoPlay:false,loop:false,opacity:1}">
                                                </div>
                                            </div>
                                            <div class="playlist">
                                                <div class="playlist_background"></div>

                                                <!-- Video -->
                                                <div class="video_container video_command active" onclick="jQuery('#P1').YTPChangeVideo({videoURL: '2ScS5kwm7nI', mute:false, addRaster:true})">
                                                    <div class="video d-flex flex-row align-items-center justify-content-start">
                                                        <div class="video_image"><div><img src="images/video_1.jpg" alt=""></div><img class="play_img" src="images/play.png" alt=""></div>
                                                        <div class="video_content">
                                                            <div class="video_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <div class="video_info"><span>1.2M views</span><span>Sep 29</span></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Video -->
                                                <div class="video_container video_command" onclick="jQuery('#P1').YTPChangeVideo({videoURL: 'BzMLA8YIgG0', mute:false, addRaster:true})">
                                                    <div class="video d-flex flex-row align-items-center justify-content-start">
                                                        <div class="video_image"><div><img src="images/video_2.jpg" alt=""></div><img class="play_img" src="images/play.png" alt=""></div>
                                                        <div class="video_content">
                                                            <div class="video_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <div class="video_info"><span>1.2M views</span><span>Sep 29</span></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Video -->
                                                <div class="video_container video_command" onclick="jQuery('#P1').YTPChangeVideo({videoURL: 'bpbcSdqvtUQ', mute:false, addRaster:true})">
                                                    <div class="video d-flex flex-row align-items-center justify-content-start">
                                                        <div class="video_image"><div><img src="images/video_3.jpg" alt=""></div><img class="play_img" src="images/play.png" alt=""></div>
                                                        <div class="video_content">
                                                            <div class="video_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <div class="video_info"><span>1.2M views</span><span>Sep 29</span></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Video -->
                                                <div class="video_container video_command" onclick="jQuery('#P1').YTPChangeVideo({videoURL: 'UjYemgbhJF0', mute:false, addRaster:true})">
                                                    <div class="video d-flex flex-row align-items-center justify-content-start">
                                                        <div class="video_image"><div><img src="images/video_4.jpg" alt=""></div><img class="play_img" src="images/play.png" alt=""></div>
                                                        <div class="video_content">
                                                            <div class="video_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <div class="video_info"><span>1.2M views</span><span>Sep 29</span></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Blog Section - Latest -->

                        <div class="blog_section">
                            <div class="section_panel d-flex flex-row align-items-center justify-content-start">
                                <div class="section_title">Latest Articles</div>
                            </div>
                            <div class="section_content">
                                <div class="grid clearfix">
                                    
                                    <!-- Small Card With Image -->
                                    <div class="card card_small_with_image grid-item">
                                        <img class="card-img-top" src="images/post_10.jpg" alt="">
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Small Card Without Image -->
                                    <div class="card card_default card_small_no_image grid-item">
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Small Card With Image -->
                                    <div class="card card_small_with_image grid-item">
                                        <img class="card-img-top" src="images/post_15.jpg" alt="">
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Small Card With Image -->
                                    <div class="card card_small_with_image grid-item">
                                        <img class="card-img-top" src="images/post_13.jpg" alt="https://unsplash.com/@jakobowens1">
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Small Card With Background -->
                                    <div class="card card_default card_small_with_background grid-item">
                                        <div class="card_background" style="background-image:url(images/post_11.jpg)"></div>
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Small Card With Background -->
                                    <div class="card card_default card_small_with_background grid-item">
                                        <div class="card_background" style="background-image:url(images/post_16.jpg)"></div>
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Small Card With Image -->
                                    <div class="card card_small_with_image grid-item">
                                        <img class="card-img-top" src="images/post_14.jpg" alt="">
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Small Card Without Image -->
                                    <div class="card card_default card_small_no_image grid-item">
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Small Card Without Image -->
                                    <div class="card card_default card_small_no_image grid-item">
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most Complex Concepts in Physics?</a></div>
                                            <small class="post_meta"><a href="#">Katy Liu</a><span>Sep 29, 2017 at 9:48 am</span></small>
                                        </div>
                                    </div>

                                    <!-- Default Card With Background -->
                                    <div class="card card_default card_default_with_background grid-item">
                                        <div class="card_background" style="background-image:url(images/post_12.jpg)"></div>
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most</a></div>
                                        </div>
                                    </div>

                                    <!-- Default Card With Background -->
                                    <div class="card card_default card_default_with_background grid-item">
                                        <div class="card_background" style="background-image:url(images/post_6.jpg)"></div>
                                        <div class="card-body">
                                            <div class="card-title card-title-small"><a href="post.html">How Did van Gogh’s Turbulent Mind Depict One of the Most</a></div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                    </div>
                    <div class="load_more">
                        <div id="load_more" class="load_more_button text-center trans_200">Load More</div>
                    </div>
                </div>

                <!-- Sidebar -->

                <div class="col-lg-3">
                    <div class="sidebar">
                        <div class="sidebar_background"></div>

                        <!-- Top Stories -->

                        <div class="sidebar_section">
                            <div class="sidebar_title_container">
                                <div class="sidebar_title">Top Stories</div>
                                <div class="sidebar_slider_nav">
                                    <div class="custom_nav_container sidebar_slider_nav_container">
                                        <div class="custom_prev custom_prev_top">
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 width="7px" height="12px" viewBox="0 0 7 12" enable-background="new 0 0 7 12" xml:space="preserve">
                                                <polyline fill="#bebebe" points="0,5.61 5.609,0 7,0 7,1.438 2.438,6 7,10.563 7,12 5.609,12 -0.002,6.39 "/>
                                            </svg>
                                        </div>
                                        <ul id="custom_dots" class="custom_dots custom_dots_top">
                                            <li class="custom_dot custom_dot_top active"><span></span></li>
                                            <li class="custom_dot custom_dot_top"><span></span></li>
                                            <li class="custom_dot custom_dot_top"><span></span></li>
                                        </ul>
                                        <div class="custom_next custom_next_top">
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 width="7px" height="12px" viewBox="0 0 7 12" enable-background="new 0 0 7 12" xml:space="preserve">
                                                <polyline fill="#bebebe" points="6.998,6.39 1.389,12 -0.002,12 -0.002,10.562 4.561,6 -0.002,1.438 -0.002,0 1.389,0 7,5.61 "/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sidebar_section_content">

                                <!-- Top Stories Slider -->
                                <div class="sidebar_slider_container">
                                    <div class="owl-carousel owl-theme sidebar_slider_top">

                                        <!-- Top Stories Slider Item -->
                                        <div class="owl-item">

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_1.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_2.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_3.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_4.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                        <!-- Top Stories Slider Item -->
                                        <div class="owl-item">

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_1.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_2.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_3.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_4.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                        <!-- Top Stories Slider Item -->
                                        <div class="owl-item">

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_1.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_2.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_3.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/top_4.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advertising -->

                        <div class="sidebar_section">
                            <div class="advertising">
                                <div class="advertising_background" style="background-image:url(images/post_17.jpg)"></div>
                                <div class="advertising_content d-flex flex-column align-items-start justify-content-end">
                                    <div class="advertising_perc">-15%</div>
                                    <div class="advertising_link"><a href="#">How Did van Gogh’s Turbulent Mind</a></div>
                                </div>
                            </div>
                        </div>

                        <!-- Newest Videos -->

                        <div class="sidebar_section newest_videos">
                            <div class="sidebar_title_container">
                                <div class="sidebar_title">Newest Videos</div>
                                <div class="sidebar_slider_nav">
                                    <div class="custom_nav_container sidebar_slider_nav_container">
                                        <div class="custom_prev custom_prev_vid">
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 width="7px" height="12px" viewBox="0 0 7 12" enable-background="new 0 0 7 12" xml:space="preserve">
                                                <polyline fill="#bebebe" points="0,5.61 5.609,0 7,0 7,1.438 2.438,6 7,10.563 7,12 5.609,12 -0.002,6.39 "/>
                                            </svg>
                                        </div>
                                        <ul id="custom_dots" class="custom_dots custom_dots_vid">
                                            <li class="custom_dot custom_dot_vid active"><span></span></li>
                                            <li class="custom_dot custom_dot_vid"><span></span></li>
                                            <li class="custom_dot custom_dot_vid"><span></span></li>
                                        </ul>
                                        <div class="custom_next custom_next_vid">
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 width="7px" height="12px" viewBox="0 0 7 12" enable-background="new 0 0 7 12" xml:space="preserve">
                                                <polyline fill="#bebebe" points="6.998,6.39 1.389,12 -0.002,12 -0.002,10.562 4.561,6 -0.002,1.438 -0.002,0 1.389,0 7,5.61 "/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sidebar_section_content">

                                <!-- Sidebar Slider -->
                                <div class="sidebar_slider_container">
                                    <div class="owl-carousel owl-theme sidebar_slider_vid">

                                        <!-- Newest Videos Slider Item -->
                                        <div class="owl-item">

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_1.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_2.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_3.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_4.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                        <!-- Newest Videos Slider Item -->
                                        <div class="owl-item">

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_1.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_2.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_3.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_4.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                        <!-- Newest Videos Slider Item -->
                                        <div class="owl-item">

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_1.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_2.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_3.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Newest Videos Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="images/vid_4.jpg" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advertising 2 -->

                        <div class="sidebar_section">
                            <div class="advertising_2">
                                <div class="advertising_background" style="background-image:url(images/post_18.jpg)"></div>
                                <div class="advertising_2_content d-flex flex-column align-items-center justify-content-center">
                                    <div class="advertising_2_link"><a href="#">Turbulent <span>Mind</span></a></div>
                                </div>
                            </div>
                        </div>

                        <!-- Future Events -->

                        <div class="sidebar_section future_events">
                            <div class="sidebar_title_container">
                                <div class="sidebar_title">Future Events</div>
                                <div class="sidebar_slider_nav">
                                    <div class="custom_nav_container sidebar_slider_nav_container">
                                        <div class="custom_prev custom_prev_events">
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 width="7px" height="12px" viewBox="0 0 7 12" enable-background="new 0 0 7 12" xml:space="preserve">
                                                <polyline fill="#bebebe" points="0,5.61 5.609,0 7,0 7,1.438 2.438,6 7,10.563 7,12 5.609,12 -0.002,6.39 "/>
                                            </svg>
                                        </div>
                                        <ul id="custom_dots" class="custom_dots custom_dots_events">
                                            <li class="custom_dot custom_dot_events active"><span></span></li>
                                            <li class="custom_dot custom_dot_events"><span></span></li>
                                            <li class="custom_dot custom_dot_events"><span></span></li>
                                        </ul>
                                        <div class="custom_next custom_next_events">
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 width="7px" height="12px" viewBox="0 0 7 12" enable-background="new 0 0 7 12" xml:space="preserve">
                                                <polyline fill="#bebebe" points="6.998,6.39 1.389,12 -0.002,12 -0.002,10.562 4.561,6 -0.002,1.438 -0.002,0 1.389,0 7,5.61 "/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sidebar_section_content">

                                <!-- Sidebar Slider -->
                                <div class="sidebar_slider_container">
                                    <div class="owl-carousel owl-theme sidebar_slider_events">

                                        <!-- Future Events Slider Item -->
                                        <div class="owl-item">

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">13</div>
                                                            <div class="event_month">apr</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">27</div>
                                                            <div class="event_month">apr</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">02</div>
                                                            <div class="event_month">may</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">09</div>
                                                            <div class="event_month">may</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                        <!-- Future Events Slider Item -->
                                        <div class="owl-item">

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">13</div>
                                                            <div class="event_month">apr</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">27</div>
                                                            <div class="event_month">apr</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">02</div>
                                                            <div class="event_month">may</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">09</div>
                                                            <div class="event_month">may</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                        <!-- Future Events Slider Item -->
                                        <div class="owl-item">

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">13</div>
                                                            <div class="event_month">apr</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">27</div>
                                                            <div class="event_month">apr</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">02</div>
                                                            <div class="event_month">may</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Future Events Post -->
                                            <div class="side_post">
                                                <a href="post.html">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="event_date d-flex flex-column align-items-center justify-content-center">
                                                            <div class="event_day">09</div>
                                                            <div class="event_month">may</div>
                                                        </div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title">How Did van Gogh’s Turbulent Mind</div>
                                                            <small class="post_meta">Katy Liu<span>Sep 29</span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

	<?php require_once("../common/include/main_footer.php"); ?>

</div>

<?php require_once("../common/include/footer.php"); ?>

<script>
	(function(h,e,a,t,m,p) {
	m=e.createElement(a);m.async=!0;m.src=t;
	p=e.getElementsByTagName(a)[0];p.parentNode.insertBefore(m,p);
	})(window,document,'script','https://u.heatmap.it/log.js');
</script>
