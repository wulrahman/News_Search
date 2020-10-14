<?php

error_reporting(0);

session_start();
    
ini_set('memory_limit', '1024M'); // or you could use 1G

//error_reporting(E_ALL);

global $setting, $page_content, $html_to_array;

$setting = array();
    
$page_content = array(0 => 0);

$setting["category_array"] = array ("POLITICS", "WELLNESS", "ENTERTAINMENT", "TRAVEL",
                        "STYLE & BEAUTY","PARENTING","HEALTHY LIVING","QUEER VOICES",
                        "FOOD & DRINK","BUSINESS","COMEDY","SPORTS","BLACK VOICES",
                        "HOME & LIVING","PARENTS","THE WORLDPOST","WEDDINGS","WOMEN",
                        "IMPACT","DIVORCE","CRIME","MEDIA","WEIRD NEWS","GREEN",
                        "WORLDPOST","RELIGION","STYLE","SCIENCE","WORLD NEWS","TASTE",
                        "TECH","MONEY", "ARTS","FIFTY","GOOD NEWS","ARTS & CULTURE",
                        "ENVIRONMENT","COLLEGE","LATINO VOICES", "CULTURE & ARTS",
                        "EDUCATION");

$setting["training_set"] = 20;  
    
$setting['suffix_min_length'] = 1;

$setting['suffix_max_length'] = 6;

// tanh     : 30000   0.01    3   -1
// sigmoid  : 30000   0.01    3   -1
// relu     : 3000    0.01    3   0

// choose the tot number of epochs
$setting['epochs'] = 100;

// choose the learning rate
$setting['learning_rate'] = 0.0001;

// numbers of hidden neurons of the first (and only one) layer
$setting['hidden_layer'] = 4;

$setting['hidden_layer_neurons'] = 4;

// activation functions: relu , tanh , sigmoid
$setting['activation_fun'] = "sigmoid";
        

$setting['compare_colour'] = "#ffffff";

// Development or published

$setting["sandbox"] = 1;

if($setting["sandbox"] == 1) {

	$setting["domain"] = 'localhost';

	$setting["url"] = "http://".$setting["domain"]."";
    
    	$setting["games_url"] = "//".$setting["domain"]."/games";

}
else {

	$setting["domain"] = "cragglist.com";

	$setting["url"]="https://www.".$setting["domain"];
    
    $setting["games_url"] = "//games.".$setting["domain"];

}

if($setting["sandbox"] == 1) {

	$setting["Lid"] = mysqli_connect('localhost', 'root', 'root', 'cragglist_com');


}
else {

	$setting["Lid"] = mysqli_connect('cragglist.com.mysql', 'cragglist_com', 'LK2ZUZ7g', 'cragglist_com');
}



// Html to array

$html_to_array = new DOMDocument();

// Api keys

//https://monkeylearn.com/text-classification/
//https://www.slideshare.net/GlennDeBacker/text-classificationphpv4

$setting["faroo_key"] = "s@g2KnPVBuu4DbmBWG3rbgO6JMs_";

$setting["webhose_key"] = "eca76abe-7138-435c-9bdf-beb12082d74c";


$setting['bighugelabs_api'] = "a611429c2f07a85ae9f35902a7a8b6d1";

$setting['newsapi_key'] = "70075de2712748d987adac44e5d89802";

// Stop words

//https://gist.github.com/sebleier/554280

$setting['sentiment_data_set'] = array(
    array("url" => "../library/sentiment-analysis/trainingSet/books/negative.review", "type" => 2, "trained" => 1, "sentiment"=>"negative"), 
    array("url" => "../library/sentiment-analysis/trainingSet/books/positive.review", "type" => 2, "trained" => 1, "sentiment"=>"positive"), 
    array("url" => "../library/sentiment-analysis/trainingSet/dvd/negative.review", "type" => 2, "trained" => 1, "sentiment"=>"negative"), array("url" => "../library/sentiment-analysis/trainingSet/dvd/positive.review", "type" => 2, "trained" => 1, "sentiment"=>"positive"), array("url" => "../library/sentiment-analysis/trainingSet/electronics/negative.review", "type" => 2, "trained" => 1, "sentiment"=>"negative"),
    array("url" => "../library/sentiment-analysis/trainingSet/electronics/positive.review", "type" => 2, "trained" => 1, "sentiment"=>"positive"),

    array("url" => "../library/sentiment-analysis/trainingSet/kitchen/negative.review", "type" => 2, "trained" => 1, "sentiment"=>"negative"),
    array("url" => "../library/sentiment-analysis/trainingSet/kitchen/positive.review", "type" => 2, "trained" => 1, "sentiment"=>"positive"), array("url" => "../library/sentiment-analysis/trainingSet/negative-words.txt", "type" => 2, "trained" => 1, "sentiment"=>"negative"),
    array("url" => "../library/sentiment-analysis/trainingSet/positive-words.txt", "type" => 2, "trained" => 1, "sentiment"=>"positive"),
    array("url" => "../library/sentiment-analysis/trainingSet/negative-articles.txt", "type" => 2, "trained" => 1, "sentiment"=>"negative"),
    array("url" => "../library/sentiment-analysis/trainingSet/positive-articles.txt", "type" => 2, "trained" => 1, "sentiment"=>"positive"),
    array("url" => "../library/sentiment-analysis/trainingSet/data.neg", "type" => 2, "trained" => 1, "sentiment"=>"positive"),
    array("url" => "../library/sentiment-analysis/trainingSet/data.pos", "type" => 2, "trained" => 1, "sentiment"=>"negative"),
    array("url" => "../library/sentiment-analysis/trainingSet/positive/*.txt", "type" => 1, "trained" => 1, "sentiment"=>"positive"), 
    array("url" => "../library/sentiment-analysis/trainingSet/negative/*.txt", "type" => 1, "trained" => 1, "sentiment"=>"negative")
);

$setting['category_data_sets'] = array(
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

$setting['stopword'] = array("a" , "able" , "about" , "above" , "according" , "accordingly" , "across" , "actually" , "after" , "afterwards" , "again" , "against" , "ain't" , "all" , "allow" , "allows" , "almost" , "alone" , "along" , "already" , "also" , "although" , "always" , "am" , "among" , "amongst" , "an" , "and" , "another" , "any" , "anybody" , "anyhow" , "anyone" , "anything" , "anyway" , "anyways" , "anywhere" , "apart" , "appear" , "appreciate" , "appropriate" , "are" , "aren't" , "around" , "as" , "aside" , "ask" , "asking" , "associated" , "at" , "available" , "away" , "awfully" , "be" , "became" , "because" , "become" , "becomes" , "becoming" , "been" , "before" , "beforehand" , "behind" , "being" , "believe" , "below" , "beside" , "besides" , "best" , "better" , "between" , "beyond" , "both" , "brief" , "but" , "by" , "c'mon" , "c's" , "came" , "can" , "can't" , "cannot" , "cant" , "cause" , "causes" , "certain" , "certainly" , "changes" , "clearly" , "co" , "com" , "come" , "comes" , "concerning" , "consequently" , "consider" , "considering" , "contain" , "containing" , "contains" , "corresponding" , "could" , "couldn't" , "course" , "currently" , "definitely" , "described" , "despite" , "did" , "didn't" , "different" , "do" , "does" , "doesn't" , "doing" , "don't" , "done" , "down" , "downwards" , "during" , "each" , "edu" , "eg" , "eight" , "either" , "else" , "elsewhere" , "enough" , "entirely" , "especially" , "et" , "etc" , "even" , "ever" , "every" , "everybody" , "everyone" , "everything" , "everywhere" , "ex" , "exactly" , "example" , "except" , "far" , "few" , "fifth" , "first" , "five" , "followed" , "following" , "follows" , "for" , "former" , "formerly" , "forth" , "four" , "from" , "further" , "furthermore" , "get" , "gets" , "getting" , "given" , "gives" , "go" , "goes" , "going" , "gone" , "got" , "gotten" , "greetings" , "had" , "hadn't" , "happens" , "hardly" , "has" , "hasn't" , "have" , "haven't" , "having" , "he" , "he's" , "hello" , "help" , "hence" , "her" , "here" , "here's" , "hereafter" , "hereby" , "herein" , "hereupon" , "hers" , "herself" , "hi" , "him" , "himself" , "his" , "hither" , "hopefully" , "how" , "howbeit" , "however" , "i'd" , "i'll" , "i'm" , "i've" , "ie" , "if" , "ignored" , "immediate" , "in" , "inasmuch" , "inc" , "indeed" , "indicate" , "indicated" , "indicates" , "inner" , "insofar" , "instead" , "into" , "inward" , "is" , "isn't" , "it" , "it'd" , "it'll" , "it's" , "its" , "itself" , "just" , "keep" , "keeps" , "kept" , "know" , "known" , "knows" , "last" , "lately" , "later" , "latter" , "latterly" , "least" , "less" , "lest" , "let" , "let's" , "like" , "liked" , "likely" , "little" , "look" , "looking" , "looks" , "ltd" , "mainly" , "many" , "may" , "maybe" , "me" , "mean" , "meanwhile" , "merely" , "might" , "more" , "moreover" , "most" , "mostly" , "much" , "must" , "my" , "myself" , "name" , "namely" , "nd" , "near" , "nearly" , "necessary" , "need" , "needs" , "neither" , "never" , "nevertheless" , "new" , "next" , "nine" , "no" , "nobody" , "non" , "none" , "noone" , "nor" , "normally" , "not" , "nothing" , "novel" , "now" , "nowhere" , "obviously" , "of" , "off" , "often" , "oh" , "ok" , "okay" , "old" , "on" , "once" , "one" , "ones" , "only" , "onto" , "or" , "other" , "others" , "otherwise" , "ought" , "our" , "ours" , "ourselves" , "out" , "outside" , "over" , "overall" , "own" , "particular" , "particularly" , "per" , "perhaps" , "placed" , "please" , "plus" , "possible" , "presumably" , "probably" , "provides" , "que" , "quite" , "qv" , "rather" , "rd" , "re" , "really" , "reasonably" , "regarding" , "regardless" , "regards" , "relatively" , "respectively" , "right" , "said" , "same" , "saw" , "say" , "saying" , "says" , "second" , "secondly" , "see" , "seeing" , "seem" , "seemed" , "seeming" , "seems" , "seen" , "self" , "selves" , "sensible" , "sent" , "serious" , "seriously" , "seven" , "several" , "shall" , "she" , "should" , "shouldn't" , "since" , "six" , "so" , "some" , "somebody" , "somehow" , "someone" , "something" , "sometime" , "sometimes" , "somewhat" , "somewhere" , "soon" , "sorry" , "specified" , "specify" , "specifying" , "still" , "sub" , "such" , "sup" , "sure" , "t's" , "take" , "taken" , "tell" , "tends" , "th" , "than" , "thank" , "thanks" , "thanx" , "that" , "that's" , "thats" , "the" , "their" , "theirs" , "them" , "themselves" , "then" , "thence" , "there" , "there's" , "thereafter" , "thereby" , "therefore" , "therein" , "theres" , "thereupon" , "these" , "they" , "they'd" , "they'll" , "they're" , "they've" , "think" , "third" , "this" , "thorough" , "thoroughly" , "those" , "though" , "three" , "through" , "throughout" , "thru" , "thus" , "to" , "together" , "too" , "took" , "toward" , "towards" , "tried" , "tries" , "truly" , "try" , "trying" , "twice" , "two" , "un" , "under" , "unfortunately" , "unless" , "unlikely" , "until" , "unto" , "up" , "upon" , "us" , "use" , "used" , "useful" , "uses" , "using" , "usually" , "value" , "various" , "very" , "via" , "viz" , "vs" , "want" , "wants" , "was" , "wasn't" , "way" , "we" , "we'd" , "we'll" , "we're" , "we've" , "welcome" , "well" , "went" , "were" , "weren't" , "what" , "what's" , "whatever" , "when" , "whence" , "whenever" , "where" , "where's" , "whereafter" , "whereas" , "whereby" , "wherein" , "whereupon" , "wherever" , "whether" , "which" , "while" , "whither" , "who" , "who's" , "whoever" , "whole" , "whom" , "whose" , "why" , "will" , "willing" , "wish" , "with" , "within" , "without" , "won't" , "wonder" , "would" , "wouldn't" , "yes" , "yet" , "you" , "you'd" , "you'll" , "you're" , "you've" , "your" , "yours" , "yourself" , "yourselves" , "zero");


$setting['stopword_2'] = array("a", "about", "above", "after", "again", "against", "ain", "all", "am", "an", "and", "any", "are", "aren", "aren't", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "can", "couldn", "couldn't", "d", "did", "didn", "didn't", "do", "does", "doesn", "doesn't", "doing", "don", "don't", "down", "during", "each", "few", "for", "from", "further", "had", "hadn", "hadn't", "has", "hasn", "hasn't", "have", "haven", "haven't", "having", "he", "her", "here", "hers", "herself", "him", "himself", "his", "how", "i", "if", "in", "into", "is", "isn", "isn't", "it", "it's", "its", "itself", "just", "ll", "m", "ma", "me", "mightn", "mightn't", "more", "most", "mustn", "mustn't", "my", "myself", "needn", "needn't", "no", "nor", "not", "now", "o", "of", "off", "on", "once", "only", "or", "other", "our", "ours", "ourselves", "out", "over", "own", "re", "s", "same", "shan", "shan't", "she", "she's", "should", "should've", "shouldn", "shouldn't", "so", "some", "such", "t", "than", "that", "that'll", "the", "their", "theirs", "them", "themselves", "then", "there", "these", "they", "this", "those", "through", "to", "too", "under", "until", "up", "ve", "very", "was", "wasn", "wasn't", "we", "were", "weren", "weren't", "what", "when", "where", "which", "while", "who", "whom", "why", "will", "with", "won", "won't", "wouldn", "wouldn't", "y", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", "could", "he'd", "he'll", "he's", "here's", "how's", "i'd", "i'll", "i'm", "i've", "let's", "ought", "she'd", "she'll", "that's", "there's", "they'd", "they'll", "they're", "they've", "we'd", "we'll", "we're", "we've", "what's", "when's", "where's", "who's", "why's", "would", "able", "abst", "accordance", "according", "accordingly", "across", "act", "actually", "added", "adj", "affected", "affecting", "affects", "afterwards", "ah", "almost", "alone", "along", "already", "also", "although", "always", "among", "amongst", "announce", "another", "anybody", "anyhow", "anymore", "anyone", "anything", "anyway", "anyways", "anywhere", "apparently", "approximately", "arent", "arise", "around", "aside", "ask", "asking", "auth", "available", "away", "awfully", "b", "back", "became", "become", "becomes", "becoming", "beforehand", "begin", "beginning", "beginnings", "begins", "behind", "believe", "beside", "besides", "beyond", "biol", "brief", "briefly", "c", "ca", "came", "cannot", "can't", "cause", "causes", "certain", "certainly", "co", "com", "come", "comes", "contain", "containing", "contains", "couldnt", "date", "different", "done", "downwards", "due", "e", "ed", "edu", "effect", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "especially", "et", "etc", "even", "ever", "every", "everybody", "everyone", "everything", "everywhere", "ex", "except", "f", "far", "ff", "fifth", "first", "five", "fix", "followed", "following", "follows", "former", "formerly", "forth", "found", "four", "furthermore", "g", "gave", "get", "gets", "getting", "give", "given", "gives", "giving", "go", "goes", "gone", "got", "gotten", "h", "happens", "hardly", "hed", "hence", "hereafter", "hereby", "herein", "heres", "hereupon", "hes", "hi", "hid", "hither", "home", "howbeit", "however", "hundred", "id", "ie", "im", "immediate", "immediately", "importance", "important", "inc", "indeed", "index", "information", "instead", "invention", "inward", "itd", "it'll", "j", "k", "keep", "keeps", "kept", "kg", "km", "know", "known", "knows", "l", "largely", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "lets", "like", "liked", "likely", "line", "little", "'ll", "look", "looking", "looks", "ltd", "made", "mainly", "make", "makes", "many", "may", "maybe", "mean", "means", "meantime", "meanwhile", "merely", "mg", "might", "million", "miss", "ml", "moreover", "mostly", "mr", "mrs", "much", "mug", "must", "n", "na", "name", "namely", "nay", "nd", "near", "nearly", "necessarily", "necessary", "need", "needs", "neither", "never", "nevertheless", "new", "next", "nine", "ninety", "nobody", "non", "none", "nonetheless", "noone", "normally", "nos", "noted", "nothing", "nowhere", "obtain", "obtained", "obviously", "often", "oh", "ok", "okay", "old", "omitted", "one", "ones", "onto", "ord", "others", "otherwise", "outside", "overall", "owing", "p", "page", "pages", "part", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "poorly", "possible", "possibly", "potentially", "pp", "predominantly", "present", "previously", "primarily", "probably", "promptly", "proud", "provides", "put", "q", "que", "quickly", "quite", "qv", "r", "ran", "rather", "rd", "readily", "really", "recent", "recently", "ref", "refs", "regarding", "regardless", "regards", "related", "relatively", "research", "respectively", "resulted", "resulting", "results", "right", "run", "said", "saw", "say", "saying", "says", "sec", "section", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sent", "seven", "several", "shall", "shed", "shes", "show", "showed", "shown", "showns", "shows", "significant", "significantly", "similar", "similarly", "since", "six", "slightly", "somebody", "somehow", "someone", "somethan", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specifically", "specified", "specify", "specifying", "still", "stop", "strongly", "sub", "substantially", "successfully", "sufficiently", "suggest", "sup", "sure", "take", "taken", "taking", "tell", "tends", "th", "thank", "thanks", "thanx", "thats", "that've", "thence", "thereafter", "thereby", "thered", "therefore", "therein", "there'll", "thereof", "therere", "theres", "thereto", "thereupon", "there've", "theyd", "theyre", "think", "thou", "though", "thoughh", "thousand", "throug", "throughout", "thru", "thus", "til", "tip", "together", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "ts", "twice", "two", "u", "un", "unfortunately", "unless", "unlike", "unlikely", "unto", "upon", "ups", "us", "use", "used", "useful", "usefully", "usefulness", "uses", "using", "usually", "v", "value", "various", "'ve", "via", "viz", "vol", "vols", "vs", "w", "want", "wants", "wasnt", "way", "wed", "welcome", "went", "werent", "whatever", "what'll", "whats", "whence", "whenever", "whereafter", "whereas", "whereby", "wherein", "wheres", "whereupon", "wherever", "whether", "whim", "whither", "whod", "whoever", "whole", "who'll", "whomever", "whos", "whose", "widely", "willing", "wish", "within", "without", "wont", "words", "world", "wouldnt", "www", "x", "yes", "yet", "youd", "youre", "z", "zero", "a's", "ain't", "allow", "allows", "apart", "appear", "appreciate", "appropriate", "associated", "best", "better", "c'mon", "c's", "cant", "changes", "clearly", "concerning", "consequently", "consider", "considering", "corresponding", "course", "currently", "definitely", "described", "despite", "entirely", "exactly", "example", "going", "greetings", "hello", "help", "hopefully", "ignored", "inasmuch", "indicate", "indicated", "indicates", "inner", "insofar", "it'd", "keep", "keeps", "novel", "presumably", "reasonably", "second", "secondly", "sensible", "serious", "seriously", "sure", "t's", "third", "thorough", "thoroughly", "three", "well", "wonder", "a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "among", "amongst", "amoungst", "amount", "an", "and", "another", "any", "anyhow", "anyone", "anything", "anyway", "anywhere", "are", "around", "as", "at", "back", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom", "but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven", "else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own", "part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "co", "op", "research-articl", "pagecount", "cit", "ibid", "les", "le", "au", "que", "est", "pas", "vol", "el", "los", "pp", "u201d", "well-b", "http", "volumtype", "par", "0o", "0s", "3a", "3b", "3d", "6b", "6o", "a1", "a2", "a3", "a4", "ab", "ac", "ad", "ae", "af", "ag", "aj", "al", "an", "ao", "ap", "ar", "av", "aw", "ax", "ay", "az", "b1", "b2", "b3", "ba", "bc", "bd", "be", "bi", "bj", "bk", "bl", "bn", "bp", "br", "bs", "bt", "bu", "bx", "c1", "c2", "c3", "cc", "cd", "ce", "cf", "cg", "ch", "ci", "cj", "cl", "cm", "cn", "cp", "cq", "cr", "cs", "ct", "cu", "cv", "cx", "cy", "cz", "d2", "da", "dc", "dd", "de", "df", "di", "dj", "dk", "dl", "do", "dp", "dr", "ds", "dt", "du", "dx", "dy", "e2", "e3", "ea", "ec", "ed", "ee", "ef", "ei", "ej", "el", "em", "en", "eo", "ep", "eq", "er", "es", "et", "eu", "ev", "ex", "ey", "f2", "fa", "fc", "ff", "fi", "fj", "fl", "fn", "fo", "fr", "fs", "ft", "fu", "fy", "ga", "ge", "gi", "gj", "gl", "go", "gr", "gs", "gy", "h2", "h3", "hh", "hi", "hj", "ho", "hr", "hs", "hu", "hy", "i", "i2", "i3", "i4", "i6", "i7", "i8", "ia", "ib", "ic", "ie", "ig", "ih", "ii", "ij", "il", "in", "io", "ip", "iq", "ir", "iv", "ix", "iy", "iz", "jj", "jr", "js", "jt", "ju", "ke", "kg", "kj", "km", "ko", "l2", "la", "lb", "lc", "lf", "lj", "ln", "lo", "lr", "ls", "lt", "m2", "ml", "mn", "mo", "ms", "mt", "mu", "n2", "nc", "nd", "ne", "ng", "ni", "nj", "nl", "nn", "nr", "ns", "nt", "ny", "oa", "ob", "oc", "od", "of", "og", "oi", "oj", "ol", "om", "on", "oo", "oq", "or", "os", "ot", "ou", "ow", "ox", "oz", "p1", "p2", "p3", "pc", "pd", "pe", "pf", "ph", "pi", "pj", "pk", "pl", "pm", "pn", "po", "pq", "pr", "ps", "pt", "pu", "py", "qj", "qu", "r2", "ra", "rc", "rd", "rf", "rh", "ri", "rj", "rl", "rm", "rn", "ro", "rq", "rr", "rs", "rt", "ru", "rv", "ry", "s2", "sa", "sc", "sd", "se", "sf", "si", "sj", "sl", "sm", "sn", "sp", "sq", "sr", "ss", "st", "sy", "sz", "t1", "t2", "t3", "tb", "tc", "td", "te", "tf", "th", "ti", "tj", "tl", "tm", "tn", "tp", "tq", "tr", "ts", "tt", "tv", "tx", "ue", "ui", "uj", "uk", "um", "un", "uo", "ur", "ut", "va", "wa", "vd", "wi", "vj", "vo", "wo", "vq", "vt", "vu", "x1", "x2", "x3", "xf", "xi", "xj", "xk", "xl", "xn", "xo", "xs", "xt", "xv", "xx", "y2", "yj", "yl", "yr", "ys", "yt", "zi", "zz");


// Robot setting

$setting["robot"] = "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36";

$setting["bot"] = "Net Bot";

$setting["icon"] = "";


// newscustom image

$setting['min_image_resolution'] = 500 * 300;

$news_thumb["image_dir"] = "feed/";

$news_thumb["temp_dir"] = $news_thumb["image_dir"]."temp/";

$news_thumb["thumb_dir"] = $news_thumb["image_dir"]."thumb/";

$news_thumb["maxwidth"] = 300;

$news_thumb["maxheight"] = 300;


$news_thumb_large["image_dir"] = "feed/";

$news_thumb_large["temp_dir"] = $news_thumb_large["image_dir"]."temp/large/";

$news_thumb_large["thumb_dir"] = $news_thumb_large["image_dir"]."thumb/large/";

$news_thumb_large["maxwidth"] = 1000;

$news_thumb_large["maxheigh"] = 1000;


// Usercustom image

$user_thumb["image_dir"] = "auth/thumb/";

$user_thumb["temp_dir"] = $user_thumb["image_dir"]."temp/";

$user_thumb["thumb_dir"] = $user_thumb["image_dir"]."thumb/";

$user_thumb["maxwidth"] = 200;

$user_thumb["maxheight"] = 150;


// Default image

$setting["getmaxwidth"] = 1;

$setting["getmaxheight"] = 1;

$setting["image_dir"] = "auth/thumb/";

$setting["temp_dir"] = $setting["image_dir"]."temp/";

$setting["thumb_dir"] = $setting["image_dir"]."thumb/";

$setting["dir_sub"] = "";

$setting["maxwidth"] = 200;

$setting["maxheight"]  = 150;


// Alphabet

$setting["alp"] = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";


// Allowed image types

$setting["typeset"] = 'jpeg';

$setting["image_types"] = array(

	'png' => 'image/png',
	'jpe' => 'image/jpeg',
	'jpeg' => 'image/jpeg',
	'jpg' => 'image/jpeg',
	'gif' => 'image/gif',
	'bmp' => 'image/bmp',
	'ico' => 'image/vnd.microsoft.icon',
	'tiff' => 'image/tiff',
	'tif' => 'image/tiff',
	'svg' => 'image/svg+xml',
	'svgz' => 'image/svg+xml'

);


$setting["admin_url"] = $setting["url"]."/admin";

$setting["main_url"] = $setting["url"]."";


// Email information

$setting["email_1"] = "@".$setting["domain"];

$setting["no_reply"] = "no_reply".$setting["email_1"];

$setting["contactemail"] = "contact".$setting["email_1"];


$setting['entities'] = array(

	"&amp" => "&",
	"&apos" => "'",
	"&THORN;"  => "�",
	"&szlig;"  => "�",
	"&agrave;" => "�",
	"&aacute;" => "�",
	"&acirc;"  => "�",
	"&atilde;" => "�",
	"&auml;"   => "�",
	"&aring;"  => "�",
	"&aelig;"  => "�",
	"&ccedil;" => "�",
	"&egrave;" => "�",
	"&eacute;" => "�",
	"&ecirc;"  => "�",
	"&euml;"   => "�",
	"&igrave;" => "�",
	"&iacute;" => "�",
	"&icirc;"  => "�",
	"&iuml;"   => "�",
	"&eth;"    => "�",
	"&ntilde;" => "�",
	"&ograve;" => "�",
	"&oacute;" => "�",
	"&ocirc;"  => "�",
	"&otilde;" => "�",
	"&ouml;"   => "�",
	"&oslash;" => "�",
	"&ugrave;" => "�",
	"&uacute;" => "�",
	"&ucirc;"  => "�",
	"&uuml;"   => "�",
	"&yacute;" => "�",
	"&thorn;"  => "�",
	"&yuml;"   => "�",
	"&THORN;"  => "�",
	"&szlig;"  => "�",
	"&Agrave;" => "�",
	"&Aacute;" => "�",
	"&Acirc;"  => "�",
	"&Atilde;" => "�",
	"&Auml;"   => "�",
	"&Aring;"  => "�",
	"&Aelig;"  => "�",
	"&Ccedil;" => "�",
	"&Egrave;" => "�",
	"&Eacute;" => "�",
	"&Ecirc;"  => "�",
	"&Euml;"   => "�",
	"&Igrave;" => "�",
	"&Iacute;" => "�",
	"&Icirc;"  => "�",
	"&Iuml;"   => "�",
	"&ETH;"    => "�",
	"&Ntilde;" => "�",
	"&Ograve;" => "�",
	"&Oacute;" => "�",
	"&Ocirc;"  => "�",
	"&Otilde;" => "�",
	"&Ouml;"   => "�",
	"&Oslash;" => "�",
	"&Ugrave;" => "�",
	"&Uacute;" => "�",
	"&Ucirc;"  => "�",
	"&Uuml;"   => "�",
	"&Yacute;" => "�",
	"&Yhorn;"  => "�",
	"&Yuml;"   => "�"

	);

$setting['apache_indexes'] = array ("N=A" => 1, "N=D" => 1, "M=A" => 1, "M=D" => 1, "S=A" => 1, "S=D" => 1, "D=A" => 1, "D=D" => 1, "C=N;O=A" => 1, "C=M;O=A" => 1, "C=S;O=A" => 1, "C=D;O=A" => 1, "C=N;O=D" => 1, "C=M;O=D" => 1, "C=S;O=D" => 1, "C=D;O=D" => 1);

$setting['english_suffix'] =  array(

	0 => 'acy',
	1 => 'al',
	2 => 'ance',
	3 => 'ence',
	4 => 'dom',
	5 => 'er',
	6 => 'or',
	7 => 'ism',
   	8 => 'ist',
 	9 => 'ity',
	10 => 'ty',
 	11 => 'ment',
	12 => 'ness',
	13 => 'ship',
	14 => 'sion',
	15 => 'tion',
	16 => 'ate',
	17 => 'en',
	18 => 'ify',
	19 => 'fy',
    20 => 'ize',
	21 => 'ise',
 	22 => 'able',
	23 => 'ible',
	24 => 'al',
	25 => 'esque',
 	26 => 'ful',
	27 => 'ic',
 	28 => 'ical',
 	29 => 'ious',
	30 => 'ous',
	31 => 'ish',
	32 => 'ive',
 	33 => 'less',
	34 => 'y'
);


// Adv: for Adverbs,      

// CP: Suffixes for Comparison,    

// N: for Nouns,   

// NP: for Nouns -- Groups of People,          

// PT: for Plurals or Tenses        

// V: for Verbs

$setting['english_suffix_main'] = array('able' => 'adj.', 
'al' => 'adj.',
'an' => 'np.',
'ance' => 'n.',
'ancy' => 'n.',
'ant' => 'adj.',
'ant' => 'np.',
'ar' => 'np.',
'ary' => 'adj.',
'ate' => 'v.',
'ed' => 'adj.',
'ee' => 'np.',
'en' => 'adj.',
'en' => 'v.',
'ence' => 'n.',
'ency' => 'n.',
'nt' => 'adj.',
'ent' => 'np.',
'er' => 'cp.',
'er' => 'np.',
'es' => 'pl. pt.',
'es' => '3ps. pt.',
'est' => 'cp.',
'fication' => 'n.',
'ful' => 'adj.',
'fy' => 'v.',
'ify' => 'v.',
'ian' => 'np.',
'ible' => 'adj.',
'ic' => 'adj.',
'ing' => 'adj. pt.',
'ion' => 'n.',
'ish' => 'adj.',
'ism' => 'n.',
'ist' => 'np.',
'ity' => 'n.',
'ive' => 'adj.',
'ize' => 'v.',
'less' => 'adj.',
'logy' => 'n.',
'ly' => 'adv.',
'ment' => 'n.',
'ness' => 'n.',
'or' => 'np.',
'ous' => 'adj.',
's' => 'pl. pt.',
's' => '3ps. pt.',
'ship' => 'n.',
'sion' => 'n.',
'tion' => 'n.',
'y' => 'adj.');

//https://myregextester.com/index.php
//http://grammar.about.com/od/words/a/comsuffixes.htm

?>
