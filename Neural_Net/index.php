 
<?php

error_reporting(0);

require_once('Neural_Net.php');

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

$setting['learning_rate'] = 0.01;

$setting['activation_fun'] ="relu";

$setting['hidden_layer_neurons'] = 2;

$setting['hidden_layer'] = 3;

$Neural_Net = new Neural_Net($setting['learning_rate'], $setting['activation_fun'], $setting['hidden_layer_neurons'], $setting['hidden_layer']);

$ai_data['input'] = [
    [0, 0],
    [0, 1],
    [1, 0],
    [1, 1]

];

$ai_data['output'] = [
    [0, 1],
    [1, 0],
    [1 ,1],
    [0, 0]

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

$bias_matrix = $Neural_Net->calculate_inital_bias();

for ($i=0; $i<$setting['epochs']; $i++) {

    foreach($inputs as $key => $input) {
       
        $forward_response = $Neural_Net->forward($input, $weights_matrix, $bias_matrix);
            
        $forward_output[] = $forward_response['output_layour'];

        $gradient_dencent = $Neural_Net->gradientdecent($forward_response, $input, $outputs[$key], $weights_matrix, $bias_matrix);
    
        $differential_response = $Neural_Net->backPropagation($forward_response, $weights_matrix, $bias_matrix, $gradient_dencent, $error_array);

        $weights_matrix = $differential_response["weights"];

        $weights_matrix = $differential_response["weights"];

        $error_array = $gradient_dencent["error_array"];


    }

}

$forward_response = $Neural_Net->forward($ai_data['input'][0], $weights_matrix, $bias_matrix);
//
//

print("<pre>".print_r($forward_response ,true)."</pre>");

//https://www.youtube.com/watch?v=EGKeC2S44Rs
?>
