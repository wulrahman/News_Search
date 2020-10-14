<?php

//https://php-ml.readthedocs.io/en/latest/machine-learning/classification/naive-bayes/#naivebayes-classifier
//https://stackoverflow.com/questions/3575626/php-implementation-of-bayes-classificator-assign-topics-to-texts
//https://stackoverflow.com/questions/3575626/php-implementation-of-bayes-classificator-assign-topics-to-texts
        
    class CategoryAnalyzer
    {
        
        protected $arrcategorys = array(),
                  $cntWord = 0,
                  $cntSentence = 0,
                  $arrBayesDifference;

        public function __construct()
        {
            global $Categorys, $setting;
            $this->arrTypes = $setting["category_array"];
            $this->arrSentenceType =array_fill(0, count($this->arrTypes), array_flip($this->arrTypes));
            $this->arrWordType =array_fill(0, count($this->arrTypes), array_flip($this->arrTypes));

            $this->aarrBayesDistribution =array_fill(0.5, count($this->arrTypes), array_flip($this->arrTypes));

            //print_r($this->arrWordType);

            //$this->arrTypes = $category_array;
            $this->arrBayesDifference = range(-1.0, 1.5, 0.1);
            //array_fill();
        }

        private function splitSentence($words)
        {
            preg_match_all('/\w+/', $words, $matches);
            return $matches;
        }

        public function insertTestData($testDataLocation, $testDataType, $testDataAmount = 0)
        {
            global $Categorys;
            if(isset($Categorys)) {
                $this->arrcategorys = $Categorys['mapcategorys'];
                $this->cntSentence = $Categorys['Sentencecount'];
                $this->arrSentenceType = $Categorys['SentenceType'];
                $this->cntWord = $Categorys['Wordcount'];
                $this->arrWordType = $Categorys['WordType'];
                
            }
            
            if (!in_array($testDataType, $this->arrTypes)) {
                
                throw new \Exception('Invalid category Type Encountered: A category Can Only Be Negative or Positive');
                return false;
            }
            
            $amountTracker = 0;
            
            $testData = fopen($testDataLocation, "r");
            
            while ($testDatum = fgets($testData)) {

                if ($amountTracker > $testDataAmount && $testDataAmount > 0) {
                    
                    break;
                }
                else {
                    
                    $amountTracker++;
                    $this->cntSentence += 1;
                    $this->arrSentenceType[$testDataType] += 1;
                    $words = self::splitSentence($testDatum)[0];
                    
                    foreach ($words as $word) {
                        
                        $this->arrWordType[$testDataType] += 1;
                        $this->cntWord += 1;
                        
                        if (!isset($this->arrcategorys[$word][$testDataType])) {
                            
                            $this->arrcategorys[$word][$testDataType] = 1;
                            
                        }
                        else {
                        
                            $this->arrcategorys[$word][$testDataType] += 1;
                            
                        }
                    }
                    
                }
                
            }
            
            $Categorys['mapcategorys'] = $this->arrcategorys;
            $Categorys['Sentencecount'] = $this->cntSentence;
            $Categorys['SentenceType'] = $this->arrSentenceType;
            $Categorys['Wordcount'] = $this->cntWord;
            $Categorys['WordType'] = $this->arrWordType;
            return true;
        }

        public function analyzeSentence($sentence)
        {
            global $Categorys;
            if(isset($Categorys)) {
                $this->arrcategorys = $Categorys['mapcategorys'];
                $this->cntSentence = $Categorys['Sentencecount'];
                $this->arrSentenceType = $Categorys['SentenceType'];
                $this->cntWord = $Categorys['Wordcount'];
                $this->arrWordType = $Categorys['WordType'];
            }
            
            foreach ($this->arrTypes as $type) {
                $this->arrBayesDistribution[$type] = $this->arrSentenceType[$type] / $this->cntSentence;
            }
            
            $categoryScores = $this->arrTypes; 
            
            $words = self::splitSentence($sentence)[0];
            
            foreach ($this->arrTypes as $type) {
                
                $categoryScores[$type] = 1;
                
                foreach($words as $word) {
                    
                    if (!isset($this->arrcategorys[$word][$type])) {
                        
                        $tracker = 0;
                        
                    }
                    else {
                        
                        $tracker = $this->arrcategorys[$word][$type];
                        
                    }
                    
                    $categoryScores[$type] *= ($tracker + 1) / ($this->arrWordType[$type] + $this->cntWord);
                    
                }
                
                $categoryScores[$type] *= $this->arrBayesDistribution[$type];
                
            }
            
            arsort($categoryScores);
            
            //print_r($categoryScores);
            //https://www.eecs.qmul.ac.uk/~norman/BBNs/Bayesian_approach_to_probability.htm
            
            $array_sum = array_sum(array_values($categoryScores));
            
            $bayesDifference = $categoryScores[key($categoryScores)] / $array_sum;
            
            foreach($categoryScores as $key => $categoryScore) {
                
                $return_array['accuracy'][$key] = $categoryScores[$key] / ($array_sum);
                
                if(is_nan($return_array['accuracy'][$key])) {
                    $return_array['accuracy'][$key] = 0;
                    
                }
                
            }
                   
            if (in_array(round($bayesDifference, 1), $this->arrBayesDifference)) {
                
                $category = 'UNKNOWN';
                
            }
            else {
                
                $category = key($categoryScores);
                
            }
            
            
            $return_array['category'] = $category;
            return $return_array;
            
        }
        
        public function analyzeSentence_return($sentence)
        {
            global $Categorys;
            if(isset($Categorys)) {
                $this->arrcategorys = $Categorys['mapcategorys'];
                $this->cntSentence = $Categorys['Sentencecount'];
                $this->arrSentenceType = $Categorys['SentenceType'];
                $this->cntWord = $Categorys['Wordcount'];
                $this->arrWordType = $Categorys['WordType'];
            }
            
            foreach ($this->arrTypes as $type) {
                $this->arrBayesDistribution[$type] = $this->arrSentenceType[$type] / $this->cntSentence;
            }
            
            $categoryScores = $this->arrTypes;
            
            $words = self::splitSentence($sentence)[0];
            
            foreach ($this->arrTypes as $type) {
                
                $categoryScores[$type] = 1;
                
                foreach($words as $word) {
                    
                    if (!isset($this->arrcategorys[$word][$type])) {
                        
                        $tracker = 0;
                        
                    }
                    else {
                        
                        $tracker = $this->arrcategorys[$word][$type];
                        
                    }
                    
                    $categoryScores[$type] *= ($tracker + 1) / ($this->arrWordType[$type] + $this->cntWord);
                    
                }
                
                $categoryScores[$type] *= $this->arrBayesDistribution[$type];
                
            }
            
            arsort($categoryScores);
            
            //print_r($categoryScores);
            //https://www.eecs.qmul.ac.uk/~norman/BBNs/Bayesian_approach_to_probability.htm
            
            $array_sum = array_sum(array_values($categoryScores));
                        
            foreach($categoryScores as $key => $categoryScore) {
                
                $return_array['accuracy'][$key] = $categoryScores[$key] / ($array_sum);
                
                if(is_nan($return_array['accuracy'][$key])) {
                    $return_array['accuracy'][$key] = 0;
                    
                }
                
            }
            
            //print_r($return_array['accuracy']);

            
            return $return_array;
            
        }

        public function analyzeDocument($documentLocation)
        {
            global $Categorys, $setting;
            if(isset($Categorys)) {
                $this->arrcategorys = $Categorys['mapcategorys'];
                $this->cntSentence = $Categorys['Sentencecount'];
                $this->arrSentenceType = $Categorys['SentenceType'];
                $this->cntWord = $Categorys['Wordcount'];
                $this->arrWordType = $Categorys['WordType'];
            }
            $documentHandle = fopen($documentLocation, 'r');
            
            while ($sentence = fgets($documentHandle)) {
                
                $category_scores = self::analyzeSentence_return($sentence);
                
                //print_r($category_scores );
                
                foreach($category_scores['accuracy'] as $key => $score) {
                    
                    if(!$categoryScores[$key]) {
                        $categoryScores[$key] = 0;
                    }
                    
                    if(in_array($key, $setting["category_array"])) {
                    
                        $categoryScores[$key] += $score;
                    
                    }
                    
                }
               
            }
            
            arsort($categoryScores);
            
            $array_sum=array_sum(array_values($categoryScores));
            
            foreach($categoryScores as $key => $categoryScore) {
                
                $return_array['accuracy'][$key] = $categoryScores[$key] / ($array_sum);
                
                if(is_nan($return_array['accuracy'][$key])) {
                    $return_array['accuracy'][$key] = 0;
                    
                }
                
            }
            
            //print_r($categoryScores);
                   
            $return_array['category'] = key($categoryScores);
            return $return_array;
        }
    }

	class CategoryAnalyzerTest
	{
		protected $CategoryAnalyzer;
		public function __construct(CategoryAnalyzer $CategoryAnalyzer)
		{
			$this->CategoryAnalyzer = $CategoryAnalyzer;
		}

		public function trainAnalyzer($testDataLocation, $testDataType, $testDataAmount)
		{
			return $this->CategoryAnalyzer->insertTestData($testDataLocation, $testDataType, $testDataAmount);
		}

		public function analyzeSentence($sentence)
		{
			return $this->CategoryAnalyzer->analyzeSentence($sentence);
		}

		public function analyzeDocument($document)
		{
			return $this->CategoryAnalyzer->analyzeDocument($document);
		}
	}
?>
