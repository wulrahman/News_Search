 
<?php

//https://github.com/awesomedata/awesome-public-datasets#agriculture
//https://datasetsearch.research.google.com/search?query=news&docid=pVEhRValD0vJaC7PAAAAAA%3D%3D
//https://www.statista.com/statistics/264488/important-news-categories-provided-by-local-news-apps-in-the-us
//https://towardsdatascience.com/top-sources-for-machine-learning-datasets-bb6d0dc3378b

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

if($user->admin == 1) {
    
    require_once('../library/brainy-master/Brainy.php');

    
        
    $category_query = mysqli_query($setting["Lid"], "(SELECT `categorys`.`id`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `categorys`.`id` = `clicks`.`category` ) AS `count` FROM `categorys`)");
    
    while($category_row = mysqli_fetch_array($category_query)) {
                
        $category_data[$category_row['id']] = $category_row['count'];
        
    }
    
    $article_query = mysqli_query($setting["Lid"], "(SELECT *, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`category`) AS `count` FROM `news`)");
    
    while($article_row = mysqli_fetch_array($article_query)) {
                
        $article_data[$article_row['id']] = $article_row['count'];        
                        
        if(empty($author_data[$article_row['author']])) {
            $author_data[$article_row['author']] = $article_row['count'];
        }
        else {
            $author_data[$article_row['author']]+=$article_row['count'];
        }
         
        $tags_query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `map_tag`  WHERE `article` ='".$article_row['id']."'");

        $total_count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if( $total_count > 0) {

            while($tag_row = mysqli_fetch_array($tags_query)) {
                
                 if(empty($tag_data[$tag_row['tag']])) {
                    $tag_data[$tag_row['tag']] = $article_row['count'];
                }
                else {
                    $tag_data[$tag_row['tag']]+=$article_row['count'];
                }

            }
            
        }
        
        if(empty($sentiment_data[$article_row['sentiment']])) {
            $sentiment_data[$article_row['sentiment']] = $article_row['count'];
        }
        else {
            $sentiment_data[$article_row['sentiment']]+= $article_row['count'];
        }
        // will come back to tags 
        //input layer sentiment || author
        
        //output layer 
    
        if($article_row['count'] > 0) {
            
            $ai_data['output'][] = array(1);
            
        }
        else {
            $ai_data['output'][] = array(0);

        }
        
        $ai_data['input'][] = array(intval($article_row['sentiment']), intval($article_row['author']), intval($article_row['count']));
        
    }
    
    print_r($ai_data['output']);
        
    function sum_multi_dimentional($data) {
        
        foreach ($data as $key => $sub_array) {

          foreach ($sub_array as $id => $value) {

            $data_1[$id] += $value;

          }
            
        }
    
        return $data_1;
    
    }
    
    function probaility($data, $type = 1) {
            
        $data_sum = array_sum($data);
        
        foreach($data as $key => $count) {
            $probabilitys[$key] = (($count)+1/($data_sum));
        }
        
        $average = array_sum($probabilitys)/count($probabilitys);

        foreach ($probabilitys as $key => $probability) {

            if($probability > $average) {
                $array[$key] = 1;
            }
            else {
                $array[$key] = 0;
            }

        }
       
        return $array;

        
    }
        
    //https://byjus.com/probability-formulas/
    
    $category_probability = probaility($category_data);
    
    $sentiment_probability = probaility($sentiment_data);
    
    $article_probability = probaility($article_data);
    
    $author_probability = probaility($author_data);
    
    $tag_probability = probaility($tag_data);
    
        
    $probability['input']["sentiment"] = $sentiment_data;
            
    $probability['input']["category"] = $category_data;
    
    $probability['input']["article"] = $article_data;
    
    $probability['input']["author"] = $author_data;
    
    $probability['input']["tags"] = $tag_data;


    
    
    $probability['output']["sentiment"] = $sentiment_probability;
            
    $probability['output']["category"] = $category_probability;
    
    $probability['output']["article"] = $article_probability;
    
    $probability['output']["author"] = $author_probability;
    
    $probability['output']["tags"] = $tag_probability;

    
    //print_r($tag_probability);

    //print("<pre>".print_r($probability,true)."</pre>");
    
    
$train_machine_learning = 1;
    
if($train_machine_learning == 1) {
    
    //require_once('../library/brainy-master/Brainy.php');

    // tanh     : 30000   0.01    3   -1
    // sigmoid  : 30000   0.01    3   -1
    // relu     : 3000    0.01    3   0

    // choose the tot number of epochs
    $epochs = 50;
    // choose the learning rate
    $learning_rate = 0.01;
    // numbers of hidden neurons of the first (and only one) layer
    $hidden_layer_neurons = 3;
    // activation functions: relu , tanh , sigmoid
    $activation_fun = 'relu';

    $brain = new Brainy($learning_rate , $activation_fun);

    // this is the input XOR matrix
    // remember to replace the zeros with -1 when you use TanH or Sigmoid
    $xor_in = array_splice($ai_data['input'], 0, 10000);
    
//    $xor_in = [
//			[0,4, 1,2],
//			[0,1, 5, 0],
//			[1,6, 0, 1],
//			[1,1, 5, 1],
//            [1,4, 5, 1],
//];

    
    //print_r($xor_in);
    
    // this is the output of the XOR
    // remember to replace the zeros with -1 when you use TanH or Sigmoid

    //$xor_out = array(1, 0, 1, 0, 0);
    $xor_out = array_splice($ai_data['output'], 0, 10000);
    

    $input_neurons = 2;
    if(!is_string($xor_in[0])) {
        echo $input_neurons = count($xor_in[0]);

    } 
    $output_neurons = 1;
    if(!is_string($xor_out[0])) {
        echo $output_neurons = count($xor_out[0]);

    }

    // getting the W1 weights random matrix (layer between input and the hidden layer) with size 2 x $hidden_layer_neurons
    $w1 = $w1_before = $brain->getRandMatrix($input_neurons, $hidden_layer_neurons);

    // getting the W2 weights random vector (layer between hidden layer and output) with size $hidden_layer_neurons x 1
    $w2 = $w2_before = $brain->getRandMatrix($hidden_layer_neurons , $output_neurons);

    // getting the B1 bies random vector with size $hidden_layer_neurons
    $b1 = $b1_before = $brain->getRandMatrix($hidden_layer_neurons , 1);

    // getting the B2 bies random vector. The size is 1x1 because there is only one output neuron
    $b2 = $b2_before =  $brain->getRandMatrix($output_neurons, 1);


    // this is for the chart
    $graph = [];
    $denom = 0;
    $correct = 0;
    $points_checker = $epochs / 100 * 4;
    if ($points_checker < 10) $points_checker = 10;



    // preparing the arrays
    foreach($xor_in as $index => $input) {
        $xor_in[$index] = $brain->arrayTranspose($input);
        $xor_out[$index] = $brain->arrayTranspose($xor_out[$index]);
    }


    $execution_start_time = microtime(true);

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

            // this is only for che accuracy chart
            $f1 = round($brain->getScalarValue($forward_response['A']) , 2);
            $f2 = round($brain->getScalarValue($xor_out[$index]) , 2);
            if ($f2 < 0) $f2 = 0;
            if ($f1 == $f2) $correct++;
            $denom++;

        } // end foreach

        // this is only for che accuracy chart
        if (!($i % $points_checker)) {
            $graph[] = $rate = $correct / $denom;
            $denom = 0;
            $correct = 0;
        }

    } // end for $epochs

    

    $execution_time = round( microtime(true) - $execution_start_time ,2);


    $g_labes = $g_vals = '';
    foreach($graph as $num => $val) {
        $g_labes .= ($num*$points_checker) . ',';
        $g_vals .= (round( $val, 2)) . ',';
    }
    $g_labes = trim($g_labes, ',');
    $g_vals = trim($g_vals, ',');
    
}
//    
//    $w1 = $w1_before = array (
//  0 => 
//  array (
//    0 => -3.8163386574876,
//    1 => -0.031487914822001,
//    2 => -3.0702426397741,
//  ),
//);
//$b1 = $b1_before = array (
//  0 => 
//  array (
//    0 => 20.240841455716,
//  ),
//  1 => 
//  array (
//    0 => 6.2742125881039,
//  ),
//  2 => 
//  array (
//    0 => 21.62418234281,
//  ),
//);
//$w2 = $w2_before = array (
//  0 => 
//  array (
//    0 => 19.715087957031,
//  ),
//  1 => 
//  array (
//    0 => -6.1701948348656,
//  ),
//  2 => 
//  array (
//    0 => -20.902485817579,
//  ),
//);
//$b2 = $b2_before = array (
//  0 => 
//  array (
//    0 => -2.88693,
//  ),
//);

    ?>


    <html>
    <head>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.1.1/Chart.min.js"></script>
        <style>
            body { font-family: monospace; margin: 50px; }
            circle { display:none; }
            .center { text-align:center; }
        </style>
    </head>
    <body>

    <h2 class="center">Activation funcion: <?= ucwords($activation_fun) ?></h2>

    <div class="chart" style="width:600px; margin:20px auto;">
        <canvas height="200" id="lineChart" style="height:400px; margin:20px auto;"></canvas>
    </div>


    <br />
    <h3>Hidden neurons: <?= $hidden_layer_neurons ?></h3>
    <h3>Learning rate: <?= $learning_rate ?></h3>
    <h3>Epochs: <?= $epochs ?></h3>
    <h3>Execution time: <?= $execution_time ?> sec</h3>

    <br />
    <br />





    <?php
        echo '<hr /><h1>Prediction:</h1>';
        foreach($xor_in as $index => $input) {
            $prediction = $brain->forward($input, $w1, $b1, $w2, $b2);

            print_r($prediction['A'] );
        }

        echo '<hr /><h1>Before</h1>';
        echo '<br /><h4>Weights matrix W1</h4>';
        sm($w1_before);
        echo '<br /><h4>Bias matrix B1</h4>';
        sm($b1_before);
        echo '<br /><h4>Weights matrix W2</h4>';
        sm($w2_before);
        echo '<br /><h4>Bias matrix B2</h4>';
        sm($b2_before);

        echo '<hr /><h1>After</h1>';
        echo '<br /><h4>Weights matrix W1</h4>';
        sm($w1);
        echo '<br /><h4>Bias matrix B1</h4>';
        sm($b1);
        echo '<br /><h4>Weights matrix W2</h4>';
        sm($w2);
        echo '<br /><h4>Bias matrix B2</h4>';
        sm($b2);
        echo '<hr />';

        $str  = '$w1 = $w1_before = '.var_export($w1_before, true).';' ."\n";
        $str .= '$b1 = $b1_before = '.var_export($b1_before, true).';' ."\n";
        $str .= '$w2 = $w2_before = '.var_export($w2_before, true).';' ."\n";
        $str .= '$b2 = $b2_before = '.var_export($b2_before, true).';' ."\n";

        dd($str, false);
    ?>




    <script>
      $(function () {

            var areaChartData = {
              labels: [<?= $g_labes ?>],
              datasets: [
                {
                  fillColor: "rgba(60,141,188,0.9)",
                  strokeColor: "rgba(60,141,188,0.8)",
                  pointColor: "#3b8bba",
                  pointStrokeColor: "rgba(60,141,188,1)",
                  pointHighlightFill: "#fff",
                  pointHighlightStroke: "rgba(60,141,188,1)",
                  data: [<?= $g_vals ?>],
                }
              ]
            };

            var areaChartOptions = {
               showScale: true,
               scaleShowGridLines: true,
               scaleGridLineColor: "rgba(0,0,0,.05)",
               scaleGridLineWidth: 1,
               scaleShowHorizontalLines: true,
               scaleShowVerticalLines: true,
               bezierCurve: true,
               bezierCurveTension: 0.3,
               pointDot: false,
               pointDotRadius: 4,
               pointDotStrokeWidth: 1,
               pointHitDetectionRadius: 20,
               datasetStroke: true,
               datasetStrokeWidth: 2,
               datasetFill: true,
               maintainAspectRatio: false,
               responsive: true,
             };

            var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
            var lineChart = new Chart(lineChartCanvas);
            var lineChartOptions = areaChartOptions;
            lineChartOptions.datasetFill = false;
            lineChart.Line(areaChartData, lineChartOptions);
      });

    </script>


    </body>
    </html>

<?php

    //https://monkeylearn.com/text-classification/
}
else {

	require_once('../common/pages/404.php');

}

//https://www.youtube.com/watch?v=EGKeC2S44Rs
?>
