<?php

$type=intval($_GET['type']);

$id=intval($_GET['id']);

if($type == 1) {

    $query=mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `url`, `filetype` FROM `games` WHERE `id`='".$id."'");

    $count=array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));

    if($count > 0) {

        $row = mysqli_fetch_object($query);
        
        if(($row->filetype != 'dcr' && $row->filetype != 'swf' && $row->filetype != 'unity' && $row->filetype != 'unity3d') && ($row->filetype == 'code' || $row->filetype == 'html')) {
            
            header("refresh:5;url=".$row->url."");
            
            header('HTTP/1.0 300 redirect');

            $title="(300) redirect!";

            $error_code ="Your are now being redirected to an external source";

            $error_message = "Your are now being redirected to an external URL ".$row->url.". That's all we know.";

            $action = 'error';

            require_once('../main/include/header.php');

            require_once('../main/include/main_header.php');
            
            ?>

            <div class="super_container">

                <!-- Home -->

                <?php require_once("pages/404_require.php"); ?>

                <?php require_once("../main/include/main_footer.php"); ?>

            </div>

            <?php require_once("../main/include/footer.php"); 
            
        }

    }
    
}

?>