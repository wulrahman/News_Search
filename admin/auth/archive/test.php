 
<?php

            $file = fopen('../library/Classifier/trainingSet/newsCorpora.csv', 'r');
            while (($line = fgetcsv($file)) !== FALSE) {
              //$line is an array of the csv elements
                if($line['CATEGORY'] == "b") {
                    $category_ai_train = "BUSINESS";
                }
                else if($line['CATEGORY'] == "t") {
                    $category_ai_train = "SCIENCE";
                }
                else if($line['CATEGORY'] == "e") {
                    $category_ai_train = "ENTERTAINMENT";
                }
                else if($line['CATEGORY'] == "m") {
                    $category_ai_train  = "WELLNESS";

                }
                
                $text = stripslashes($list["STORY"]);
                        
                $tmpfname = tempnam("/tmp", "FOO");

                $handle = fopen($tmpfname, "w");
                fwrite($handle, $text);
                fclose($handle);
                        
                $sats->trainAnalyzer($tmpfname, $category_ai_train, $train_times); //trainign with positive data
                                                
                // do here something

                unlink($tmpfname);

            }
            fclose($file);
            
            $file = fopen('../library/Classifier/trainingSet/News_Category_Dataset_v2.json', 'r');
            $decodedText = html_entity_decode($file);
            $myArray = json_decode($decodedText, true);
            
            foreach($myArray as $key => $row_myArray ) {

                $text = stripslashes($row_myArray["short_description"]);
                        
                $tmpfname = tempnam("/tmp", "FOO");

                $handle = fopen($tmpfname, "w");
                fwrite($handle, $text);
                fclose($handle);
                    
                $sats->trainAnalyzer($tmpfname, $row_myArray['category'], $train_times);
                
                unlink($tmpfname);

                
            }