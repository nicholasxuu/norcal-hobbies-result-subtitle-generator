<?php

require_once("core.php");

function result_analyzer($input) {
	//process input file for windows EOL and Mac EOL.
	$input = str_replace("\r\n", "\n", $input);
	$input = str_replace("\r", "\n", $input);


	$inputArr = explode("\n", $input);
	//echo var_dump($inputArr);


	$total_data = get_total_data($inputArr);
	//echo var_dump($total_data);

	return $total_data;
}

