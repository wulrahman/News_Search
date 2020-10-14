<?php

$title="Cragglist | ".htmlspecialchars($q);
    
$description="Don't compromise with quality, follow the latest and greatest market developments with our comprehensive real-time news feed.";

$keywords="cragglist, news, script, search, engine, realtime news, free";

$page_content = array(0);
    
foreach(explode(" ",$q) as $p) {

    if(!$verifier->space($p)) {

        $match[] = '('.$p.'*)';

    }

}

$matchs = implode(' ', $match);
    
$type_array_limit = array();
                                    
$style_type = array(1=>2, 2=>2, 3=>4, 4=>4, 5=>2, 6=>4, 7=>4);

$array_total = array_sum($style_type);

$main_query_search = $get_content_article->get_content_search_fulltext($matchs, 1, $array_total);


if(count($main_query_search['results']) > 0) {

    require_once('include/header.php');

    ?>

    <div class="super_container">

        <!-- Header -->

        <?php require_once('include/main_header.php');?>


        <!-- Home -->

        <div class="home">
            <div class="home_background parallax-window" data-parallax="scroll" style="background-image:url(<?=$setting['main_url']?>/main/images/category.jpg);" data-image-src="<?=$setting['main_url']?>/main/images/category.jpg" data-speed="0.8"></div>
        </div>
        
        <!-- Page Content -->

        <div class="page_content">
            <div class="container">
                <div class="row row-lg-eq-height">

                    <!-- Main Content -->

                    <div class="col-lg-9">
                
                        <div class="main_content">

                            <!-- Category -->

                            <div class="category">

                                <?php

                                foreach($style_type as $key => $type) {

                                    for($i = 0; $i < $type; $i++) {
                                        $type_array_limit[] =$key;
                                    }

                                }

                                //print_r($main_query);

                                $tags_row = unserialize(json_decode($main_query_search['results']['0']["tags"]));

                                //arsort($tags_row);

                                $tags_row = array_keys(array_filter($tags_row));


                                ?>
                                <div class="section_panel d-flex flex-row align-items-center justify-content-start">
                                    <div class="section_title">What's Trending</div>

                                    <?php

                                    require('include/tag_nav.php');

                                    ?>

                                </div>

                                <div class="section_content">
                                    <input type="hidden" name="pagination" id="pagination" value="1">
                                    <div class="grid clearfix" id="post-list_items">

                                        <?php

                                        foreach($main_query_search['results'] as $key => $row) {

                                            $tags = unserialize(json_decode($row["tags"]));

                                            $tags = array_values(array_filter($tags));

                                            $days = date("F j, Y",strtotime($row["timestamp"]));

                                            $description = $manipulation->limit_text(strip_tags($row["summary"]),30);

                                            $title = $manipulation->limit_text(strip_tags($row["title"]),15);

                                            $id = $row["id"];

                                            $image_main_url = $setting['main_url'].'/main/'.$row["thumb_large_url"];
                                            
                                            $author_row = $get_content_article->get_author_name($row["author"]);

                                            $author = $manipulation->limit_text(strip_tags($author_row['name']),3);

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

                    <!-- Sidebar -->


                    <?php

                    require_once('include/sidebar.php');

                    ?>


                </div>
            </div>
        </div>

        <!-- Footer -->

        <?php require_once("include/main_footer.php"); ?>

    </div>

    <?php require_once("include/footer.php"); ?>

    <script type="text/javascript">
    var page_content = '<?=urlencode(implode(",", $page_content))?>';

    var limit = '11';query='<?=urlencode($q)?>'; var type = 'search';

    Scroll_content = new Scroll_content(site_url, page_content, limit, type, query);
        
    $(document).ready(function(){
        
        Scroll_content.windowOnScroll();

    });

    </script>


<?php 
}
else {

    $action = 'error';

    require_once('include/header.php');

    require_once('include/main_header.php');

    $error_code = "It's So Embarrassing";

    $error_message = "I can explain myself....We're really sorry but at the moment we have no search results for ".$q.".";

    ?>

    <div class="super_container">

        <!-- Home -->
        <?php require_once("../common/pages/404_require.php");?>

        <?php require_once("include/main_footer.php"); ?>

    </div>

    <?php require_once("include/footer.php"); 
}
?>

