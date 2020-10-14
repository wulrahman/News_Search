<?php

include_once "Matrix.php";


class Neural_Net extends Matrix {

    private $learning_rate = 0.01;
    private $activation_fun = 'relu';

    //https://github.com/stephencwelch/Neural-Networks-Demystified/blob/master/Part%204%20Backpropagation.ipynb

    /**
     * Set you neural network with the learning rate anf the activation function you prefer
     *
     * @param numeric $learning_rate  choose your learning rate
     * @param string $activation_fun  choose your activation funciton: RELU or SIGMOID or TANH
     */
    public function __construct($learning_rate, $activation_fun) {

        $activation_fun = strtolower($activation_fun);

        if (!is_numeric($learning_rate)) {

            throw new Exception('The learning rate is not numeric');

        }
        if (!in_array($activation_fun, ['relu', 'sigmoid', 'tanh'])) {

            throw new Exception('The allowed activation funciton are: RELU, SIGMOID, TANH');
            
        }

        $this->learning_rate = $learning_rate;

        $this->activation_fun = $activation_fun;

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
        
        global $setting;
        
        $inital_hidden_layour_size = $setting['hidden_layer_neurons'];
            
        for($i=0; $i < $setting['hidden_layer']; $i++) {

            $dimentions[$i]['row'] = $inital_hidden_layour_size*$i;
                
            $dimentions[$i]['column'] = $dimentions[$i-1]['row'];

            $dot_matrix[$i]['row'] = $inital_hidden_layour_size*$i;

            $dot_matrix[$i]['column'] = 1;
            
            if($i == 0) {

                $dimentions[$i]['row'] = $input_neurons;
                
                $dimentions[$i]['column'] = $input_neurons;

                $dot_matrix[$i]['row'] = $input_neurons;

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
        
        global $setting;
            
        for($i=0; $i < $setting['hidden_layer']; $i++) {

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
        
        global $input_neurons, $output_neurons, $setting;
        
        for($i=0; $i <= (intval($setting['hidden_layer'])-1); $i++) {
                        
            if($i == 0) {
                
                $arrays[$i] = $this->layour($input, $weights_matrix[$i]['w'], $bias_matrix[$i]['b']);

                                    
            }
            else {

                $arrays[$i] = $this->layour(end($arrays)['a'], $weights_matrix[$i]['w'], $bias_matrix[$i]['b']);

            }

        }

        $output_layour = $this->layour(end($arrays)['a'], $weights_matrix['output']['w'], $bias_matrix['output']['b']);

        return [
            'hidden_layours' => $arrays,
            'output_layour' => $output_layour,
        ];
        
      
    }
        
    public function layour_gradient ($layour_input, $layour, $layour_weight, $type, $previous_weight=null, $dependent_array=null, $dependent=null) {

        global $setting;

        foreach($layour['a'] as $id => $predicted) {

            if($type == "output") {

                $dependent_array['output'][$id]['T_error'] =  (float) $predicted['0'] - (float) $dependent[0][$id];

                $dependent_array['output'][$id]['der'] = $this->drivartive_function([$predicted], [$layour['z'][$id]])[0];


            }
            else if($type == ($setting['hidden_layer']-1)) {

                foreach($dependent_array['output'] as $key => $dependent) {

                    $dependent_array['layouri'][$type][$id]['T_errori'][$key] = $dependent['der'][0] * $dependent['T_error'] * $previous_weight[0][$id]; // take sum

                }

                $dependent_array['layour'][$type][$id]['T_error'] = array_sum($dependent_array['layouri'][$type][$id]['T_errori']);
                
                $dependent_array['layour'][$type][$id]['der'] = $this->drivartive_function([$predicted], [$layour['z'][$id]])[0];


            }
            else {

                foreach($dependent_array['layour'][$type+1] as $key => $dependent) {

                    $dependent_array['layouri'][$type][$id]['T_errori'][$key] = $dependent['der'][0] * $dependent['T_error'] * $previous_weight[0][$id]; // take sum

                }

                $dependent_array['layour'][$key][$id]['T_error'] = array_sum($dependent_array['layouri'][$type][$id]['T_errori']);
                
                $dependent_array['layour'][$key][$id]['der'] = $this->drivartive_function([$predicted], [$layour['z'][$id]])[0];

            }


            $error_array[$id]['error'] = $dependent_array['output'][$id]['T_error'] * $dependent_array['output'][$id]['der'][0];

        }

        foreach($layour_weight as $key => $weights) {

            foreach($weights as $id => $weight) {

               $arrays['error'][$key][$id] = $error_array[$key]['errori'] * $layour_input['a'][$id][0];

            }

        }


        $arrays['delta'] = $arrays['error'];

        $arrays['alpha'] = $error_array[$id]['error'];

        $arrays['dependent'] = $dependent_array;
     
        return $arrays;
    
    }
    
    public function gradientdecent($forward, $input, $ai_output, $weights_matrix, $bias_matrix) {

        global $setting;

        $output_new = $this->layour_gradient(end($forward["hidden_layours"]), $forward['output_layour'], $weights_matrix['output']['w'],  "output", null, null, $ai_output);

        $sum_slop['output'] = $output_new['delta'];

        $dependent['output'] = $output_new['dependent'];
        
        krsort($forward["hidden_layours"]);

        foreach($forward["hidden_layours"] as $key => $layour) {

            if($key == 0) {

                $layour_input = $input;

            }
            else {
                
                $layour_input = $forward["hidden_layours"][$key-1];
            
            }

            if($key == ($setting['hidden_layer']-1)) {

                $dependent = $dependent['output'];

                $previous_weights = $weights_matrix['output']['w'];
                 
            }
            else {

                $dependent = $dependent[$key+1];

                $previous_weights = $weights_matrix[$key+1]['w'];

            }

            $arrays[$key] = $this->layour_gradient($layour_input, $layour, $weights_matrix[$key]['w'], $key, $previous_weights, $dependent);

            $sum_slop[$key] = $arrays[$key]['delta'];

            $dependent[$key] = $arrays[$key]['dependent'];

        }

        return [
            'hidden_layours' => $arrays,
            'output_layour' => $output_new,
            'slops' =>  $sum_slop
        ];
        

    }

    public function new_weight ($arrays, $layour_weight) {

        global $setting;

        $arrays['corr_mat'] = $this->matrixTimesValue($arrays['delta'], $setting['learning_rate']);

        $arrays['w'] = $this->matrixSub($layour_weight, $arrays['corr_mat']);

        return $arrays;
    
    }

    public function new_bias ($arrays, $layour_bias) {

        global $setting;

        $arrays['corr_mat'] = $arrays['alpha'] * $setting['learning_rate'];

        $arrays['b'] = array(array($layour_bias['0'] - $arrays['corr_mat']));

        return $arrays;
    
    }

    public function backPropagation($forward, $weights_matrix, $bias_matrix, $gradient_dencent) {
    
        global $setting;

        $new_weights['output']['w'] = $this->new_weight($gradient_dencent['output_layour'], $weights_matrix['output']['w'])['w'];

        $new_bias['output']['b'] = $this->new_bias($gradient_dencent['output_layour'], $bias_matrix['output']['b'][0])['b'];
    
        foreach($forward["hidden_layours"] as $key => $layour) {
            
            $new_weights[$key]['w'] = $this->new_weight($gradient_dencent['hidden_layours'][$key], $weights_matrix[$key]['w'])['w'];

            $new_bias[$key]['b'] = $this->new_bias($gradient_dencent['hidden_layours'][$key], $bias_matrix[$key]['b'][0])['b'];

        }
    
        return [
            'weights' => $new_weights,
            'bias' => $new_bias
        ];

    }


  /**
   * It calculates the Sigmoid derivative of a given matrix
   * d/dX sigm = sigm (1 - sigm)
   *
   * @param array $matrix   the matrix where you want to calculate the derivative
   * @return array          the final matrix
   */
    public function sigmoidDerivative($z) {

        $z_2 = $this->matrixProductValueByValue($z,$z);

        return $this->matrixSub($z, $z_2);

    }


  /**
   * It calculates the Relu derivative of a given matrix
   * d/dX relu =  if (x > 0) then 1 else 0
   *
   * @param array $matrix   the matrix where you want to calculate the derivative
   * @return array          the final matrix
   */
    public function reluDerivate($z) {

        $relu_der = [];

        foreach($z as $row_num => $row) {

            foreach($row as $col_num => $val) {

                $relu_der[$row_num][$col_num] = ($val >= 0) ? 1 : 0;

            }

        }

        return $relu_der;

    }


  /**
   * It calculates the Hyperbolic Tangent derivative of a given matrix
   * d/dX tanh = (1-(tanh)^2)
   *
   * @param array $matrix   the matrix where you want to calculate the derivative
   * @return array          the final matrix
   */
    public function tanhDerivative($matrix) {

        $matrix_square = $this->matrixProductValueByValue($matrix,$matrix);
        $matrix_neg = $this->matrixTimesValue($matrix_square, -1);

        return $this->matrixSumValue($matrix_neg, 1);
        
    }

}
