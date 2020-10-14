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

                        <?php 
                        
                        for($i=0; $i <= 2; $i++ ) {
                            
                            $side_main_query = $get_content_article->get_popular_user_preload(8);
                            
                            if(count($side_main_query) > 0) {
                        
                                ?><!-- Top Stories Slider Item -->
                                <div class="owl-item">

                                    <?php

                                    foreach($side_main_query as $key => $side_main_row) {

                                        $tags = unserialize(json_decode($side_main_row["tags"]));

                                        $tags = array_values(array_filter($tags));

                                        $days = date("j M",strtotime($side_main_row["timestamp"]));

                                        $description = $manipulation->limit_text(strip_tags($side_main_row["summary"]),30);

                                        $title = $manipulation->limit_text(strip_tags($side_main_row["title"]),8);

                                        $id = $side_main_row["id"];

                                        $image_main_url = $setting['main_url'].'/main/'.$side_main_row["thumb_url"]; 

                                        $author_row = $get_content_article->get_author_name($side_main_row['author']);

                                        $author = $manipulation->limit_text(strip_tags($author_row['name']),3);

                                        ?>


                                            <!-- Sidebar Post -->
                                            <div class="side_post">
                                                <a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>">
                                                    <div class="d-flex flex-row align-items-xl-center align-items-start justify-content-start">
                                                        <div class="side_post_image"><div><img src="<?=$image_main_url?>" alt=""></div></div>
                                                        <div class="side_post_content">
                                                            <div class="side_post_title"><?=$title?></div>
                                                            <small class="post_meta"><?=$author?><span><?=$days?></span></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div><?php

                                    }

                                    ?>


                            </div><?php
                                
                            }

                        }

                        ?>

                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
