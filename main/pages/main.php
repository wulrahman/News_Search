<?php

$title="Cragglist";

$description="Don't compromise with quality, follow the latest and greatest market developments with our comprehensive real-time news feed.";

$keywords="cragglist, news, script, search, engine, realtime news, free";

$page_content = array(0);

//print_r($setting['user']->model);

$setting['user']->model;

if($setting['user']->model !== null) {
    
    $main_query = $get_content_article->get_content_user_model(3);
    
}
else {
    
    $main_query = $get_content_article->get_sort_content(3);

}


$style = "";

$color_class = new colorTests();

$color_difference = $color_class->colorDiff("#ffffff", $main_query['0']["image_colour"]);

if($color_difference < 50) {
    
    $style = "background-color:rgba(0,0,0,0.75)";
}

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
                                
            $array_count = count($main_query);
                
            $count_main_item = 0;
                                                      
            foreach($main_query as $main_key => $row) {

                $days = date("F j, Y",strtotime($row['timestamp']));

                $image_main_url = $setting['main_url'].'/main/'.$row["thumb_large_url"];

                $category_row = $get_content_article->get_category_name($row["category"]);
                
                $tags = array_keys(unserialize(json_decode($row["tags"])));
                
                $color_difference = $color_class->colorDiff("#ffffff", $row["image_colour"]);                        
                $color = "white";
                $text_color_1 = "";
                $text_color_2 = "";
                if($color_difference < 50) {
                    $text_color_1 = "#000000";
                    $text_color_2 = "#000000";
                    $color = "black";

                }
                ?>
            
                    <!-- Slider Item -->
                    <div class="owl-item" data-color="<?=$color?>">
                        <div class="home_slider_background" style="background-image:url(<?=$image_main_url?>)"></div>
                        <div class="home_slider_content_container">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <div class="home_slider_content">
                                            <div class="home_slider_item_category trans_200"><a href="<?=$setting['main_url']?>/?action=post&category=<?=$category_row['name']?>" class="trans_200"><?=$category_row['name']?></a></div>
                                            <div class="home_slider_item_title">
                                                <a href="<?=$setting['main_url']?>/?action=view&id=<?=$row["id"]?>" style="color:<?=$text_color_1?>!important;"><?=$row["title"]?></a>
                                            </div>
                                            <div class="home_slider_item_link">
                                                <a href="<?=$setting['main_url']?>/?action=view&id=<?=$row["id"]?>" class="trans_200" style="color:<?=$text_color_2?>!important;">Continue Reading
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

                                    $similar_items = $get_content_article->get_relevent_content($row["id"], 3);
                                        
                                    //print_r($similar_items);
                                       
                                    foreach($similar_items as $key => $row_similar_content) {
                                       
                                        $tag_similar = unserialize(json_decode($row_similar_content['tags']));

                                        $tags_similar = array_values(array_filter($tags_similar));

                                        $days_similar = date("F j, Y",strtotime($row_similar_content['timestamp']));
                                       
                                        $title_similar  = $manipulation->limit_text(strip_tags($row_similar_content['title']),8);

                                        $image_similar_main_url = $setting['main_url'].'/main/'.$row_similar_content['thumb_large_url']; ?>


                                           <!-- Similar Post -->
                                           <div class="col-lg-3 col-md-6 similar_post_col">
                                               <div class="similar_post trans_200">
                                                   <a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_similar_content['id']?>"><?=$title_similar?></a>
                                               </div>
                                           </div><?php

                                               
                                    }

                                    ?>

                                </div>
                            </div>
                            
                            <?php
                                
                            //0 2 = 0, 1
                            //1 2 = 1, 2
                            //-1 2 = 0, 0
                            
                
                            if(($count_main_item) == $array_count-1) {
                                
                                $count_main_item = -1;
                                
                            }
                                
                            $next_title = $main_query[$count_main_item+1]["title"];
                                
                            $next_image_main_url = $setting['main_url'].'/main/'.$main_query[$count_main_item+1]['thumb_large_url']; ?>

                            <div class="home_slider_next_container">
                                <div class="home_slider_next" style="background-image:url(<?=$next_image_main_url?>)">
                                    <div class="home_slider_next_background trans_400"></div>
                                    <div class="home_slider_next_content trans_400">
                                        <div class="home_slider_next_title">next</div>
                                        <div class="home_slider_next_link"><?=$next_title?></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><?php
                
                    $count_main_item++;
                                                
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
                                
                                 <?php
                        
                                require('include/tag_nav.php');
                        
                                ?>
                                
                            </div>
                            <div class="section_content">
                                <div class="grid clearfix">

                                    <?php
                                    
                                    $style_type = array(1=>1, 2=>1, 3=>1, 4=>2, 5=>3);

                                    foreach($style_type as $key => $type) {

                                        for($i = 0; $i < $type; $i++) {
                                            $type_array_limit[] =$key;
                                        }

                                    }
                                    
                                    if($setting['user']->model !== null) {


                                        $main_query = $get_content_article->get_content_user_model(array_sum($style_type));
                                        
                                    }
                                    else {
                                        
                                        $main_query = $get_content_article->get_sort_content(array_sum($style_type));

                                    }
                                    
                                    foreach($main_query as $key => $row_content) {

                                        $tags = unserialize(json_decode($row_content["tags"]));
                                        
                                        $tags = array_values(array_filter($tags));
                                        
                                        $days = date("F j, Y",strtotime($row_content["timestamp"]));
                                        
                                        $description = $manipulation->limit_text(strip_tags($row_content["summary"]),30);
                                        
                                        $title = $manipulation->limit_text(strip_tags($row_content["title"]),15);
                                        
                                        $author_row = $get_content_article->get_author_name($row_content["author"]);

                                        $author = $manipulation->limit_text(strip_tags($author_row["name"]),4);

                                        $id = $row_content["id"];

                                        $image_main_url = $setting['main_url'].'/main/'.$row_content["thumb_large_url"];
                                        
                                        if($type_array_limit[$key] == 1) {  ?>

                                            <!-- Largest Card With Image -->
                                            <div class="card card_largest_with_image grid-item">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="https://unsplash.com/@cjtagupa">
                                                <div class="card-body">
                                                    <div class="card-title"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                    <p class="card-text"><?=$description?></p>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span> <?=$days?></span></small>
                                                </div>
                                            </div><?php
                                                
                                        }
                                        else if($type_array_limit[$key] == 2) { ?>
                                                    
                                           <!-- Small Card NO Image -->
                                                                                                   
                                           <div class="card card_default card_small_no_image grid-item" style="position: absolute; left: 586px; top: 0px;">
                                               <div class="card-body">
                                                   <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                   <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                               </div>
                                           </div><?php

                                        }
                                        else if($type_array_limit[$key] == 3) { ?>
                                           
                                           <!-- Small Card With Background -->
                                           <div class="card card_default card_small_with_background grid-item">
                                               <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                               <div class="card-body">
                                                   <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                   <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                               </div>
                                           </div><?php

                                        }
                                        else if($type_array_limit[$key] == 4) { ?>
                                           
                                           <!-- Small Card With Image -->
                                           <div class="card card_small_with_image grid-item" style="position: absolute; left: 0px; top: 340px;">
                                               <img class="card-img-top" src="<?=$image_main_url?>" alt="https://unsplash.com/@jakobowens1">
                                               <div class="card-body">
                                                   <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                   <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                               </div>
                                           </div><?php
                                               
                                        }
                                       else if($type_array_limit[$key] == 5) { ?>
                                          
                                             <!-- Default Card No Image -->
                                             <div class="card card_default card_default_no_image grid-item">
                                                 <div class="card-body">
                                                     <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                 </div>
                                             </div><?php

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
                                
                                <?php
                        
                                require('include/tag_nav.php');
                        
                                ?>
                                
                            </div>
                            <div class="section_content">
                                <div class="grid clearfix">
                                    
                                    <?php
                                                                 
                                    $type_array_limit = array();

                                    $style_type = array(1=>1, 2=>1, 3=>1, 4=>1, 5=>1, 6=>1);

                                    foreach($style_type as $key => $type) {

                                        for($i = 0; $i < $type; $i++) {
                                            $type_array_limit[] =$key;
                                        }

                                    }

                                    $row_content = $get_content_article->get_popular_user_preload(1);

                                    $similar_items = $get_content_article->get_relevent_content($row_content['0']["id"], array_sum($style_type));

                                                                                
                                    foreach($similar_items as $key => $row_similar_content) {
                                                                                
                                        $author_row = $get_content_article->get_author_name($row_similar_content["author"]);

                                        $author = $manipulation->limit_text(strip_tags($author_row["name"]),4);
                                        
                                        $days = date("F j, Y",strtotime($row_similar_content["timestamp"]));
                                        
                                        $description = $manipulation->limit_text(strip_tags($row_similar_content["summary"]),30);
                                        
                                        $title = $manipulation->limit_text(strip_tags($row_similar_content["title"]),15);
                                        
                                        $id = $row_similar_content["id"];

                                        $image_main_url = $setting['main_url'].'/main/'.$row_similar_content["thumb_large_url"];
                                        
                                        
                                        if($type_array_limit[$key] == 1) {  ?>

                                            <!-- Large Card With Background -->
                                            <div class="card card_large_with_background grid-item">
                                                <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                <div class="card-body">
                                                    <div class="card-title"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 2) {  ?>

                                            <!-- Large Card With Image -->
                                            <div class="card grid-item card_large_with_image">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="">
                                                <div class="card-body">
                                                    <div class="card-title"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                    <p class="card-text"><?=$description?></p>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 3) {  ?>

                                            
                                            <!-- Default Card With Image -->
                                            <div class="card card_small_with_image grid-item">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 4) {  ?>


                                            <div class="card card_default card_default_with_background grid-item">
                                                <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 5) {  ?>


                                            <!-- Default Card No Image -->
                                            <div class="card card_default card_default_no_image grid-item">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 6) {  ?>

                                            <!-- Default Card With Background -->

                                            <div class="card card_default card_default_with_background grid-item">
                                                <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                </div>
                                            </div><?php

                                        }
                                        
                                }
                                ?>
                                </div>
                                
                            </div>
                        </div>

    
                        <!-- Blog Section - Latest -->

                        <div class="blog_section">
                            <div class="section_panel d-flex flex-row align-items-center justify-content-start">
                                <div class="section_title">Latest Articles</div>
                            </div>
                            <div class="section_content">
                                <div class="grid clearfix" id="post-list_items">
                                    <input type="hidden" name="pagination" id="pagination" value="1">

                                    <?php
                                        
                                    $type_array_limit = array();
                                    
                                    $style_type = array(1=>1, 2=>1, 3=>2, 4=>2, 5=>1, 6=>2, 7=>2);
                                    
                                    $main_query = $get_content_article->get_sort_content(array_sum($style_type));
                                                                                     
                                    foreach($style_type as $key => $type) {
                                          
                                        for($i = 0; $i < $type; $i++) {
                                            $type_array_limit[] =$key;
                                        }
                                          
                                    }

                                                                        
                                    foreach($main_query as $key => $row) {
                                       
                                        $tags = unserialize(json_decode($row["tags"]));
                                        
                                        $author_row = $get_content_article->get_author_name($row["author"]);
                                        
                                        $author = $manipulation->limit_text(strip_tags($author_row["name"]),4);

                                        $tags = array_values(array_filter($tags));
                                        
                                        $days = date("F j, Y",strtotime($row["timestamp"]));
                                        
                                        $description = $manipulation->limit_text(strip_tags($row["summary"]),30);
                                        
                                        $title = $manipulation->limit_text(strip_tags($row["title"]),15);
                                        
                                        $id = $row["id"];

                                        $image_main_url = $setting['main_url'].'/main/'.$row["thumb_large_url"];
                                        
                                        if($type_array_limit[$key] == 1) { ?>

                                            <!-- Small Card With Image -->
                                            <div class="card card_small_with_image grid-item">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 2) { ?>

                                             <!-- Small Card Without Image -->
                                             <div class="card card_default card_small_no_image grid-item">
                                                 <div class="card-body">
                                                     <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                     <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                 </div>
                                             </div><?php

                                        }
                                        else if($type_array_limit[$key] == 3) { ?>
                                            
                                            <!-- Small Card With Image -->
                                            <div class="card card_small_with_image grid-item">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 4) { ?>

                                             <!-- Small Card With Background -->
                                             <div class="card card_default card_small_with_background grid-item">
                                                 <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                 <div class="card-body">
                                                     <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                     <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                 </div>
                                             </div><?php

                                        }
                                        else if($type_array_limit[$key] == 5) { ?>
                                            
                                            <!-- Small Card With Image -->
                                            <div class="card card_small_with_image grid-item">
                                                <img class="card-img-top" src="<?=$image_main_url?>" alt="">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 6) { ?>

                                            <!-- Small Card Without Image -->
                                            <div class="card card_default card_small_no_image grid-item">
                                                <div class="card-body">
                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                </div>
                                            </div><?php

                                        }
                                        else if($type_array_limit[$key] == 7) { ?>

                                             <!-- Small Card With Background -->
                                             <div class="card card_default card_small_with_background grid-item">
                                                 <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div>
                                                 <div class="card-body">
                                                     <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                     <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                 </div>
                                             </div><?php

                                        }
                                    }
                                                 
                                    ?>


                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="load_more">
                         <div class="ajax-loader text-center">
                            <img src="<?=$setting['main_url']?>/main/images/LoaderIcon.gif"> Loading more posts...
                        </div>
                        <div id="load_more" class="load_more_button text-center trans_200">Load More</div>
                    </div>
                </div>

                <?php

                require_once('include/sidebar.php');

                ?>

            </div>
        </div>
    </div>

	<?php require_once("include/main_footer.php"); ?>

</div>

<?php require_once("include/footer.php"); ?>


<script type="text/javascript">

var page_content = '<?=urlencode(implode(",", $page_content))?>';

var limit = '11';

Scroll_content = new Scroll_content(site_url, page_content, limit);
    //https://gomakethings.com/how-to-test-if-an-element-is-in-the-viewport-with-vanilla-javascript/#:~:text=If%20an%20element%20is%20in%20the%20viewport%2C%20it's%20position%20from,the%20height%20of%20the%20viewport.
    
$(document).ready(function(){
    
    Scroll_content.windowOnScroll();

});
</script>