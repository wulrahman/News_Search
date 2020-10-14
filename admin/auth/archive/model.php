 
<?php

//https://github.com/awesomedata/awesome-public-datasets#agriculture
//https://datasetsearch.research.google.com/search?query=news&docid=pVEhRValD0vJaC7PAAAAAA%3D%3D
//https://www.statista.com/statistics/264488/important-news-categories-provided-by-local-news-apps-in-the-us
//https://towardsdatascience.com/top-sources-for-machine-learning-datasets-bb6d0dc3378b

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

//http://www.readabilityformulas.com/free-readability-formula-tests.php

require_once('../library/brainy-master/Brainy.php');

// tanh     : 30000   0.01    3   -1
// sigmoid  : 30000   0.01    3   -1
// relu     : 3000    0.01    3   0

// choose the tot number of epochs
$epochs = 300;

// choose the learning rate
$learning_rate = 0.01;

// numbers of hidden neurons of the first (and only one) layer
$hidden_layer_neurons = 4;

// activation functions: relu , tanh , sigmoid
$activation_fun = 'relu';

if($user->login_status == 1) {

    $identifier_query = mysqli_query($setting["Lid"], "SELECT `identifier` FROM `user_geo_location` WHERE `user` = '".$user->id."' AND `timestamp` >= NOW() - INTERVAL 30 DAY");

    while($identifier = mysqli_fetch_array($identifier_query)) {

        $array_identifiers[] = $identifier['identifier'];

    }

    $model_query = mysqli_query($setting['Lid'],"SELECT * FROM `user_model` WHERE `user` = '".$user->id."' AND `timestamp` >= NOW() - INTERVAL 30 MINUTE");

}
else {

    $model_query = mysqli_query($setting['Lid'],"SELECT * FROM `user_model` WHERE `identifier` = '".$user->cookie_id."' AND `timestamp` >= NOW() - INTERVAL 30 MINUTE");

    $array_identifiers[] = $user->cookie_id;


}

$train_model_count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

if($train_model_count == 0) {

    $article_query_clicked = mysqli_query($setting["Lid"], "(SELECT  `sentiment`, `author`, `readability`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`category`  AND `identifier` IN ('".implode("','", $array_identifiers)."') AND `clicks`.`timestamp` >= NOW() - INTERVAL 30 DAY) AS `count` FROM `news` ORDER BY `count` DESC LIMIT 100)");


    $article_query_noclicked = mysqli_query($setting["Lid"], "(SELECT  `sentiment`, `author`, `readability`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`category`  AND `identifier` IN ('".implode("','", $array_identifiers)."') AND `clicks`.`timestamp` >= NOW() - INTERVAL 30 DAY) AS `count` FROM `news` ORDER BY `count` ASC LIMIT 100)");

    while($article_row_clicked = mysqli_fetch_array($article_query_clicked)) {

        if($activation_fun == "relu") {

            $article_bool_click = $article_row_clicked['count'] > 0?1:0;

        }
        else if($activation_fun == "tanh" || $activation_fun == "sigmoid") {

            $article_bool_click = $article_row_clicked['count'] > 0?1:(-1);

        }

        $ai_data['input'][] = array(intval($article_row_clicked['sentiment']), intval($article_row_clicked['author']), intval($article_row_clicked['count']), intval($article_row_clicked['readability']));

        $ai_data['output'][] = array($article_bool_click);

    }    

    while($article_row_notclicked = mysqli_fetch_array($article_query_noclicked)) {

        if($activation_fun == "relu") {

            $article_bool_click = $article_row_notclicked['count'] > 0?1:0;

        }
        else if($activation_fun == "tanh" || $activation_fun == "sigmoid") {

            $article_bool_click = $article_row_notclicked['count'] > 0?1:(-1);

        }

        $ai_data['input'][] = array(intval($article_row_notclicked['sentiment']), intval($article_row_notclicked['author']), intval($article_row_notclicked['count']), intval($article_row_notclicked['readability']));

        $ai_data['output'][] = array($article_bool_click);

    }

    $brain = new Brainy($learning_rate , $activation_fun);

    // this is the input XOR matrix
    // remember to replace the zeros with -1 when you use TanH or Sigmoid
    $xor_in = array_splice($ai_data['input'], 0, 10000);

    // this is the output of the XOR
    // remember to replace the zeros with -1 when you use TanH or Sigmoid
    $xor_out = array_splice($ai_data['output'], 0, 10000);

    $input_neurons = count($xor_in[0]);
    $output_neurons = count($xor_out[0]);

    // getting the W1 weights random matrix (layer between input and the hidden layer) with size 2 x $hidden_layer_neurons
    $w1 = $brain->getRandMatrix($input_neurons, $hidden_layer_neurons);

    // getting the W2 weights random vector (layer between hidden layer and output) with size $hidden_layer_neurons x 1
    $w2 = $brain->getRandMatrix($hidden_layer_neurons , $output_neurons);

    // getting the B1 bies random vector with size $hidden_layer_neurons
    $b1 = $brain->getRandMatrix($hidden_layer_neurons , 1);

    // getting the B2 bies random vector. The size is 1x1 because there is only one output neuron
    $b2 =  $brain->getRandMatrix($output_neurons, 1);

    // preparing the arrays
    foreach($xor_in as $index => $input) {
        $xor_in[$index] = $brain->arrayTranspose($input);
        $xor_out[$index] = $brain->arrayTranspose($xor_out[$index]);
    }

    for ($i=0; $i<$epochs; $i++) {

        foreach($xor_in as $index => $input) {
            // forward the input and get the output
            $forward_response = $brain->forward($input, $w1, $b1, $w2, $b2);

            // backprotagating the error and finding the new weights and biases
            $new_setts = $brain->backPropagation($forward_response, $input, $xor_out[$index], $w1, $w2, $b1, $b2);
            $w1 = $new_setts['w1'];
            $w2 = $new_setts['w2'];
            $b1 = $new_setts['b1'];
            $b2 = $new_setts['b2'];

        }

    }

    $model['w1'] = $w1;
    $model['w2'] = $w2;
    $model['b1'] = $b1;
    $model['b2'] = $b2;

    $model = mysqli(json_encode(serialize($model)));

    if($user->login_status == 1) {

        mysqli_query($setting["Lid"], "INSERT INTO `user_model`(`user`, `model`, `timestamp`) VALUES ('".$user->id."','".$model."',now())");
    }
    else {

        mysqli_query($setting["Lid"], "INSERT INTO `user_model`(`identifier`, `model`, `timestamp`) VALUES ('".$user->cookie_id."','".$model."',now())");

    }


}

//https://www.youtube.com/watch?v=EGKeC2S44Rs
?>
