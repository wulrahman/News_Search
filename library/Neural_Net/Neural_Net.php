<?php

include_once "Matrix.php";

class Neural_Net extends Matrix {

    private $learning_rate = 0.01;

    private $activation_fun = 'relu';
    
    private $inital_hidden_layour_size = 10;

    private $hidden_layour = 10;

    public function __construct($learning_rate, $activation_fun, $inital_hidden_layour_size, $hidden_layour) {

        $activation_fun = strtolower($activation_fun);

        if (!is_numeric($learning_rate)) {

            throw new Exception('The learning rate is not numeric');

        }
        if (!in_array($activation_fun, ['relu', 'sigmoid', 'tanh'])) {

            throw new Exception('The allowed activation funciton are: RELU, SIGMOID, TANH');
            
        }

        $this->learning_rate = $learning_rate;

        $this->activation_fun = $activation_fun;

        $this->inital_hidden_layour_size = $inital_hidden_layour_size;

        $this->hidden_layour = $hidden_layour;

    }

        
    public function drivartive_function($predicted, $layour_input) {
            
        if ($this->activation_fun == 'tanh') {

            $output = $this->tanhDerivative($predicted);

        }
        else if ($this->activation_fun == 'sigmoid') {

            $output = $this->sigmoidDerivative($predicted);

        }
        else if ($this->activation_fun == 'relu') {

            $output = $this->reluDerivate($layour_input);

        }
        
        return $output;
        
    }
    
    public function calculate_inital_weights($input_neurons, $output_neurons) {
        
        $inital_hidden_layour_size = $this->inital_hidden_layour_size;
            
        for($i=1; $i <= $this->hidden_layour; $i++) {
            
            if($i == 1) {

                $dimentions[$i]['row'] = $input_neurons;
                
                $dimentions[$i]['column'] = $input_neurons;

                $dot_matrix[$i]['row'] = $input_neurons;

                $dot_matrix[$i]['column'] = 1;
                

            }
            else {

                $dimentions[$i]['row'] = $inital_hidden_layour_size+$i;
                
                $dimentions[$i]['column'] = $dimentions[$i-1]['row'];

                $dot_matrix[$i]['row'] = $inital_hidden_layour_size*$i;

                $dot_matrix[$i]['column'] = 1;

            }

            $weights_matrix[$i]['w'] = $this->getRandMatrix($dimentions[$i]['row'], $dimentions[$i]['column']);

        }

        $dimentions['output']['row'] = $output_neurons;
                
        $dimentions['output']['column'] = $dimentions[$i-1]['row'];

        $weights_matrix['output']['w'] = $this->getRandMatrix($dimentions['output']['row'], $dimentions['output']['column']);

        return $weights_matrix;

    }

    public function calculate_inital_bias() {
            
        for($i=1; $i <= $this->hidden_layour; $i++) {

            $bias_matrix[$i]['b'] = $this->getRandMatrix(1, 1);

        }

        $bias_matrix['output']['b'] = $this->getRandMatrix(1, 1);

        return $bias_matrix;

    }


    public function layour ($data_in, $weights, $bias) {

        $arrays['z'] = $this->matrixDotProduct($weights, $data_in);

        $arrays['z'] = $this->matrixSumValue($arrays['z'], $bias[0][0]);
        
        $arrays['a'] = $this->matrixOperation($this->activation_fun, $arrays['z']);
        
        return $arrays;
        
    }
    
    public function forward($input, $weights_matrix, $bias_matrix) {
        
        for($i=0; $i <= (intval($this->hidden_layour)-1); $i++) {
                        
            if($i == 0) {
                
                $arrays[$i] = $this->layour($input, $weights_matrix[$i+1]['w'], $bias_matrix[$i+1]['b']);
                                    
            }
            else {

                $arrays[$i] = $this->layour(end($arrays)['a'], $weights_matrix[$i+1]['w'], $bias_matrix[$i+1]['b']);

            }

        }

        $output_layour = $this->layour(end($arrays)['a'], $weights_matrix['output']['w'], $bias_matrix['output']['b']);

        return [
            'hidden_layours' => $arrays,
            'output_layour' => $output_layour,
        ];
        
      
    }
        
    public function layour_gradient ($layour_input, $layour, $layour_weight, $type, $weights_after=null, $dependent=null) {

        global $new_test_error;

        $new_error = $new_test_error;

        if($type === "output") {

            $new_error["output"]['Etotal/Ypredicted'] = (2 * $this->matrixSums($this->matrixSub($layour['a'], $dependent)))/count($layour['a']);
            
            foreach($layour_weight as $key => $node) {

                $new_error["output"]['Ypredicted/current_net'][$key] = $this->drivartive_function([$layour['a'][$key]], [$layour['z'][$key]])[0][0];

                $new_error["output"]['Etotal/net_next'][$key] = $new_error["output"]['Etotal/Ypredicted'] * $new_error["output"]['Ypredicted/current_net'][$key];

                foreach($node as $id => $weights) {

                    if($type === 0) {

                        $new_error["output"]['net_next/Weight'][$key][$id] =  $layour_input[$id][0];

                    }
                    else {

                        $new_error["output"]['net_next/Weight'][$key][$id] =  $layour_input['a'][$id][0];

                    }

                    $new_error["output"]['Etotal/Weight'][$key][$id] = $new_error["output"]['Etotal/net_next'][$key] * $new_error["output"]['net_next/Weight'][$key][$id];

                    if(array_key_exists($id, $new_test_error["output"]['Etotal/Weight'][$key])) {

                        $new_error["output"]['Etotal/Weight'][$key][$id] = ($new_error["output"]['Etotal/Weight'][$key][$id]+$new_test_error["output"]['Etotal/Weight'][$key][$id])/2;

                    }

                    $arrays['error'][$key][$id] = $new_error["output"]['Etotal/Weight'][$key][$id];

                }
    
            }

            $error_array['error'] = array_sum([$new_error["output"]['Etotal/net_next']]);

        }
        else {

            foreach($layour_weight as $key => $node) {

                $new_error[$type]['net_next/out_current'][$key] = $this->matrixTranspose($weights_after);

                $new_error[$type]['out_current/net_current'][$key] = $this->drivartive_function([$layour['a'][$key]], [$layour['z'][$key]])[0][0];

                if($type === ($this->hidden_layour-1)) {

                    $new_error[$type]['Etotal/net_next'][$key] = $this->matrixSums([$new_error["output"]['Etotal/net_next']]);

                }
                else {

                    $new_error[$type]['Etotal/net_next'][$key] = $this->matrixSums([$new_error[$type+1]['Etotal/net_next']]);
                    

                }

                foreach($node as $id => $weights) {

                    if($type === 0) {

                        $new_error[$type]['net_current/Weight'][$key][$id] =  $layour_input[$id][0];

                    }
                    else {

                        $new_error[$type]['net_current/Weight'][$key][$id] =  $layour_input['a'][$id][0];

                    }

                    $new_error[$type]['Etotal/out_current'][$key][$id] = $new_error[$type]['Etotal/net_next'][$key] * $new_error[$type]['net_next/out_current'][$key][$id][0];

                    $new_error[$type]['Etotal/net_current'][$key][$id] = $new_error[$type]['Etotal/out_current'][$key][$id] * $new_error[$type]['out_current/net_current'][$key];

                    $new_error[$type]['Etotal/Weight'][$key][$id] = $new_error[$type]['Etotal/net_current'][$key][$id] * $new_error[$type]['net_current/Weight'][$key][$id] ;
                    
                    if(array_key_exists($id, $new_test_error[$type]['Etotal/Weight'][$key])) {

                        $new_error[$type]['Etotal/Weight'][$key][$id] = ($new_error[$type]['Etotal/Weight'][$key][$id]+$new_test_error[$type]['Etotal/Weight'][$key][$id])/2;

                    }
                   
                    $arrays['error'][$key][$id] = $new_error[$type]['Etotal/Weight'][$key][$id];

                }

                $new_error[$type]['Etotal/net_next'][$key] = $this->matrixSums([$new_error[$type]['Etotal/net_current'][$key]]);

            }

            $error_array['error'] = $this->matrixSums([$new_error[$type]['Etotal/out_current']]);

        }

        $new_test_error = $new_error;

        $arrays['delta'] = $arrays['error'];

        $arrays['alpha'] = $error_array['error'];

        $arrays['error_array'] = array("delta" => $arrays['delta'], "alpha" => $arrays['alpha']);
     
        return $arrays;
    
    }
    
    public function gradientdecent($forward, $input, $ai_output, $weights_matrix, $bias_matrix) {

        $output_new = $this->layour_gradient(end($forward["hidden_layours"]), $forward['output_layour'], $weights_matrix['output']['w'],  "output", null, $ai_output);

        $sum_slop['output'] = $output_new['delta'];

        $error_array['output'] = $output_new['error_array'];
        
        krsort($forward["hidden_layours"]);

        foreach($forward["hidden_layours"] as $key => $layour) {
                
            if($key == 0) {

                $layour_input = $input;

            }
            else  {

                $layour_input = $forward["hidden_layours"][$key-1];

            }

            if($key == ($this->hidden_layour-1)) {

                $weights_after = $weights_matrix['output']['w'];

            }
            else {
                
                $weights_after = $weights_matrix[$key+2]['w'];

            }

            $arrays[$key] = $this->layour_gradient($layour_input, $layour, $weights_matrix[$key+1]['w'], $key, $weights_after);

            $sum_slop[$key] = $arrays[$key]['delta'];

            $error_array[$key] = $arrays[$key]['error_array'];

        }

        return [
            'hidden_layours' => $arrays,
            'output_layour' => $output_new,
            'slops' =>  $sum_slop,
            'error_array' =>  $error_array
        ];
        

    }

    public function new_weight ($arrays, $layour_weight, $previous_delta=null) {

        (float) $arrays['corr_mat'] = $this->matrixTimesValue($arrays['delta'], $this->learning_rate);

        (float) $arrays['w'] = $this->matrixSub($layour_weight, $arrays['corr_mat']);

        return $arrays;
    
    }

    public function new_bias ($arrays, $layour_bias, $previous_alpha=null) {

        (float) $arrays['corr_mat'] = (float) $arrays['alpha'] * (float) $this->learning_rate;
        
        (float) $arrays['b'] = array(array($layour_bias['0'] - $arrays['corr_mat']));

        return $arrays;
    
    }

    public function backPropagation($forward, $weights_matrix, $bias_matrix, $gradient_dencent, $error_array) {
    
        $new_weights['output']['w'] = $this->new_weight($gradient_dencent['output_layour'], $weights_matrix['output']['w'])['w'];

        $new_bias['output']['b'] = $this->new_bias($gradient_dencent['output_layour'], $bias_matrix['output']['b'][0])['b'];
    
        foreach($forward["hidden_layours"] as $key => $layour) {

            $new_weights[$key+1]['w'] = $this->new_weight($gradient_dencent['hidden_layours'][$key], $weights_matrix[$key+1]['w'])['w'];

            $new_bias[$key+1]['b'] = $this->new_bias($gradient_dencent['hidden_layours'][$key], $bias_matrix[$key+1]['b'][0])['b'];

        }
    
        return [
            'weights' => $new_weights,
            'bias' => $new_bias
        ];

    }

    public function sigmoidDerivative($z) {

        $z_2 = $this->matrixProductValueByValue($z,$z);

        return $this->matrixSub($z, $z_2);

    }

    public function reluDerivate($z) {

        $relu_der = [];

        foreach($z as $row_num => $row) {

            foreach($row as $col_num => $val) {

                $relu_der[$row_num][$col_num] = ($val >= 0) ? 1 : 0;

            }

        }

        return $relu_der;

    }

    public function tanhDerivative($matrix) {

        $matrix_square = $this->matrixProductValueByValue($matrix,$matrix);
        $matrix_neg = $this->matrixTimesValue($matrix_square, -1);

        return $this->matrixSumValue($matrix_neg, 1);
        
    }

}
