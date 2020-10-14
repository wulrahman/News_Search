 
<?php

//https://github.com/awesomedata/awesome-public-datasets#agriculture
//https://datasetsearch.research.google.com/search?query=news&docid=pVEhRValD0vJaC7PAAAAAA%3D%3D
//https://www.statista.com/statistics/264488/important-news-categories-provided-by-local-news-apps-in-the-us
//https://towardsdatascience.com/top-sources-for-machine-learning-datasets-bb6d0dc3378b

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

if($user->admin == 1) {
    
    //https://www.dummies.com/programming/php/check-whether-php-cookies-are-enabled/
    
    //error_reporting(E_ALL);

    
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
    
    if($user->login_status == 1 ||  $user->cookie_id !== null) {
        
        if($user->login_status == 1) {

            $identifier_query = mysqli_query($setting["Lid"], "SELECT `identifier` FROM `user_geo_location` WHERE `user` = '".$user->id."' AND `timestamp` >= NOW() - INTERVAL 30 DAY");

            while($identifier = mysqli_fetch_array($identifier_query)) {

                $array_identifiers[] = $identifier['identifier'];

            }

        }
        else if($user->cookie_id !== null) {

            $array_identifiers[] = $user->cookie_id;

        }
        
        $article_query_clicked = mysqli_query($setting["Lid"], "(SELECT  `sentiment`, `author`, `readability`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`category`  AND `identifier` IN ('".implode("','", $array_identifiers)."') AND `clicks`.`timestamp` >= NOW() - INTERVAL 30 DAY) AS `count` FROM `news` ORDER BY `count` DESC LIMIT 100)");


        $article_query_noclicked = mysqli_query($setting["Lid"], "(SELECT  `sentiment`, `author`, `readability`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`category`  AND `identifier` IN ('".implode("','", $array_identifiers)."') AND `clicks`.`timestamp` >= NOW() - INTERVAL 30 DAY) AS `count` FROM `news` ORDER BY `count` ASC LIMIT 100)");
        
    }
    else {
        
        $article_query_clicked = mysqli_query($setting["Lid"], "(SELECT  `sentiment`, `author`, `readability`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`category`  AND `clicks`.`timestamp` >= NOW() - INTERVAL 30 DAY) AS `count` FROM `news` ORDER BY `count` DESC LIMIT 100)");
    
        
        $article_query_notclicked = mysqli_query($setting["Lid"], "(SELECT `sentiment`, `author`, `readability`, (SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `news`.`id` = `clicks`.`category`  AND `clicks`.`timestamp` >= NOW() - INTERVAL 30 DAY) AS `count` FROM `news` ORDER BY `count` ASC LIMIT 100)");
        
    }


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
        
    

        
    
$train_machine_learning = 1;
    
if($train_machine_learning == 1) {

    $brain = new Brainy($learning_rate , $activation_fun);

    // this is the input XOR matrix
    // remember to replace the zeros with -1 when you use TanH or Sigmoid
    $xor_in = array_splice($ai_data['input'], 0, 10000);
    
    // this is the output of the XOR
    // remember to replace the zeros with -1 when you use TanH or Sigmoid

    //$xor_out = array(1, 0, 1, 0, 0);
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
