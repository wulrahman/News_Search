<?php

    
if(count($tag_data) == 0) { 

    $article_query = mysqli_query($setting["Lid"], "(SELECT `news`.`tags`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`article` AND `timestamp` >= NOW() - INTERVAL 30 DAY) AS `count` FROM `news` ORDER BY `count`)");

    while($article_row = mysqli_fetch_array($article_query)) {

        $tags = array_keys(unserialize(json_decode($article_row['tags'])));

        $tags = array_values(array_filter($tags));

        foreach($tags as $key => $tag) {

            if(empty($tag_data[$tag])) {
                $tag_data[$tag] = $article_row['count'];
            }
            else {
                $tag_data[$tag]+=$article_row['count'];
            }
        }

    }

}

if(count($tag_data) > 0) { 

 
    //$tag_data = $manipulation->sum_multi_dimentional($tag_data);
    
    $tag_probability = $manipulation->probaility($tag_data);
    
    arsort($tag_probability);
    
    ?>

    <div class="section_tags ml-auto">
        <ul>
            <li class="active"><a href="<?=$setting['main_url']?>/?action=post">all</a></li>
            <?php
            $tags = array_slice ( $tag_probability, 0, 5);
            foreach($tags as $tag => $probability) { 
            
                $tag_name = $manipulation->limit_text(strip_tags($tag),6); ?>
                <li><a href="<?=$setting['main_url']?>/?q=<?=urlencode($tag)?>"><?=$tag_name?></a></li><?php

            }
            ?>
        </ul>
    </div>
    <div class="section_panel_more">
        <ul>
            <li>more
                <ul>
                    <?php
                    $tags = array_slice ( $tag_probability, 6, 6);
                    foreach($tags as $tag => $probability) { ?>
                        <li><a href="<?=$setting['main_url']?>/?q=<?=urlencode($tag)?>"><?=$tag?></a></li><?php

                    }
                          
                    ?>
                </ul>
            </li>
        </ul>
    </div><?php
}
?>