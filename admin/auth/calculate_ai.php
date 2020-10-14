 
<?php

ini_set('memory_limit', '10G'); // or you could use 1G

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

if($user->admin == 1) {

	require_once('include/header.php');

	require_once('include/main_header.php');

	require_once('include/main_nav.php');    
 
    $page=intval($_GET['page']);

    if ($page == 0) {

        $page = 1;

    }

    $limit = 16;

    $new_ai = new AI_WAHEED;

    $path  = "../library/Syllable";

    $tage_extraction = new tage_extraction($path);

    $status_train = 1;
    
    $train_system_sentiment = $_POST['sentiment']?1:0;

    $train_system_category = $_POST['category']?1:0;

    $train_system_tags = $_POST['tags']?1:0;

    $setting['epochs'] = 1;

    $get_suffix_language = 0;

    $get_word_suffix = 1;

    if($get_suffix_language == 1) {

        $words = $tage_extraction->get_word_list('../library/AI/words_ai_test.txt');

        $word_array = $words['words'];

        $word_array_i = $words['wordsi'];
        
        $outputs_key = $words['outputs_key'];
        
        foreach($word_array_i as $key => $type) {

            $new_words = $tage_extraction->get_english_syllables($path, implode(" ", $type));

            foreach($new_words as $keys => $word) {

                $word_lengh = strlen($word);

                $syllabels[$keys] = array_filter(explode("-", $word));

                if(count($syllabels[$keys]) >= 1) {

                    $suffex_1[$keys] = end($syllabels[$keys]);
    
                    if(strlen($suffex_1[$keys]) != $word_lenght && $tage_extraction->endsWith($word, $suffex_1[$keys])) {
    
                       $suffex[$key][] = $suffex_1[$keys];
    
                    }
    
                }

            }


            $suffex_value_count[$key] = array_count_values($suffex[$key]);

            // $array_value_filter_value[$key] = array_filter($array_value_count[$key], fn ($m) => $m >= $average_array[$key]);

        }

        $fh = fopen('../library/AI/tags_ai_suffex.txt', 'w');
        fwrite($fh, serialize(json_encode($suffex_value_count)));
        fclose($fh);

        
    }
    
    
    $new_ai_sentiment = new AI_WAHEED;

    if($train_system_sentiment == 1 && isset($_POST['submit'])) {
        
         foreach($setting['sentiment_data_set'] as $key => $sentiment_file_data) {

            if($sentiment_file_data["type"] == 1 && $sentiment_file_data["trained"] == $status_train ) {

                $files = glob($sentiment_file_data["url"]);

                foreach($files as $file) {

                    $content = file_get_contents($file);

                    $array_sentiment['document'][$sentiment_file_data["sentiment"]][] = $new_ai_sentiment->document_matrix($content);

                }


            }
            if($sentiment_file_data["type"] == 2 && $sentiment_file_data["trained"] == $status_train) {

                $content = file_get_contents($sentiment_file_data["url"]);

                $array_sentiment['document'][$sentiment_file_data["sentiment"]][] = $new_ai_sentiment->document_matrix($content);

            }

        }



        $query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `category`, `sentiment`, `cotent`, `hash_id` FROM `news` WHERE `training_set`='1' ORDER BY `id` DESC");

        $count=array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));

        if($count > 0) { 

            while ($row = mysqli_fetch_object($query)) { 

                $query_event = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `done` FROM `events` WHERE `event`='Label Data' AND `hash_id` = '".$row->hash_id."' ORDER BY `id` DESC"); 

                $row_event = mysqli_fetch_object($query_event);

                if($row_event->done == 1) {

                    $text = stripslashes($row->cotent);

                    if($row->sentiment == 3) {
                        $sentiment = 'positive';
                    }
                    else  if($row->sentiment == 2) {
                        $sentiment = 'negative';
                    }
                    
                    $array_sentiment['document'][$sentiment][] = $new_ai_sentiment->document_matrix($text);

                }

            }

        }
        
        if(!empty($array_sentiment)) {
            
            $probability_sentiment = $new_ai_sentiment->train($array_sentiment);
            
            $fh = fopen('../library/AI/sentiment_ai_test.txt', 'w');
            fwrite($fh, serialize($probability_sentiment));
            fclose($fh);
            
        }



    }


    $new_ai = new AI_WAHEED;

    if($train_system_category == 1) {

        foreach($setting['category_data_sets'] as $key => $category_file_data) {

            if($category_file_data["type"] == 1 && $category_file_data["trained"] == 1) {

                $files = glob($category_file_data["url"]);

                foreach($files as $file) {

                    $content = file_get_contents($file);

                    $array['document'][$category_file_data["category"]][] = $new_ai->document_matrix($content);

                }

            }

        }


        $query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `category`, `sentiment`, `cotent`, `hash_id` FROM `news` WHERE `training_set`='1' ORDER BY `id` DESC");

        $count=array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));

        if($count > 0) { 

            while ($row = mysqli_fetch_object($query)) { 

                $query_event = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `done` FROM `events` WHERE `event`='Label Data' AND `hash_id` = '".$row->hash_id."' ORDER BY `id` DESC"); 

                $row_event = mysqli_fetch_object($query_event);

                if($row_event->done == 1) {

                    $text = stripslashes($row->cotent);

                    $category_row = $get_content_article->get_category_name($row->category);

                    $array['document'][$category_row["name"]][] = $new_ai->document_matrix($content);


                }                       

            }

        }

        
        if(!empty($array)) {
            
            $probability = $new_ai->train($array);

            $fh = fopen('../library/AI/category_ai_test.txt', 'w');
            fwrite($fh, serialize($probability));
            fclose($fh);
            
        }



    }
    
    ?>


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
										<i class="notika-icon notika-windows"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Training Naive Bayes classifier and Neural Network</h2>
										<p>Here you'll find options to retrain the classifier and neural network, this being sentiment classifier, category classifier and tag generator.</p>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
            
            
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <form action="?type=calculate_ai" method="post" enctype="multipart/form-data" onsubmit="return SubmitForm(this)">
                        <div class="form-element-list mg-t-30">
                            <div class="basic-tb-hd">
                                <h2>Choose AIs to retrain</h2>
                                <p>Please check the classifier you'd like to retain, please take note that it could tak several minutes for this to take effect. It's advise that you acquire new traing data before proceeding, since not doing so will not result in any change in the classifier performance.</p>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="fm-checkbox">
                                        <label><input type="checkbox" name="sentiment" class="i-checks"> <i></i> Selecting this will retain the sentiment classifier.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="fm-checkbox">
                                        <label><input type="checkbox" name="category" class="i-checks"> <i></i> Checking this option will retain the category classifier.</label>
                                    </div>
                                </div>
                            </div>
                            <p></p>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <button class="btn btn-success notika-btn-success" name="submit">Retrian classifier</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
		</div>
	</div>
	<!-- Breadcomb area End-->
    <!-- Data Table area Start-->


    <?php

    require_once("include/main_footer.php");
    
    require_once("include/footer.php");
    
}
else {

	require_once('../common/pages/404.php');

}

//https://www.youtube.com/watch?v=EGKeC2S44Rs
?>
