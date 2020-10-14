 
<?php

//https://github.com/awesomedata/awesome-public-datasets#agriculture
//https://datasetsearch.research.google.com/search?query=news&docid=pVEhRValD0vJaC7PAAAAAA%3D%3D
//https://www.statista.com/statistics/264488/important-news-categories-provided-by-local-news-apps-in-the-us
//https://towardsdatascience.com/top-sources-for-machine-learning-datasets-bb6d0dc3378b

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

//http://www.readabilityformulas.com/free-readability-formula-tests.php

//print_r($setting['user']);


if($user->login_status == 1) {

    $model_query = mysqli_query($setting['Lid'],"SELECT * FROM `user_model` WHERE `user` = '".$user->id."' AND `timestamp` >= NOW() - INTERVAL 1 SECOND");

}
else {

    $model_query = mysqli_query($setting['Lid'],"SELECT * FROM `user_model` WHERE `identifier` = '".$user->cookie_id."' AND `timestamp` >= NOW() - INTERVAL 1 SECOND");

}

//print_r($user);

print_r($setting['user']->array_identifiers);

$train_model_count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

$train_model_count ==0;
if($train_model_count == 0) {

    $article_query_clicked = mysqli_query($setting["Lid"], "(SELECT  `published`, `sentiment`, `author`, `readability`, `category`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`article` AND `identifier` IN ('".implode("','", $setting['user']->array_identifiers)."') AND `clicks`.`timestamp` >= NOW() - INTERVAL 30 DAY) AS `count` FROM `news` ORDER BY RAND()) ");

    $ai_data = array();

    while($article_row_clicked = mysqli_fetch_array($article_query_clicked)) {

        if($setting['activation_fun'] == "relu") {

            $article_bool_click = $article_row_clicked['count'] > 0?$article_row_clicked['count']:0;

        }
        else if($setting['activation_fun'] == "tanh" || $setting['activation_fun'] == "sigmoid") {

            $article_bool_click = $article_row_clicked['count'] > 0?$article_row_clicked['count']:(-1);

        }

        $time = strtotime($article_row_clicked['published']);

        $ai_data['input'][] = array(intval($article_row_clicked['sentiment']), intval($article_row_clicked['author']),  intval($article_row_clicked['category']), intval($article_row_clicked['readability']));

        $ai_data['output'][] = array($article_bool_click);

    }    

    //$setting['learning_rate'] = 0.001;

    $Neural_Net = new Neural_Net($setting['learning_rate'], $setting['activation_fun']);
    
    global $input_neurons, $output_neurons;
    
    $input_neurons = count($ai_data['input'][0]);

    $output_neurons = count($ai_data['output'][0]);
    
    foreach($ai_data['input'] as $index => $input) {
        
        $inputs[$index] = $Neural_Net->arrayTranspose($input);
        $outputs[$index] = $Neural_Net->arrayTranspose($ai_data['output'][$index]);
        
    }
    
    if($user->model !== null) {

        $weights_matrix = $user->model['weights'];

        $bias_matrix = $user->model['bias'];
    }
    else {

        $weights_matrix = $Neural_Net->calculate_inital_weights($input_neurons, $output_neurons);

        $bias_matrix = $Neural_Net->calculate_inital_bias();

    }

    for ($i=0; $i<$setting['epochs']; $i++) {
    
        foreach($inputs as $key => $input) {
           
            $forward_response = $Neural_Net->forward($input, $weights_matrix, $bias_matrix);
    
            $forward_output[] = $forward_response['output_layour'];
    
            $gradient_dencent = $Neural_Net->gradientdecent($forward_response, $input, $outputs[$key], $weights_matrix, $bias_matrix);
        
            $differential_response = $Neural_Net->backPropagation($forward_response, $weights_matrix, $bias_matrix, $gradient_dencent);
    
            $weights_matrix = $differential_response["weights"];

            $bias_matrix = $differential_response["bias"];

    
        }
    
    }

    $model['weights'] = $weights_matrix;

    $model['bias'] = $bias_matrix;
    
    $model = mysqli(json_encode(serialize($model)));

    if($user->login_status == 1) {

        mysqli_query($setting["Lid"], "INSERT INTO `user_model`(`user`, `model`, `timestamp`) VALUES ('".$user->id."','".$model."',now())");
    }
    else {

        mysqli_query($setting["Lid"], "INSERT INTO `user_model`(`identifier`, `model`, `timestamp`) VALUES ('".$user->cookie_id."','".$model."',now())");

    }

}
//
//

print("<pre>".print_r($forward_response,true)."</pre>");

//https://www.youtube.com/watch?v=EGKeC2S44Rs
?>
