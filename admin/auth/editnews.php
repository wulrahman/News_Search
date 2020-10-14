<?php

if($user->admin == 1) {
    
    $id = intval($_GET['id']);

	$row = mysqli_fetch_object(mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `id`='".$id."'"));

	$count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

	if ($count > 0) {
        
        require_once('include/header.php');
        
        require_once('include/main_header.php');
        
        require_once('include/main_nav.php');
            
        if(isset($_POST['submit'])) {
            
            $name = $manipulation->htmlstring(htmlentities($_POST['name']));
            
            $content = mysqli($_POST['content']);
            
			$category = mysqli($_POST['category']);
            
            if(in_array($category, $setting["category_array"])) {
                
                $category_name_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `categorys`  WHERE `name` ='".$category."'  ORDER BY `cat_order` ASC LIMIT 0,1");
    
                $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
                
                if($total_count > 0) {

                    $category_row = mysqli_fetch_object($category_name_query);

                    $category_id = $category_row->id;

                }
                else {

                    mysqli_query($setting['Lid'],"INSERT INTO `categorys` (`name`) VALUES ('".$category."')");

                    $category_id = mysqli_insert_id($setting["Lid"]);

                }

            }
            
			$published = intval($_POST['published']?1:0);
            
            $training_set_processed = intval($_POST['processed']?1:0);
            
            $training_set = intval($_POST['training_set']?1:0);
            
            $sentiment = intval($_POST['sentiment']); 
            
            if(isset($_FILES["image"])) {
        
                if ($_FILES["image"]["error"] > 0) {

                   $error[] ="Return Code: ".$_FILES["image"]["error"];

                }
                else {

                    $thumb = "../games/games/images/".$_FILES["image"]["name"];
                        
                    $filename = basename($_FILES['image']['name']);

                    $ext = substr($filename, strrpos($filename, '.') + 1);

                    if (file_exists($thumb)) {

                        $errors_array[] = $_FILES["image"]["name"]." already exists.";

                    }
                    else if ((strpos($ext, "php") !== false) || $ext == 'aspx' || $ext == 'py' || $ext == 'htaccess') {

                        $errors_array[] = 'Uploading PHP files disabled';

                    }
                    else {
                        
                        $image = $setting['url']."/games/games/images/".$_FILES["image"]["name"];

                        move_uploaded_file($_FILES["image"]["tmp_name"], $thumb);

                    }

                }

            }
            
            
            
            if($training_set == 1) {
                mysqli_query($setting['Lid'],"INSERT INTO `events` (`event`, `hash_id`, `jobs`) VALUES ('Label Data', '".$row->hash_id."', '1')");
            }
            
            if($training_set_processed == 1) {
                mysqli_query($setting["Lid"],"UPDATE `events` SET `done` = '1' WHERE `events`.`hash_id` = '".$row->hash_id."' AND `event` = 'Label Data'");
                
            }

            
             mysqli_query($setting['Lid'], "UPDATE `news` SET `title`='".$name."',`publish`='".$published."',`cotent`='".$content."',`category`='".$category_id."',`sentiment`='".$sentiment."',`training_set`='".$training_set."' WHERE `id`='".$row->id."'");
            
        
        }
        
        $row = mysqli_fetch_object(mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `news` WHERE `id`='".$row->id."'"));

        $array_new = unserialize(json_decode($row->response));
        
        if(array_key_exists('h2', $array_new)) {
        
            $array_headings = array_merge($array_new['h1'], $array_new['h2']);
            
        } 
        else {
        
            $array_headings = $array_new['h1'];
            
        }
        
        if(array_key_exists('h3', $array_new)) {

            $array_headings = array_merge($array_headings, $array_new['h3']);
        
        }
        
        if(array_key_exists('h4', $array_new)) {
        
            $array_headings = array_merge($array_headings, $array_new['h4']);
            
        }
        
        if(array_key_exists('h5', $array_new)) {
        
            $array_headings = array_merge($array_headings, $array_new['h5']);
            
        }

        
		?>



    
    <!-- Main Menu area End-->
	<!-- Breadcomb area Start-->
	<div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-form"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Content Review Form</h2>
										<p>Please review the content and appropriate assign, alter content to the guidelines.</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button data-toggle="tooltip" data-placement="left" title="Download Report" class="btn"><i class="notika-icon notika-sent"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <!-- Form Element area Start-->
    <div class="form-element-area">
    
        <form method="post" enctype="multipart/form-data" onsubmit="return SubmitForm(this)">

            <div class="container">
                <div class="row">
                    <?php 
                    foreach($array_new['img'] as $key => $image) { ?>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                            <img class="animate-one" src="<?=$image['src']?>" alt="<?=$image['alt']?>">
                        </div><?php
                    }
                    ?>
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form-element-list mg-t-30">
                            
                            <div class="row">
                                
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="nk-int-st">
                                        <h4>Article Title</h4>
                                        <textarea class="form-control" rows="2" name="name" placeholder="Start pressing Enter to see growing..."><?=$row->title?></textarea>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <div class="cmp-tb-hd mg-t-20">
                                <p></p>
                            </div>
                                
                             <div class="row">
                                
                               <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="nk-int-st">
                                        <div class="cmp-tb-hd mg-t-20">

                                            <h4>Alternative Headings</h4>
                                            <p>Please copy and paste any heading into the above "Article Title" input you feel best describes the article and it's overall message in comparison to the heading pre-selected by our automated system.</p>
                                             <?php 
                                            foreach($array_headings as $key => $heading) { ?>

                                                <p><code><?=stripslashes($heading)?>.</code></p><?php
                                            }
                                            ?>
                                            
                                        </div>
                                    </div>
                                </div>
         
                            </div>
                            
                             <div class="row">
                                 
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                         
                                    <div class="cmp-tb-hd mg-t-20">
                                        <h4>Article Content</h4>
                                        <p>Please edit content to correcting any grammatical errors, altering overall format to make content more consistent throughout, fixing sentence structure while keeping the overall message of the article intact.</p>
                                    </div>
                                   
                                    <textarea class="html-editor"  name="content"><?=stripslashes($row->cotent)?></textarea>
   
                                </div>
                                 
                            </div>
                                
                            <div class="cmp-tb-hd mg-t-20">
                                <p></p>
                            </div>

                            <div class="row">

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="nk-int-mk sl-dp-mn">
                                        <h2>Sentiment</h2>
                                    </div>
                                    <div class="bootstrap-select fm-cmp-mg">
                                        <?php 
                                                
                                        if($row->sentiment == 1) {
                                                $positive_selected = "selected";
                                        }
                                        else if($row->sentiment == 2) {
                                                $negative_selected = "selected";
                                        }
                                        ?>
                                        <select class="selectpicker" name="sentiment" >
                                            <option <?=$positive_selected?> value="1">Positive</option>
                                            <option <?=$negative_selected?> value="2">Negative</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="nk-int-mk sl-dp-mn sm-res-mg-t-10">
                                        <h2>Category</h2>
                                    </div>
                                    <div class="bootstrap-select fm-cmp-mg">
                                        <select class="selectpicker" data-live-search="true" name="category">
                                            
                                                <?php
                                                                                        
                                                $category_row = $get_content_article->get_category_name($row->category);

                                                foreach($setting["category_array"] as $key => $category) {

                                                        if($category_row['name'] == $category) {
                                                            $category_selected = "selected";
                                                        }

                                                        ?>

                                                        <option value="<?=$category?>" <?=$category_selected?>>
                                                           <?=$category?>
                                                        </option><?php

                                                        $category_selected = "";

                                                }

                                                ?>
                                        </select>
                                    </div>
                                </div>
                                 
                                <div class="cmp-tb-hd mg-t-20">
                                    <p></p>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    
                                    <?php
        
                                    $query_event = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `done` FROM `events` WHERE `event`='Label Data' AND `hash_id` = '".$row->hash_id."' ORDER BY `id` DESC"); 
                                    
                                    $row_event = mysqli_fetch_object($query_event);
                                        
                                    $processed_data_check = "";
                                    if($row_event->done == 1) {
                                        $processed_data_check = "checked";
                                    }
        
                                    ?>

                                    <div class="fm-checkbox">
                                        <label><input type="checkbox" <?=$processed_data_check?> class="i-checks" name="processed"> <i></i> I have read this article, selected what I believe to be the correct - best matched - sentiment and category for this article.</label>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                    <?php 
        
                                    $checked_publish_value = 0;
                                    if($row->publish == 1) {
                                        $checked_publish = "checked";
                                        $checked_publish_value = 1;
                                    }
                                    ?>

                                    <div class="fm-checkbox">
                                        <label><input type="checkbox" <?=$checked_publish?> class="i-checks1" name="published"> <i></i> I have read this article and to my level of comprehension this article is readable and appropriate for this type of audience and demographic.</label>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <?php
                                    $checked_training_value = 0;
                                    if($row->training_set == 1) {
                                        $training_set = "checked";
                                        $checked_training_value = 1;
                                    }
                                    ?>
                                    <div class="fm-checkbox">
                                        <label><input type="checkbox" <?=$training_set?> class="i-checks2" name="training_set"> <i></i> This article is to be labled as training data, in the event it's mislabled by our automated system.</label>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-example-int mg-t-15">
                                            <button type="submit" name="submit" value="Save" class="btn btn-success notika-btn-success waves-effect">Save</button>
                                        </div>
                                </div>

                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>

            </div>

        </form>

    </div>
<?php
//echo $_REQUEST["content-textarea"];
        
      ?>

    <!-- Form Element area End-->

    <!-- Dropzone area Start-->
    <div class="dropzone-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="dropdone-nk mg-t-30">
                        <div id="dropzone1" class="multi-uploader-cs">
                            <form action="/upload" class="dropzone dropzone-nk needsclick" id="demo1-upload" enctype="multipart/form-data">
                                <div class="dz-message needsclick download-custom">
                                    <i class="notika-icon notika-cloud"></i>
                                    <h2>Drop files here or click to upload.</h2>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Dropzone area End-->


    <?php
        
    }
    

    require_once("include/main_footer.php");
    
	require_once("include/footer.php");
    
}
else {

	require_once('../common/pages/404.php');

}

?>
