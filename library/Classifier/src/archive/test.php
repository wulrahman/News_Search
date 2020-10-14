<?php
	require_once('test/style.php');
	require_once('SentimentAnalyzer.php');
    
        //https://laracasts.com/discuss/channels/general-discussion/php-json-decode-returns-nullhttps://laracasts.com/discuss/channels/general-discussion/php-json-decode-returns-null
    ini_set('memory_limit', '1024M'); // or you could use 1G
    ini_set('max_execution_time', 300); //300 seconds = 5 minutes

	/*	
		We instantiate the SentimentAnalyzerTest class below by passing in the SentimentAnalyzer object (class)
		found in the file: 'SentimentAnalyzer.class.php'.

		This class must be injected as a dependency into the constructor as shown below
		
	*/

	$sat = new CategoryAnalyzerTest(new CategoryAnalyzer());

	/*
     https://www.kaggle.com/rmisra/news-category-dataset
		Training The Sentiment Analysis Algorithm with words found in the trainingSet directory

		The File 'data.neg' contains a list of sentences that's been marked 'Negative'.
		We use the words in this file to train the algorithm on how a negative sentence/sentiment might
		be structured.

		Likewise, the file 'data.pos' contains a list of 'Positive' sentences and the words are also
		used to train the algorithm on how to score a sentence or document as 'Positive'.

		The trainAnalyzer method below accepts three parameters:
			* param 1: The Location of the file where the training data are located
			* param 2: Used to describe the 'type' of file [param 1] is; used to indicate
					   whether the supplied file contians positive words or not
			* param 3: Enter a less than or equal to 0 here if you want all lines in the
					   file to be used as a training set. Enter any other number if you want to
					   use exactly those number of lines to train the algorithm
https://www.freeformatter.com/batch-formatter.html
     https://medium.com/@dataturks/rare-text-classification-open-datasets-9d340c8c508e
     https://homepages.inf.ed.ac.uk/rbf/IAPR/researchers/MLPAGES/mldat.htm
     http://mlg.ucd.ie/datasets/bbc.html
     https://skymind.ai/wiki/open-datasets
     https://www.data.gov
     http://mlr.cs.umass.edu/ml/datasets.html
     https://webscope.sandbox.yahoo.com/catalog.php?datatype=r&did=75
     https://www.datasetlist.com
	*/
    
    $train_system = 0;
    
    global $Categorys;
    
    if($train_system == 1) {
        
        $files = glob("../trainingSet/bbc/entertainment/*.txt");

        foreach($files as $file) {
            $content = file_get_contents($file);
            $sat->trainAnalyzer(  $file,"BUSINESS", 500000); //training with negative data
        }

        $files = glob("../trainingSet/bbc/politics/*.txt");

        foreach($files as $file) {
            $content = file_get_contents($file);
            $sat->trainAnalyzer(  $file,"POLITICS", 500000); //training with negative data
        }
        
        $files = glob("../trainingSet/bbc/entertainment/*.txt");

        foreach($files as $file) {
            $content = file_get_contents($file);
            $sat->trainAnalyzer(  $file,"ENTERTAINMENT", 500000); //training with negative data
        }
        
        $files = glob("../trainingSet/bbc/sport/*.txt");

        foreach($files as $file) {
            $content = file_get_contents($file);
            $sat->trainAnalyzer(  $file,"SPORTS", 500000); //training with negative data
        }

        $files = glob("../trainingSet/bbc/tech/*.txt");

        foreach($files as $file) {
            $content = file_get_contents($file);
            $sat->trainAnalyzer(  $file,"TECH", 500000); //training with negative data
        }
        $fh = fopen('logdata.txt', 'w');
        fwrite($fh, serialize($Categorys));
        fclose($fh);
               
        
    }
    else {
        $Categorys = unserialize(file_get_contents('logdata.txt'));
    }


	/*
		The analyzeSentence method accepts as a sentence as parameter and score it as a positive, 
		negative or neutral sentiment. it returns an array that looks like this:

		array
		(
			'sentiment' => '[the sentiment value returned]',
			'accuracy' => array
							(
								'positivity'=> 'A floating point number showing us the probability of the sentence being positive',
								'negativity' => 'A floating point number showing us the probability of the sentence being negative',
							),
		)

		An example is shown below:
	*/

		$sentence1 = "Donald Trump Jr. and the Fox News host Laura Ingraham on Tuesday evening sought to characterize a major defeat for Republicans in the Kentucky governor's race as unrelated to President Donald Trump, even though the president had campaigned hard for the losing the candidate. As of early Wednesday morning, the state's Democratic attorney general, Andy Beshear, appeared to have defeated incumbent Gov.";
		$sentence2 = "Hmmm … perhaps Musk wants us to walk away mumbling to ourselves, “I’ve seen things you people wouldn’t believe.” Musk has talked about producing an all-electric pickup truck for years now. In December, Musk resurrected the idea saying that Tesla might have a prototype to unveil in 2019. In December, Musk resurrected the idea saying that Tesla might have a prototype to unveil in 2019.";

		$sentimentAnalysisOfSentence1 = $sat->analyzeSentence($sentence1);

		$resultofAnalyzingSentence1 = $sentimentAnalysisOfSentence1['sentiment'];
		$probabilityofSentence1BeingPositive = $sentimentAnalysisOfSentence1['accuracy']['positivity'];
		$probabilityofSentence1BeingNegative = $sentimentAnalysisOfSentence1['accuracy']['negativity'];

		$sentimentAnalysisOfSentence2 = $sat->analyzeSentence($sentence2);

		$resultofAnalyzingSentence2 = $sentimentAnalysisOfSentence2['sentiment'];
		$probabilityofSentence2BeingPositive = $sentimentAnalysisOfSentence2['accuracy']['positivity'];
		$probabilityofSentence2BeingNegative = $sentimentAnalysisOfSentence2['accuracy']['negativity'];



		


	/*
		The AnalyzeDocument method accepts the path to a text file as parameter.
		It analyzes the file and scores it as either a positive or a negative sentiment. It also
		returns an array with the same keys as the analyzeSentence method.

		An example is demonstrated below

	*/

		

		require_once('test/presentation.php');
?>
