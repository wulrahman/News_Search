<?php

$category = mysqli(urldecode($_GET['category']));

$main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `categorys`  WHERE `name` ='".$category."'  ORDER BY `cat_order` ASC LIMIT 0,1");
    
$total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

if( $total_count > 0) {
    
    $category_row = mysqli_fetch_object($main_query);
    
     
    if($user->login_status == 1) {

        mysqli_query($setting["Lid"], "INSERT INTO `clicks` (`type`, `timestamp`, `count`, `category`, `user`, `identifier`) VALUES ('category', CURRENT_TIMESTAMP, '1', '".$category_row->id."',  '".$user->id."', '".$user->cookie_id."')");
        
    }
    else {
        
        mysqli_query($setting["Lid"], "INSERT INTO `clicks` (`type`, `timestamp`, `count`, `category`, `identifier`) VALUES ('category', CURRENT_TIMESTAMP, '1', '".$category_row->id."', '".$user->cookie_id."')");
    
        
    }
    
    $title="Cragglist | ".$category_row->name;
    
}
else {
    
    $title="Cragglist | ALL";

}


$page_content = array(0);

$type_array_limit = array();

$style_type = array(1=>1, 2=>1, 3=>2, 4=>3, 5=>3, 6=>4);

foreach($style_type as $key => $type) {

    for($i = 0; $i < $type; $i++) {
        $type_array_limit[] =$key;
    }

}

if($total_count == 0) {

    $main_query_main = $get_content_article->get_sort_content(array_sum($style_type));

}
else {

    $main_query_main = $get_content_article->get_category_content($category_row->id, array_sum($style_type));

}


if(count($main_query_main) > 0) {
      
    $tags_row = unserialize(json_decode($main_query_main['0']["tags"]));

    $tags_row = array_keys(array_filter($tags_row));
    
    if(!$verifier->space($main_query_main['0']["summary"])) {
                                                  
        $paragraphs = preg_split("/(?<=[!?.])(?:$|\s+(?=\p{Lu}\p{Ll}*\b))/i",$main_query_main['0']["summary"]);
        
    }

    $description=$paragraphs[0];
    
    $keywords=implode(", ", $tags_row);
    
}


require_once('include/header.php');

?>

<div class="super_container">

    <!-- Header -->
    <?php

    require_once('include/main_header.php');

    ?>
    <!-- Home -->

    <div class="home">
        <div class="home_background parallax-window" data-parallax="scroll" data-image-src="<?=$setting['main_url']?>/main/images/category.jpg" data-speed="0.8"></div>
    </div>
    
    <!-- Page Content -->

    <div class="page_content">
        <div class="container">
            <div class="row row-lg-eq-height">

                <!-- Main Content -->

                <div class="col-lg-9">
                        
                    <?php

                    if(count($main_query_main) > 0) {
                        
                        ?>
                    
                        <div class="main_content">

                            <!-- Category -->
                            <div class="category">

                                <?php

                                foreach($style_type as $key => $type) {

                                    for($i = 0; $i < $type; $i++) {
                                        $type_array_limit[] =$key;
                                    }

                                }

                                shuffle($type_array_limit);

                                ?>

                                <div class="section_panel d-flex flex-row align-items-center justify-content-start">
                                    <div class="section_title">What's Trending</div>

                                    <?php

                                    require('include/tag_nav.php');

                                    ?>

                                </div>

                                <div class="container max-width-lg cd-timeline__container">

                                    <div class="section_content">

                                        <input type="hidden" name="pagination" id="pagination" value="1">

                                        <div class="clearfix" id="post-list_items">

                                            <?php

                                            foreach($main_query_main as $key => $row) {

                                                $tags = unserialize(json_decode($row["tags"]));

                                                $tags = array_values(array_filter($tags));

                                                $days = date("F j, Y",strtotime($row["timestamp"]));

                                                $description = $manipulation->limit_text(strip_tags($row["summary"]),30);

                                                $title = $manipulation->limit_text(strip_tags(stripslashes(stripslashes($row["title"]))),15);

                                                $id = $row["id"];

                                                $image_main_url = $setting['main_url'].'/main/'.$row["thumb_large_url"];
                                                
                                                $author_row = $get_content_article->get_author_name($row["site"]);

                                                $author = $manipulation->limit_text(strip_tags($author_row['name']),3);

                                                $image_class = "no_image";
                                                $background_class = "no_background";

                                                if(!$verifier->space($row["thumb_large_url"])) {
                                                    $image_class = "with_image";
                                                    $background_class = "with_background";

                                                }

                                                ?>

                                                <div class="cd-timeline__block">

                                                    <div class="cd-timeline__img cd-timeline__img--picture">
                                                    </div> <!-- cd-timeline__img -->

                                                    <div class="cd-timeline__content text-component">


                                                        <?php

                                                        if($type_array_limit[$key] == 1) {  ?>

                                                            <!-- Largest Card With Image -->
                                                            <div class="card card_largest_<?=$image_class?> grid-item">

                                                                <?php 
                                                                if($image_class == "with_image") { ?>
                                                                    <img class="card-img-top" src="<?=$image_main_url?>" alt="https://unsplash.com/@cjtagupa"><?php
                                                                }
                                                                ?>
                                                                <div class="card-body">
                                                                    <div class="card-title"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                                    <p class="card-text"><?=$description?></p>
                                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span> <?=$days?></span></small>
                                                                </div>
                                                            </div><?php

                                                        }
                                                        else if($type_array_limit[$key] == 2) {  ?>

                                                            <!-- Large Card With Background -->
                                                            <div class="card card_large_<?=$background_class?> grid-item">
                                                                <?php
                                                                if($background_class == "with_background") { ?>
                                                                    <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div><?php
                                                                }
                                                                ?>
                                                                <div class="card-body">
                                                                    <div class="card-title"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                                </div>
                                                            </div><?php

                                                        }
                                                        else if($type_array_limit[$key] == 3) { ?>

                                                            <!-- Default Card No Image -->
                                                            <div class="card card_default card_default_<?=$image_class?> grid-item">
                                                                <?php 
                                                                if($image_class == "with_image") { ?>
                                                                    <img class="card-img-top" src="<?=$image_main_url?>" alt="https://unsplash.com/@cjtagupa"><?php
                                                                }
                                                                ?>
                                                                <div class="card-body">
                                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                                </div>
                                                            </div><?php

                                                        }
                                                        else if($type_array_limit[$key] == 4) {  ?>

                                                            <!-- Default Card With Background -->

                                                            <div class="card card_default card_default_<?=$background_class?> grid-item">
                                                                    <?php
                                                                    if($background_class == "with_background") { ?>
                                                                        <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div><?php
                                                                    }
                                                                    ?>
                                                                    <div class="card-body">
                                                                        <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                                    </div>
                                                            </div><?php

                                                        }
                                                        else if($type_array_limit[$key] == 5) { ?>

                                                            <!-- Small Card With Image -->
                                                            <div class="card card_small_<?=$image_class?> grid-item">

                                                                <?php
                                                                if($image_class == "with_image") { ?>
                                                                    <img class="card-img-top" src="<?=$image_main_url?>" alt=""><?php
                                                                }
                                                                ?>
                                                                <div class="card-body">
                                                                    <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                                    <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                                </div>
                                                            </div><?php

                                                        }
                                                    
                                                        else if($type_array_limit[$key] == 6) { ?>

                                                                <!-- Small Card With Background -->
                                                                <div class="card card_default card_small_<?=$background_class?> grid-item">
                                                                    <?php
                                                                    if($background_class == "with_background") { ?>
                                                                        <div class="card_background" style="background-image:url(<?=$image_main_url?>)"></div><?php
                                                                    }
                                                                    ?>
                                                                    <div class="card-body">
                                                                        <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$title?></a></div>
                                                                        <small class="post_meta"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$id?>"><?=$author?></a><span><?=$days?></span></small>
                                                                    </div>
                                                                </div><?php

                                                        }


                                                        ?>

                                                    </div>
                                                    
                                                </div>

                                                <?php

                                            }

                                            ?>

                                        </div>

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
                
                    <?php 
                                                              
                    }
                    ?>
                
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

var limit = '11';query='<?=$category_row->id?>'; var type = 'category';

Scroll_content = new Scroll_content(site_url, page_content, limit, type, query);
    
$(document).ready(function(){
    
    Scroll_content.windowOnScroll();

});

</script>