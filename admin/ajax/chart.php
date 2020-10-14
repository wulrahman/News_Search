<?php

header('Content-Type: application/json');

// Set up the ORM library
require_once("../../setting.php");

require_once("../../portable-utf8.php");

require_once("../../common.php");

if (isset($_GET['start']) AND isset($_GET['end'])) {

	$start = $_GET['start'];

	$end = $_GET['end'];

	$datas = array();

	$rank = 0;

	$range = DateRangeArray($start, $end);

	foreach($range as $key => $date) {

		$rank++;

		$row = mysqli_fetch_object(mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS COUNT(*) as `value`, `ip`, DATE(`timestamp`) as `data` FROM `views` WHERE DATE(`timestamp`) = '".$date."' GROUP BY `ip`, DATE(`timestamp`)"));

		$count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

		$datas[$rank]['label'] = $date;

		$datas[$rank]['value'] = $count;

	}

	echo json_encode($datas);

}

?>
