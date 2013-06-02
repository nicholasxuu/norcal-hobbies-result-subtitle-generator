<?php

require_once("core.php");

function result_analyzer($input, $race_type) {
	$total_data = array();
	if ($race_type == "kart_racing")
	{
		
		$retval = array(
			2 => array(
				'Driver' => "Nicholas Xu",
				'Car#' => "38",
				'Laps' => "86",
				'RaceTime' => "45:00.000",
				'FastLap' => "30.661",
				'Behind' => "",
				'laptime_array' => array(),
				'finish_position' => 1,
			),
			0 => array(
				'Driver' => "Aaron Farris",
				'Car#' => "3",
				'Laps' => "85",
				'RaceTime' => "45:00.000",
				'FastLap' => "30.943",
				'Behind' => "",
				'laptime_array' => array(),
				'finish_position' => 2,
			),
			1 => array(
				'Driver' => "WelcomeToTheJungho",
				'Car#' => "20",
				'Laps' => "85",
				'RaceTime' => "45:00.000",
				'FastLap' => "31.044",
				'Behind' => "",
				'laptime_array' => array(),
				'finish_position' => 3,
			),
		);
		
		$racer_index = 0;
		$lines = explode("\n", $input);
		$laptime_array = array();
		foreach ($lines as $line) {
			$line = trim($line);
			if (empty($line)) {
				$retval[$racer_index]['laptime_array'] = $laptime_array;
				$laptime_array = array();
				$racer_index++;
			} else {
				$elements = preg_split("/\s/", $line);
				if (sizeof($elements)==3) {
					array_push($laptime_array, $elements[1]);
					//echo var_dump($elements);
				}
				else
				{
					//echo var_dump($elements);
				}
			}
		}
		
		$total_data['Monza Enduro'] = $retval;
	}
	else if ($race_type == "rc_racing")
	{
		//process input file for windows EOL and Mac EOL.
		$input = str_replace("\r\n", "\n", $input);
		$input = str_replace("\r", "\n", $input);


		$inputArr = explode("\n", $input);
		//echo var_dump($inputArr);


		$total_data = get_total_data($inputArr);
		//echo var_dump($total_data);
	}
	return $total_data;
}

