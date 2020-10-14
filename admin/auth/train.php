<?php


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
        