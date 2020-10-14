 
<?php

//https://github.com/awesomedata/awesome-public-datasets#agriculture
//https://datasetsearch.research.google.com/search?query=news&docid=pVEhRValD0vJaC7PAAAAAA%3D%3D
//https://www.statista.com/statistics/264488/important-news-categories-provided-by-local-news-apps-in-the-us
//https://towardsdatascience.com/top-sources-for-machine-learning-datasets-bb6d0dc3378b

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

//http://www.readabilityformulas.com/free-readability-formula-tests.php

$Neural_Net = new Neural_Net($setting['learning_rate'], $setting['activation_fun']);

$ai_data['input'] = [
    [0, 0],
    [0, 1],
    [1, 0],
    [1, 1]

];

$ai_data['output'] = [
    [0],
    [1],
    [1],
    [0]

];

global $input_neurons, $output_neurons;

$input_neurons = count($ai_data['input'][0]);

$output_neurons = count($ai_data['output'][0]);

foreach($ai_data['input'] as $index => $input) {
    
    $inputs[$index] = $Neural_Net->arrayTranspose($input);
    $outputs[$index] = $Neural_Net->arrayTranspose($ai_data['output'][$index]);
    
}

$sum_slop = null;

$setting['epochs'] = 100;

$weights_matrix = $Neural_Net->calculate_inital_weights($input_neurons, $output_neurons);

for ($i=0; $i<$setting['epochs']; $i++) {

    foreach($inputs as $key => $input) {
       
        $forward_response = $Neural_Net->forward($input, $weights_matrix, $outputs[$key]);

        $forward_output[] = $forward_response['output_layour'];

        $gradient_dencent = $Neural_Net->gradientdecent($forward_response["hidden_layours"], $forward_response['output_layour'], $input, $outputs[$key], $forward_response['weights']);

        $sum_slop[] = $gradient_dencent['slops'];

        $differential_response = $Neural_Net->backPropagation($forward_response["hidden_layours"], $forward_response['output_layour'], $input, $outputs[$key], $forward_response['weights'], $gradient_dencent);

        $weights_matrix = $differential_response["weights"];

    }

}

$forward_response = $Neural_Net->forward($ai_data['input'][0], $weights_matrix);
//
//

print("<pre>".print_r($forward_response ,true)."</pre>");

//https://www.youtube.com/watch?v=EGKeC2S44Rs
?>
