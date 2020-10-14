 
<?php

error_reporting(0);

session_start();
    
//error_reporting(E_ALL);

//https://www.youtube.com/watch?v=EGKeC2S44Rs
//https://github.com/awesomedata/awesome-public-datasets#agriculture
//https://datasetsearch.research.google.com/search?query=news&docid=pVEhRValD0vJaC7PAAAAAA%3D%3D
//https://www.statista.com/statistics/264488/important-news-categories-provided-by-local-news-apps-in-the-us
//https://towardsdatascience.com/top-sources-for-machine-learning-datasets-bb6d0dc3378b

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");
    
class AI_WAHEED {
    
    function train($array) {
        
        //print_r($document_matrix);
        
        $array_phrases = array();

        foreach($array['document'] as $label => $documents) {

            foreach($documents as $key => $phrases) {

                foreach($phrases as $phrase => $score) {

                    if(array_key_exists($phrase,  $array_phrases['label'][$label])) {

                        $array_phrases['label'][$label][$phrase] += $score;

                    }
                    else {
                        $array_phrases['label'][$label][$phrase] = $score;
                    }


                    if(array_key_exists($phrase,  $array_phrases['phrase'])) {

                        $array_phrases['phrase'][$phrase] += $score;

                    }
                    else {
                        $array_phrases['phrase'][$phrase] = $score;
                    }
                }

            }

        }

        $vocabulary = count($array_phrases['phrase']);

        $total_documents = count($array['document']);

        foreach ($array_phrases['label'] as $label => $phrases) {

            $n = array_sum(array_values($phrases));

            $probability["label"][$label] = count($array['document'][$label])/ $total_documents;

            foreach($phrases as $phrase => $n_k) {
                $probability[$label][$phrase] = ($n_k+1)/($n + $vocabulary);
            }

        }
        
        return $probability;
        
    }
    
    function document_matrix($document_matrix) {
        
    }
    
    function predict($trained_data, $data, $tune_value) {
        
        //print_r($trained_data);
        
        $words = self::splitSentence($data)[0];
                                    
        $score[] = array();

        foreach($trained_data as $label => $data) {
            
            foreach($words as $key => $word) {
                
                //remove words that may occure too often which could negatively effect the results

                $limit_score = $tune_value;

                $min_score = min($data);

                if(array_key_exists($word, $data)) {

                    $score["label"][$label][$word] = $data[$word];

                }
                else {

                    $score["label"][$label][$word] = $min_score;

                }

            }

        }
        
        $final_score_main_array[] = array();

        foreach($score["label"] as $label => $scores) {

            $final_score_main = $trained_data["label"][$label];
            
            foreach($scores as $key => $score) {
                
                $final_score_main_array[] = $score;
                
            }

            $final_score[$label] = array_product($scores)*$final_score_main;

        }
        
        $final_score["category"] = array_keys($final_score, max($final_score));
        
        return $final_score;
        
        
    }
    
    function splitSentence($words) {
        
        preg_match_all('/\w+/', $words, $matches);
        return $matches;
        
    }
    
    function sentenceCase($string) { 
    
        $sentences = preg_split('/([.?!]+)/', $string, -1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE); 
        $newString = ''; 

        foreach ($sentences as $key => $sentence) { 

            $newString .= ($key & 1) == 0? 
                ucfirst(strtolower(trim($sentence))) : 
                $sentence.' '; 

        } 

        return trim($newString); 

    }

    function breakLongText($text, $length = 200, $maxLength = 250) {

        //Text length
        $textLength = strlen($text);

        //initialize empty array to store split text
        $splitText = array();

        //return without breaking if text is already short
        if (!($textLength > $maxLength)){
            $splitText[] = $text;
            return $splitText;
        }

        //Guess sentence completion
        $needle = '.';

        /*iterate over $text length 
        as substr_replace deleting it*/  
        while (strlen($text) > $length) {

            $end = strpos($text, $needle, $length);

            if ($end === false) {

                //Returns FALSE if the needle (in this case ".") was not found.
                $splitText[] = substr($text,0);
                $text = '';
                break;

            }

            $end++;
            $splitText[] = substr($text,0,$end);
            $text = substr_replace($text,'',0,$end);

        }

        if ($text) {
             $splitText[] = substr($text,0);
        }

        return $splitText;

    }

    
}

$process_data = 0;


    $new_ai = new AI_WAHEED;


if($process_data == 1) {


    $category_data_sets = array(
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/sci.crypt/*", "type" => 1, "trained" => 1, "category"=>"SCIENCE"), 
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/sci.electronics/*", "type" => 1, "trained" => 1, "category"=>"SCIENCE"), 
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/sci.med/*", "type" => 1, "trained" => 1, "category"=>"SCIENCE"), array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/sci.space/*", "type" => 1, "trained" => 1, "category"=>"SCIENCE"), array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/misc.forsale/*", "type" => 1, "trained" => 1, "category"=>"HOME & LIVING"),  
        array("url" => "../../library/Classifier/trainingSet/bbc/business/*.txt", "type" => 1, "trained" => 1, "category"=>"BUSINESS"),  
        array("url" => "../../library/Classifier/trainingSet/bbc/politics/*.txt", "type" => 1, "trained" => 1, "category"=>"POLITICS"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/talk.politics.guns/*", "type" => 1, "trained" => 1, "category"=>"POLITICS"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/talk.politics.guns/*", "type" => 1, "trained" => 1, "category"=>"POLITICS"), 
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/talk.politics.misc/*", "type" => 1, "trained" => 1, "category"=>"POLITICS"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/talk.politics.mideast/*", "type" => 1, "trained" => 1, "category"=>"POLITICS"), 
        array("url" => "../../library/Classifier/trainingSet/bbc/entertainment/*.txt", "type" => 1, "trained" => 1, "category"=>"ENTERTAINMENT"),
        array("url" => "../../library/Classifier/trainingSet/bbc/sport/*.txt", "type" => 1, "trained" => 1, "category"=>"SPORTS"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/rec.autos/*", "type" => 1, "trained" => 1, "category"=>"SPORTS"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/rec.motorcycles/*", "type" => 1, "trained" => 1, "category"=>"SPORTS"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/rec.sport.baseball/*", "type" => 1, "trained" => 1, "category"=>"SPORTS"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/rec.sport.hockey/*", "type" => 1, "trained" => 1, "category"=>"SPORTS"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/comp.graphics/*", "type" => 1, "trained" => 1, "category"=>"TECH"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/comp.os.ms-windows.misc/*", "type" => 1, "trained" => 1, "category"=>"TECH"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/comp.sys.ibm.pc.hardware/*", "type" => 1, "trained" => 1, "category"=>"TECH"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/comp.sys.mac.hardware/*", "type" => 1, "trained" => 1, "category"=>"TECH"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/comp.windows.x/*", "type" => 1, "trained" => 1, "category"=>"TECH"),
        array("url" => "../../library/Classifier/trainingSet/bbc/tech/*.txt", "type" => 1, "trained" => 1, "category"=>"TECH"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/alt.atheism/*", "type" => 1, "trained" => 1, "category"=>"RELIGION"),
        array("url" => "../../library/Classifier/trainingSet/20news-bydate-train/talk.religion.misc/*", "type" => 1, "trained" => 1, "category"=>"RELIGION"),
        array("url" =>"../../library/Classifier/trainingSet/20news-bydate-train/soc.religion.christian/*", "type" => 1, "trained" => 1, "category"=>"RELIGION")

    );

    //https://monkeylearn.com/text-classification/

    foreach($category_data_sets as $key => $category_file_data) {

        if($category_file_data["type"] == 1 && $category_file_data["trained"] == 1) {

            $files = glob($category_file_data["url"]);

            foreach($files as $file) {

                $content = file_get_contents($file);

                $words = $new_ai->splitSentence($content)[0];
                
                //print_r($word);
                
                //$word = preg_replace('/\s+/', '', $word);

                //$word = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $word);

                $frequency = array();

                foreach($words as $word) {

                    $word = strtolower($word);
                                 
                    if(isset($frequency[$word])) {
                        
                        $frequency[$word] += 1;
                        
                    }
                    else {
                        
                        $frequency[$word] = 1;
                        
                    }

                }
                
//              take too long to process correlated phrases
//              $rank_phrases = RakePlus::create($content,  $setting['stopword_2']);
//                        
//              Note: en_US is the default language.
//                        
//              $phrase_scores = array_filter($rank_phrases->sortByScore('desc')->scores());
//                        

                $array['document'][$category_file_data["category"]][] = $frequency;

                echo "file done </br>";

            }

        }

    }
//https://www.globalsoftwaresupport.com/naive-bayes-classifier-explained-step-step/


    $probability = $new_ai->train($array);
    //$array_phrases['label'][$label][$phrase] =n_k

    //count(array($array_phrases['phrase'])) = vocabulary 
    //count($array_phrases['label'][$label]) = n

    //print("<pre>".print_r($probability,true)."</pre>");

    $fh = fopen('../../library/AI/category_ai_test.txt', 'w');
    fwrite($fh, serialize($probability));
    fclose($fh);


}


if($process_data == 0) {

    $test_ais = unserialize(file_get_contents('../../library/AI/category_ai_test.txt'));


    //print("<pre>".print_r($test_ais,true)."</pre>");

    //$text = stripslashes($row->cotent);

    //print_r($test_ais);

    $text = 'Colin Montgomerie believes the "time has come" for officials to introduce a "tournament ball for professionals" to curb long hitting off the tee.

American Bryson DeChambeau averaged 345 yards off the tee in round one of this week\'s Charles Schwab Challenge - the 2019 PGA Tour average was 294 yards.

"To see him carrying 330 yards in the air, this is getting unreal," said Montgomerie on BBC Radio 5 live.

"Something has to be done or these classic courses cannot be used."

DeChambeau, who is joint second on 10 under par after two rounds at Colonial Country Club in Texas, has bulked out during the enforced three-month break caused by the coronavirus pandemic.

"I\ve put on about 20lb [since the lockdown] and about 45lb in the last nine months," said the 26-year-old after Friday\'s second round.

"My ultimate goal is to get as strong as I can, applying some force and speed to the swing to see what it can handle."

Montgomerie admitted he "could not believe" the size of DeChambeau and how far he was hitting the ball.

"He is huge," said the 56-year-old Scot, a record eight-time winner of the European Tour\'s Order of Merit.

"It\'s great to see athleticism in the game but this is a whole new game we are beginning to witness.

"On Friday, Bryson had 10 holes on which he was within 100 yards of the green for his approach. And if you include the four par threes that means there were only four holes on which Bryson was more than 100 yards away for his approach.

"The game has changed dramatically. It\'s now brute force and a sand wedge."

The R&A and the United States Golf Association (USGA), who administer the rules of golf, are evaluating options after releasing a report in February that claimed the increase in hitting distances and course yardages "is detrimental to the game\'s long-term future".

"I\'m an advocate of what Jack Nicklaus proposes - a tournament ball for professionals, that goes only 80-85% as far," Montgomerie said.

"The time has come, because we can\'t be building courses at 10,000 yards.

"We haven\'t the money or the space and there are the obvious ecological reasons. A tournament ball would be a massive step, because of that term "bifurcation" [professionals playing different rules to amateurs]. Yet haven\'t we reached that stage, now?"';

    $paragraphs = $new_ai->breakLongText($new_ai->sentenceCase($text));
    
    //print_r($paragraphs);
    
    foreach($paragraphs as $key => $sentence) {
        
        $final_score[]  = $new_ai->predict($test_ais, $sentence, 0);
        
    }
    
    foreach($final_score as $key => $label) {
        
        if(count($label['category']) == 1) {
            
            if(isset($label_score[$label['category']['0']])) {
                
                $label_score[$label['category']['0']] += 1;
                
            }
            else {
                
                $label_score[$label['category']['0']] = 1;
                
            }
            
        }
        
    }

    //print_r(array_keys($final_score, max($final_score)));

    print("<pre>".print_r(array_keys($label_score, max($label_score)),true)."</pre>");



}


?>