<?php

// don't let user kill the script by hitting the stop button 
//ignore_user_abort(true); 

// don't let the script time out 
//set_time_limit(0); 

// start output buffering
//ob_start();

$page_content = array(0);

$event_main_query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `timestamp`, `hash_id` FROM `events` WHERE `event` = 'Get Content' AND `done` !=`jobs` AND `done` < `jobs` ORDER BY `timestamp` ASC");

    //"`timestamp` + INTERVAL 1 HOUR >= now() AND"
$event_count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

if($event_count > 0) {
    
    $event_main_row = mysqli_fetch_object($event_main_query);
    
    $event_id = $event_main_row->id;

    
    $view_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS SQL_CALC_FOUND_ROWS * FROM `news` WHERE `hash_id`='".$event_main_row->hash_id."' AND `publish` = 0");
    
    $view_total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));
    
    if ($view_total_count > 0) {
        
        $content_main_row = mysqli_fetch_object($view_query);

        echo $content_main_row->id." Article Id";
        //$text_type =  $_GET['type'];

        $parse = parse_url($content_main_row->source_url);

        $url = mysqli(fix_url($content_main_row->source_url, $parse));

        $array = url_info(strtolower(redirection($url)), 1, "", "", 1);

        if($array['status'] == 200) {

            $array = site_info($array);

            $array = itemscope($array);

            $domain_count = substr_count($domain, '.'); // 2

            $pieces = explode(".", $domain); 

            if($domain_count == 1) {

                $array['domain_lenght'] = strlen($pieces[0]); 

            }
            else if($domain_count >= 2){ 

                $array['domain_lenght'] = strlen($pieces[1]);

            }

            $array['get_images_count'] = count($array['img']);

            foreach($array['img'] as $key => $image) {

                if(!space($image['alt'])) {

                    $array['get_alt_images']++;

                }

            }

            $array['get_alt_miss'] = $array['get_images_count'] - $array['get_alt_images'];

            $array['check_heading'] = 0;

            foreach($array['getheading'] as $heading => $headings){

                if(!empty($headings)){

                    $array['check_heading']++;

                }

            }

            $array['robot'] = url_info("http://".$domain."/robots.txt");

            $array['getheading']= get_headings_tag($array['response']);

            $time = $array['curl_info']['total_time'];	

            $curl_info = $array['curl_info'];

            $encoding = $array['main_header']['Content-Encoding'];

            $array['gzip']['page_size'] = mb_strlen($array['response'], '8bit');

            $array['gzip']['text_size'] = mb_strlen($array['content'], '8bit');

            $actual_lenght = strlen($array['response']);

            $strip_lenght = strlen($array['content']);

            $array['gzip']['getRatio'] = round((($strip_lenght/$actual_lenght)*100), 2);	

            if($encoding == "gzip") {

                $after_gzip = strlen(gzcompress($array['response']));

                $array['gzip']['convert_before'] = bytesToSize($actual_lenght);

                $array['gzip']['convert_after'] = bytesToSize($after_gzip);

                $array['gzip']['percentage'] = round((($actual_lenght - $after_gzip) / ($actual_lenght)) * 100, 2);

                $array['gzip_enable'] = "true";

            }

            $sentence = array();

            foreach($array['p'] as $key => $paragraph) {

                //preg_match("/[0-9.!?,;:]$/", $string)

                if(preg_match('/[\p{P}\p{N}]$/u', $paragraph)) {
                    $sentence[] = $paragraph;
                }
            }

            $meta = implode(" ", $sentence);

            $title = $array['title'];

            $description = $array['description'];

            $new_url = $array['url'];

            $response = mysqli($array['response']);

            $full_text = mysqli($array['full_text']);
            
            unset($array['response']);

        }
        else {

            $meta = $content_main_row->description;

        }
        
        $value_paragraph = 2;
        
        $text = filter_var($meta, FILTER_SANITIZE_STRING);
        
        $text_old =  $text;

        $paragraphs = preg_split("/(?<=[!?.])(?:$|\s+(?=\p{Lu}\p{Ll}*\b))/i",$text);

        $count_paragraphs = round((count($paragraphs)/$value_paragraph), 0, PHP_ROUND_HALF_UP);

        for ($i = 0; $i <= $count_paragraphs ;$i++) {

             $setence_number = (($i)*$value_paragraph);
                                              
             $new_array_sentence = array_slice($paragraphs, ($setence_number), ($value_paragraph));
                                          
             $new_paragraphs[$i] = '<p class="post_p">'.implode(" ", $new_array_sentence)." </p>";
                                              

        }
                
        $new_tags = array();

        $text = implode(" ", $new_paragraphs);
        $content_news = mysqli($text);
        
        $new_text = $text_old;
                
        $api = new TextRankFacade();
        // English implementation for stopwords/junk words:
        $stopWords = new English();
        $api->setStopWords($stopWords);

        // Array of the sentences from the most important part of the text:
        $result_highlights = $api->getHighlights($new_text);
        $array['results']['summary']['highlights'] = $result_highlights;
        $text_highlights = stripslashes(implode(" ", $result_highlights));
        //echo $text_highlights = longest_string_in_array($result_highlights)['0'];

        // Array of the most important sentences from the text:
        $result_summary = $api->summarizeTextBasic($new_text);
        $array['results']['summary']['summary'] = $result_summary;
        $text_summary = stripslashes(implode(" ", $result_summary));
        //echo $text_summary = longest_string_in_array($result_summary)['0'];
        
        // Note: en_US is the default language.
        $rake = RakePlus::create($text_highlights,  $setting['stopword_2']);
        $array['results']['phrases'] = $phrase_scores = $rake->sortByScore('desc')->scores();
        
        // start of sentiment anaysis//
        $sat = new SentimentAnalyzerTest(new SentimentAnalyzer());

        //https://blog.cambridgespark.com/50-free-machine-learning-datasets-sentiment-analysis-b9388f79c124
        
        $train_system = 0;
        
        global $Sentiments;
        
        if($train_system == 1) {

            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/books/negative.review', 'negative', 500000); //training with negative data
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/books/positive.review', 'positive', 500000); //trainign with positive data
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/dvd/negative.review', 'negative', 500000); //training with negative data
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/dvd/positive.review', 'positive', 500000); //trainign with positive data
            
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/electronics/negative.review', 'negative', 500000); //training with negative data
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/electronics/positive.review', 'positive', 500000); //trainign with positive data

            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/kitchen/negative.review', 'negative', 500000); //training with negative data
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/kitchen/positive.review', 'positive', 500000); //trainign with positive data
            //echo file_get_contents('../sentiment-analysis/trainingSet/negative-words.txt',);
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/negative-words.txt', 'negative', 500000); //training with negative data
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/positive-words.txt', 'positive', 500000); //trainign with positive data
            
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/negative-articles.txt', 'negative', 500000); //training with negative data
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/positive-articles.txt', 'positive', 500000); //trainign with positive data

            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/data.neg', 'negative', 500000); //training with negative data
            $sat->trainAnalyzer('../sentiment-analysis/trainingSet/data.pos', 'positive', 500000); //trainign with positive data
            
            // now pack it up again
            $fh = fopen('sentimentModel.txt', 'w');
            fwrite($fh, serialize($Sentiments));
            fclose($fh);
            
            
        }
        else {
            $Sentiments = unserialize(file_get_contents('sentimentModel.txt'));
        }

        $sentimentAnalysisOfSentence = array();
        //'/(?<=[.?!])\s+(?=[a-z])/i'
        $sentences = preg_split("/(?<=[!?.])(?:$|\s+(?=\p{Lu}\p{Ll}*\b))/i", $new_text);
                
        
        foreach($sentences as $key => $sentense) {
            
            $sentimentAnalysisOfSentence1 = $sat->analyzeSentence($sentense);
            $sentimentAnalysisOfSentence['positivity'][] =$sentimentAnalysisOfSentence1['accuracy']['positivity'];
            $sentimentAnalysisOfSentence['negativity'][] =$sentimentAnalysisOfSentence1['accuracy']['negativity'];
            $sentimentAnalysisOfSentence['sentiment'][] = $sentimentAnalysisOfSentence1['sentiment'];
                
        }
        
        //print_r($mapSentiments);
        //print_r($sentimentAnalysisOfSentence);
        
        $array['results']['sentiment']['positivity'] = array_sum($sentimentAnalysisOfSentence['positivity']);
        $array['results']['sentiment']['negativity'] = array_sum($sentimentAnalysisOfSentence['negativity']);
        
        if( $array['results']['sentiment']['positivity'] >  $array['results']['sentiment']['negativity'] ) {
            $outcome = "postive";
        }
        else if($array['results']['sentiment']['positivity'] < $array['results']['sentiment']['negativity'] ) {
            $outcome = "negative";
        }
        else {
            $outcome = "neatural";
        }
        
        $array['results']['sentiment']['sentiment'] = $outcome;
        
          // end of sentiment anaysis//
        
          // start of category anaysis//
        
        
        global $category_array;
        $category_array = array ("POLITICS", "WELLNESS", "ENTERTAINMENT", "TRAVEL",
                                 "STYLE & BEAUTY","PARENTING","HEALTHY LIVING","QUEER VOICES",
                                 "FOOD & DRINK","BUSINESS","COMEDY","SPORTS","BLACK VOICES",
                                 "HOME & LIVING","PARENTS","THE WORLDPOST","WEDDINGS","WOMEN",
                                 "IMPACT","DIVORCE","CRIME","MEDIA","WEIRD NEWS","GREEN",
                                 "WORLDPOST","RELIGION","STYLE","SCIENCE","WORLD NEWS","TASTE",
                                 "TECH","MONEY", "ARTS","FIFTY","GOOD NEWS","ARTS & CULTURE",
                                 "ENVIRONMENT","COLLEGE","LATINO VOICES", "CULTURE & ARTS",
                                 "EDUCATION");
        
        $sats = new CategoryAnalyzerTest(new CategoryAnalyzer());

        $train_system = 0;
        
        global $Categorys;
        
        if($train_system == 1) {
            
            $files = glob("../Classifier/trainingSet/bbc/entertainment/*.txt");

            foreach($files as $file) {
                $content = file_get_contents($file);
                $sats->trainAnalyzer(  $file,"BUSINESS", 500000); //training with negative data
            }

            $files = glob("../Classifier/trainingSet/bbc/politics/*.txt");

            foreach($files as $file) {
                $content = file_get_contents($file);
                $sats->trainAnalyzer(  $file,"POLITICS", 500000); //training with negative data
            }
            
            $files = glob("../Classifier/trainingSet/bbc/entertainment/*.txt");

            foreach($files as $file) {
                $content = file_get_contents($file);
                $sats->trainAnalyzer(  $file,"ENTERTAINMENT", 500000); //training with negative data
            }
            
            $files = glob("../Classifier/trainingSet/bbc/sport/*.txt");

            foreach($files as $file) {
                $content = file_get_contents($file);
                $sats->trainAnalyzer(  $file,"SPORTS", 500000); //training with negative data
            }

            $files = glob("../Classifier/trainingSet/bbc/tech/*.txt");

            foreach($files as $file) {
                $content = file_get_contents($file);
                $sats->trainAnalyzer(  $file,"TECH", 500000); //training with negative data
            }
            $fh = fopen('categoryModel.txt', 'w');
            fwrite($fh, serialize($Categorys));
            fclose($fh);
            //print_r($Categorys);
            
        }
        else {
            $Categorys = unserialize(file_get_contents('categoryModel.txt'));
            
        }

        foreach($sentences as $key => $sentense) {
               
            $categoryAnalysisOfSentence1 = $sats->analyzeSentence($sentense);
            //print_r($categoryAnalysisOfSentence1);
            foreach($categoryAnalysisOfSentence1['accuracy'] as $key => $value) {
                $categoryAnalysisOfSentence[$key][] = $categoryAnalysisOfSentence1['accuracy'][$key];
                
            }
            $categoryAnalysisOfSentence['sentiment'][] = $categoryAnalysisOfSentence1['sentiment'];
                   
        }
        
         //print_r($categoryAnalysisOfSentence["BUSINESS"]);
        
        foreach($category_array as $key  => $value) {
            
            $array['results']['category']['categorys'][$value] = array_sum($categoryAnalysisOfSentence[$value]);

            
        }
        
        $array_value = array_values($array['results']['category']['categorys']);
        
        $array_key = array_keys($array['results']['category']['categorys']);
        
        //print_r($array_key);
        
        $temp = 0;
        
        foreach($array_value as $key => $sorted) {
            
            if($sorted > $temp) {
                $temp = $sorted;
                $outcome = $array_key[$key];
            }
            
        }
        //echo $outcome;
        
        $array['results']['category']['category'] = $outcome;
        
        // end of category anaysis//
                           
        foreach(array_keys($array['results']['phrases']) as $key => $tag) {
            
            $tag = rtrim($tag, "'");
            
            $tag = rtrim($tag, "\'");
            
            $tag = rtrim($tag, "-");

            $color = random_color();
                
            $hash_id = md5($tag);
            
            $count = array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT COUNT(`id`) FROM `tags` WHERE `hash_id`='".$hash_id."'")));
            
            if($count == 0 && !space($tag)){
                
                mysqli_query($setting['Lid'],"INSERT INTO `tags` (`hash_id`, `tag`, `color`) VALUES ('".$hash_id."', '".mysqli($tag)."', '".$color."')");
                
                $new_tags[] = $tag;
        
            }
            else if(!space($tag)){
                
                mysqli_query($setting['Lid'],"UPDATE `tags` SET `publications` = `publications` + 1 WHERE `hash_id` = '".$hash_id."'");
                
                $new_tags[] = $tag;

            }
                
        }
        
        $textStatistics = new TextStatistics;
        $text_content = strip_tags($content_news);
          
        $array['readability']['fleschKincaidReadingEase'] = $textStatistics->fleschKincaidReadingEase($text_content);
        $array['readability']['fleschKincaidGradeLevel'] = $textStatistics->fleschKincaidGradeLevel($text_content);
        $array['readability']['gunningFogScore'] = $textStatistics->gunningFogScore($text_content);
        $array['readability']['colemanLiauIndex'] = $textStatistics->colemanLiauIndex($text_contentt);
        $array['readability']['smogIndex'] = $textStatistics->smogIndex($text_content);
        $array['readability']['automatedReadabilityIndex'] = $textStatistics->automatedReadabilityIndex($text_content);
        $array['readability']['daleChallReadabilityScore'] = $textStatistics->daleChallReadabilityScore($text_content);
        $array['readability']['spacheReadabilityScore'] = $textStatistics->spacheReadabilityScore($text_content);
        $array['readability']['faleChallDifficultWordCount'] = $textStatistics->daleChallDifficultWordCount($text_content);
        $array['readability']['spacheDifficultWordCount'] = $textStatistics->spacheDifficultWordCount($text_content);
        $array['readability']['letterCount'] = $textStatistics->letterCount($text_content);
        $array['readability']['sentenceCount'] = $textStatistics->sentenceCount($text_content);
        $array['readability']['wordCount'] = $textStatistics->wordCount($text_content);
        $array['readability']['averageWordsPerSentence'] = $textStatistics->averageWordsPerSentence($text_content);
        $array['readability']['syllableCount'] = $textStatistics->syllableCount($text_content);
        $array['readability']['totalSyllables'] = $textStatistics->totalSyllables($text_content);
        $array['readability']['averageSyllablesPerWord'] = $textStatistics->averageSyllablesPerWord($text_content);
        $array['readability']['wordsWithThreeSyllables'] = $textStatistics->wordsWithThreeSyllables($text_content);
        $array['readability']['percentageWordsWithThreeSyllables'] = $textStatistics->percentageWordsWithThreeSyllables($text_content);
        //http://www.readabilityformulas.com/free-readability-formula-tests.php
                  
        //print_r($new_tags);
        $readability  = '0';
        if($array['readability']['gunningFogScore'] > 8 && $array['readability']['smogIndex'] > 8 && $array['readability']['automatedReadabilityIndex'] > 8) {
            $readability  = '1';
        }
        $publish  = '1';
        if(count($new_tags) == 0) {
            $publish  = '0';
        }
        
        $palette = Palette::fromFilename($content_main_row->thumb_large_url);
        
        // $palette is an iterator on colors sorted by pixel count
        foreach($palette as $color => $count) {
            // colors are represented by integers
            $array["main_image"]["color"][Color::fromIntToHex($color)] = $count;
        }
            
        $main_image_colour = array_search(max($array["main_image"]["color"]), $array["main_image"]["color"]);
            
        $tags = mysqli(json_encode(serialize($new_tags)));
        
        $array_new = mysqli(json_encode(serialize($array)));
        
        mysqli_query($setting["Lid"],"UPDATE `events` SET `done` = '1' WHERE `events`.`Id` = '".$event_id."'");
        
        mysqli_query($setting["Lid"],"UPDATE `news` SET `tags`='".$tags."',`publish`='".$publish."',`readability`='".$readability."', `sentiment`='".$array['results']['sentiment']['sentiment']."',`category`='".$array['results']['category']['category']."', `summary`='".$text_summary."', `highlights`='".$text_highlights."', `cotent`='".$content_news."', `image_colour`='".$main_image_colour."',`response`='".$array_new."' WHERE `hash_id` = '".$event_main_row->hash_id."'");
        
        foreach($new_tags as $key => $tag) {
                                                                   
            $tag_main_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS MATCH(`cotent`) AGAINST('".$tag."' IN NATURAL LANGUAGE MODE) as `relevance` FROM `news` WHERE MATCH(`cotent`) AGAINST('".$tag."' IN NATURAL LANGUAGE MODE) AND `hash_id` = '".$event_main_row->hash_id."' GROUP BY `id` HAVING Relevance > 0 LIMIT 0, 1");
                                                                   
            $tag_relevence_row = mysqli_fetch_object($tag_main_query);
                                                       
            $tag_relevence[$tag] = $tag_relevence_row->relevance;
                                                       
        }
        
        $tag_relevence_new = mysqli(json_encode(serialize($tag_relevence)));

        mysqli_query($setting["Lid"],"UPDATE `news` SET `tags`='".$tag_relevence_new."' WHERE `hash_id` = '".$event_main_row->hash_id."'");
        
    }
    else {
        
         mysqli_query($setting["Lid"],"DELETE FROM `events` WHERE `hash_id` = '".$event_main_row->hash_id."'");

    }

    
}
    
   
    
/*
// now force PHP to output to the browser... 
$size = ob_get_length(); 

header("Content-Length: $size"); 

header('Connection: close'); 

ob_end_flush(); 

ob_flush(); 

flush(); // yes, you need to call all 3 flushes!

if (session_id()) session_write_close();
 
 

// everything after this will be executed in the background. 
// the user can leave the page, hit the stop button, whatever.
        
$classifier = new NaiveBayesClassifier();
    
//$classifier -> train($text, $category_array[$text_type]);
    
//echo $category_array[$text_type];

$category = $classifier -> classify($text);
$category);
*/
    
print("<pre>".print_r($array,true)."</pre>");

?>
<!--<head>
  <meta http-equiv="refresh" content="1">
</head>-->

