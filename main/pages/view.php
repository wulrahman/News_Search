<?php

$page_content = array(0);

$view_main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `id`='".intval($_GET['id'])."' AND `publish` = '1' LIMIT 1");

$view_total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

if ($view_total_count > 0) {
    
    $row_content = mysqli_fetch_object($view_main_query);
    
    $author_row = $get_content_article->get_author_name($row_content->author);
    
    $author = $author_row['name'];   

    $page_content[] = $row_content->id;

	$title_content = $row_content->title;
    
    $days = date("F j, Y",strtotime($row_content->timestamp));

    $image_main_url = $setting['main_url'].'/main/'.$row_content->thumb_large_url;
    
    $tags = array_keys(unserialize(json_decode($row_content->tags)));
    
    $tags = array_values(array_filter($tags));
    
    $array = unserialize(json_decode($row_content->response));
        
    $text = stripslashes($row_content->cotent);
    
    $text = preg_replace('#<[^>]+>#', ' ', $text);
    
    $title="Cragglist | ".$row_content->title;
    
    if(!$verifier->space($row_content->summary)) {
                                                  
        $paragraphs = preg_split("/(?<=[!?.])(?:$|\s+(?=\p{Lu}\p{Ll}*\b))/i",$row_content->summary);
        
    }

    
    $description = $paragraphs[0];
    
    $keywords= implode(",", $tags);
    
    require_once('include/header.php');

	require_once('include/main_header.php');
    
    $text_new =  $text;
    
    $main_query_category = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `categorys`  WHERE `id` ='".$row_content->category."'  ORDER BY `cat_order` ASC LIMIT 0,1");
            
    $total_count_category=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
    
    if( $total_count_category > 0) {
        
        $category_row = mysqli_fetch_object($main_query_category);
        
        if($user->login_status == 1) {
        
            mysqli_query($setting["Lid"], "INSERT INTO `clicks` (`type`, `timestamp`, `count`, `category`, `user`, `identifier`) VALUES ('category', CURRENT_TIMESTAMP, '1', '".$category_row->id."', '".$user->id."', '".$user->cookie_id."')");
            
        }
        else {
            
            mysqli_query($setting["Lid"], "INSERT INTO `clicks` (`type`, `timestamp`, `count`, `category`, `identifier`) VALUES ('category', CURRENT_TIMESTAMP, '1', '".$category_row->id."', '".$user->cookie_id."')");
            
        }
        
    }
        
    if($user->login_status == 1) {
        
        mysqli_query($setting["Lid"], "INSERT INTO `clicks` (`type`, `timestamp`, `count`, `article`, `user`, `identifier`) VALUES ('article', CURRENT_TIMESTAMP, '1', '".$row_content->id."', '".$user->id."', '".$user->cookie_id."')");
        
    }
    else {
        
        mysqli_query($setting["Lid"], "INSERT INTO `clicks` (`type`, `timestamp`, `count`, `article`, `identifier`) VALUES ('article', CURRENT_TIMESTAMP, '1', '".$row_content->id."', '".$user->cookie_id."')");
        
    }
            

        
	?>



<div class="super_container">

    <?php
    
    require_once('include/main_header.php');
        
    ?>

    <!-- Home -->

    <div class="home">
        <div class="home_background parallax-window" data-parallax="scroll" data-image-src="<?=$image_main_url?>" data-speed="0.8"></div>
        <div class="home_content">
            <div class="post_category trans_200"><a href="<?=$setting['main_url']?>/?action=post&category=<?=$category_row->name?>" class="trans_200"><?=$category_row->name?></a></div>
            <div class="post_title"><?=$title_content?> <?=$user->admin?'<a href="'.$setting['main_url'].'/admin/?type=editnews&id='.$row_content->id.'" class="trans_200">edit</a>':false?></div>
        </div>
    </div>
    
    <!-- Page Content -->

    <div class="page_content">
        <div class="container">
            <div class="row row-lg-eq-height">

                <!-- Post Content -->

                <div class="col-lg-9">
                    <div class="post_content">

                        <!-- Top Panel -->
                        <div class="post_panel post_panel_top d-flex flex-row align-items-center justify-content-start">
                            <?php
        
                            $author = $manipulation->limit_text(strip_tags($author),3); ?>
    
                            <div class="author_image"><div><img src="images/author.jpg" alt=""></div></div>
                            <div class="post_meta"><a href="#"><?=$author?></a><span><?=$days?></span></div>

                        </div>

                        <!-- Post Body -->

                        <div class="post_body">

                        <?php
                        if (!$verifier->space($array['figcaption'][0])) { ?>
                        
                            <p class="text-bg"><?=$array['figcaption'][0]?></p><?php
                                                            
                        }
                   
                        if(!$verifier->space($row_content->summary)) {
                                                  
                            $paragraphs = preg_split("/(?<=[!?.])(?:$|\s+(?=\p{Lu}\p{Ll}*\b))/i",$row_content->summary);

                            $value_paragraph = 2;

                            $count_paragraphs = round((count($paragraphs)/$value_paragraph), 0, PHP_ROUND_HALF_UP);
                                
                            $setence_count = count($paragraphs);

                            for ($i = 0; $i < $count_paragraphs ;$i++) {
                                
                                $setence_number = (($i)*$value_paragraph);
                                
                                $new_array_sentence = array_slice($paragraphs, ($setence_number), ($value_paragraph));
                            
                                $new_paragraphs_summary[$i] = '<p class="post_p">'.implode(" ", $new_array_sentence)." </p> ";
                                

                            }
                                
                            $row_content->summary = implode("", $new_paragraphs_summary); ?>

                            <h3>Summary </h3>
                            <?=$row_content->summary?><?php
                        
                        }
                        ?>

                        <?php
                        if(!$verifier->space($row_content->highlights)) {
                            
                            $paragraphs = preg_split("/(?<=[!?.])(?:$|\s+(?=\p{Lu}\p{Ll}*\b))/i",$row_content->highlights);

                            $value_paragraph = 2;

                            $count_paragraphs = round((count($paragraphs)/$value_paragraph), 0, PHP_ROUND_HALF_UP);
                                
                            $setence_count = count($paragraphs);

                            for ($i = 0; $i < $count_paragraphs ;$i++) {
                                
                                $setence_number = (($i)*$value_paragraph);
                                
                                $new_array_sentence = array_slice($paragraphs, ($setence_number), ($value_paragraph));
                            
                                $new_paragraphs_highlights[$i] = '<p class="post_p">'.implode(" ", $new_array_sentence)." </p>";
                                

                            }
                                
                            $row_content->highlights = implode("", $new_paragraphs_highlights); ?>
                            <h3>Highlights </h3>
                            <?=$row_content->highlights?><?php
                        }
                        ?>
                        <h3>Article </h3>
                            
                        
                         <?php
                          
                        $paragraphs = preg_split("/(?<=[!?.])(?:$|\s+(?=\p{Lu}\p{Ll}*\b))/i",$text);
                            
                        $value_paragraph = 2;

                        $count_paragraphs = round((count($paragraphs)/$value_paragraph), 0, PHP_ROUND_HALF_UP);
                            
                        $setence_count = count($paragraphs);

                        for ($i = 0; $i < $count_paragraphs ;$i++) {
                            
                            $setence_number = (($i)*$value_paragraph);
                            
                            $new_array_sentence = array_slice($paragraphs, ($setence_number), ($value_paragraph));
                        
                            $new_paragraphs_main[$i] = '<p class="post_p">'.implode(" ", $new_array_sentence)." </p>";
                            

                        }
                            
                        $text = implode("", $new_paragraphs_main);
                            
                        //print_r($new_paragraphs);
    
                              
                        ?>

                        <?=$text?>

                            <!-- Post Tags -->
                            <div class="post_tags">
                                <ul>
                                    <?php
    

                                    $tags = array_slice ( $tags, 0, 6);
    
                                    foreach($tags as $key => $tag) { ?>
                                        <li class="post_tag"><a href="<?=$setting['main_url']?>/?q=<?=urlencode($tag)?>"><?=$tag?></a></li><?php
                                        
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Bottom Panel -->
                        <div class="post_panel bottom_panel d-flex flex-row align-items-center justify-content-start">
                            <div class="author_image"><div><img src="images/author.jpg" alt=""></div></div>
                            <div class="post_meta"><a href="#"><?=$author?></a><span><?=$days?></span></div>
                        </div>

                        <!-- Similar Posts -->
                        <div class="similar_posts">
                            <div class="grid clearfix">

                                    <?php

                                    $similar_items = $get_content_article->get_relevent_content($row_content->id, 3);
                                        
                                    //print_r($similar_items);
                                       
                                    foreach($similar_items as $key => $row_similar_content) {
                                       
                                        $tag_similar = unserialize(json_decode($row_similar_content['tags']));

                                        $tags_similar = array_values(array_filter($tags_similar));

                                        $days_similar = date("F j, Y",strtotime($row_similar_content['timestamp']));
                                       
                                        $title_similar  = $manipulation->limit_text(strip_tags($row_similar_content['title']),8);

                                        $image_similar_main_url = $setting['main_url'].'/main/'.$row_similar_content['thumb_large_url'];
                                        
                                        $author_row = $get_content_article->get_author_name($row_similar_content['author']);
                                
                                        $author = $manipulation->limit_text(strip_tags($author_row['name']),3);

                                        ?>


                                           <!-- Small Card With Image -->
                                           <div class="card card_small_with_image grid-item">
                                               <img class="card-img-top" src="<?=$image_similar_main_url?>" alt="https://unsplash.com/@jakobowens1">
                                               <div class="card-body">
                                                   <div class="card-title card-title-small"><a href="<?=$setting['main_url']?>/?action=view&id=<?=$row_similar_content['id']?>"><?=$title_similar?></a></div>
                                                   <small class="post_meta"><a href="#"><?=$author?></a><span><?=$days_similar?></span></small>
                                               </div>
                                           </div><?php

                                               
                                    }

                                    ?>

                            </div>

                        </div>
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

    <?php require_once('include/main_footer.php'); ?>
</div>

	<?php
    
	require_once("include/footer.php");

}
else {

    $action = 'error';

    require_once('include/header.php');

    require_once('include/main_header.php');
    
    $error_code = "OUCH!!! OW!! NO!!!";

    $error_message = "I don't know how this happened, but hear me out i'm getting to it.";

    ?>

    <div class="super_container">

        <!-- Home -->
        <?php require_once("../common/pages/404_require.php");?>

        <?php require_once("include/main_footer.php"); ?>

    </div>

    <?php require_once("include/footer.php"); 
}
?>
