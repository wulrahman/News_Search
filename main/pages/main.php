<?php

$title="Cragglist";

$description="Don't compromise with quality, follow the latest and greatest market developments with our comprehensive real-time news feed.";

$keywords="cragglist, news, script, search, engine, realtime news, free";

$page_content = array(0);

//print_r($setting['user']->model);

$setting['user']->model;

if($setting['user']->model !== null) {
    
    $main_query = $get_content_article->get_content_user_model(10, 1);
    
}
else {
    
    $main_query = $get_content_article->get_sort_content(10, 0);

}


$style = "";

if($main_query['0']["image_color_difference"] < 350) {
    
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

                //images/category.jpg
                
                $image_main_url = $setting['main_url'].'/main/'.$row["thumb_large_url"];

                $category_row = $get_content_article->get_category_name($row["category"]);
                
                $tags = array_keys(unserialize(json_decode($row["tags"])));

                $title = stripslashes(stripslashes($row["title"]));

                $title = $manipulation->limit_text(strip_tags($title),15);


                $color = "white";
                $text_color_1 = "#ffffff";
                $text_color_2 = "#ffffff";
                if($row["image_color_difference"] < 350) {
                    $text_color_1 = "#808080";
                    $text_color_2 = "#808080";
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
                                                <a href="<?=$setting['main_url']?>/?action=view&id=<?=$row["id"]?>" style="color:<?=$text_color_1?>!important;"><?=$title?></a>
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
                                                   <a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_similar_content['id']?>"><?=stripslashes(stripslashes($title_similar))?></a>
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
                                
                            $next_title = stripslashes(stripslashes($main_query[$count_main_item+1]["title"]));

                            $next_title = $manipulation->limit_text(strip_tags($next_title),15);
                                
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


</div>

<?php require_once("include/footer.php"); ?>
