<?php

/****
 * for shared functions
 **/


 
// get array of car# based on driver section's headerline.
function get_driver_section_header_array($line) {
	$driSectLine = trim($line);
	$driSectLine = trim($driSectLine, "_");
	$driSectLine = str_replace(" ", "", $driSectLine);
	$driSectArr = preg_split('/_+/', $driSectLine);
	return $driSectArr;
}

// get array of car# based on lap section's headerline.
function get_lap_section_header_array($line) {
	$lapSectLine = trim($line);
	$lapSectLine = trim($lapSectLine, "_");
	$lapSectLine = str_replace(" ", "", $lapSectLine);
	$lapSectArr = preg_split('/_+/', $lapSectLine);
	return $lapSectArr;
}

// get the index of delimiters in a line of lap section headerline, 
// later laptime lines will be in the same index format.
function get_lap_section_index($line, $delimiter = " ") {
	$last_i = 0;
	$lapSectIndex = array();
	while (($i = strpos($line, $delimiter, $last_i)) !== false) {
		$last_i = $i + 1;
		array_push($lapSectIndex, $i);
	}
	return $lapSectIndex;
}

// get total data object from result sheet
function get_total_data($inputArr) {
	$section = 0;
	$type2input = false;

	$total_data = array();
	$driSectArr = array();
	$lapSectArr = array();
	$lapSectIndex = array(); 
	$currRace = "";
	
	foreach ($inputArr as $line)
	{
		if (trim($line) != "" || $section === 2)
		{
			if (strstr($line, "Round#") !== false && strstr($line, "Race#") !== false) // title
			{
				// start new race
				$section = 0;
				
				$currRace = trim($line);
				$total_data[$currRace] = array();
			}
			else if (preg_match('/.*Driver.*Car#.*Laps.*/', $line)) 
			{
				
				$section = 1;
				
				//echo var_dump($line);
				$driSectArr = get_driver_section_header_array($line);
				//echo var_dump($driSectArr);
				
			}
			else if (preg_match('/.*1\_.+2\_.+3\_.*/', $line))
			{
				$section = 2;

				//echo var_dump($line);
				$lapSectArr = get_lap_section_header_array($line);
				//echo var_dump($lapSectArr);
				
				$lapSectIndex = get_lap_section_index($line);
				//echo var_dump($lapSectIndex);
			}
			else if ($section === 2 && preg_match('/----/', $line))
			{
				$section = 3;
				//echo $line;
			}
			else if ($section === 1)
			{
				//echo var_dump($line);
				$elementArr = preg_split('/\s+/', $line);
				//echo var_dump($elementArr);
				
				$i = 0;
				$name = true;
				$currData = array();
				$currData["Driver"] = "";
				
				foreach ($elementArr as $e)
				{
					// Assuming all start with driver's name
					if ($name && (! preg_match('/#\d/', $e)))
					{
						$currData["Driver"] .= " " . $e;
					}
					else
					{
						$name = false;
						$i++;
						$currData[$driSectArr[$i]] = $e;
					}	
				}
				$currData["Driver"] = trim($currData["Driver"]);
				//echo var_dump($currData);
				
				$currData["Car#"] = str_replace("#", "", $currData["Car#"]);
				$currData["laptime_array"] = array();
				$currData["position_array"] = array();
				$total_data[$currRace][$currData["Car#"]] = $currData;
			}
			else if ($section === 2)
			{
				if (empty($line)) {
					$type2input = true;
				}
				else
				{
					$first_ending_splitter_index = 1; // first delimiter is in the beginning of the line, so we start from 1. this index is fixed for $lapSectArr
					for ($i = $first_ending_splitter_index; $i < count($lapSectIndex)-1; $i++) {
						$plap_section = substr($line, $lapSectIndex[$i-1], $lapSectIndex[$i] - $lapSectIndex[$i-1]);
						$plap_section = trim($plap_section);
						//echo var_dump($plap_section);
						if (!empty($plap_section)) {
							$plap_section = explode("/", $plap_section);
							array_push($total_data[$currRace][$lapSectArr[$i-1]]["laptime_array"], $plap_section[1]); // fill lap time
							array_push($total_data[$currRace][$lapSectArr[$i-1]]["position_array"], $plap_section[0]); // fill position 
						}
						
					}
				}
			}
			else if ($section === 3)
			{
				// these are not important data
				//echo var_dump($line);
				
				// when new race starts, finish last race first
				if ($type2input) { // if it's second type of input, need to remove un-needed lines of data
					foreach ($total_data[$currRace] as $carNum => $whatever) {
						$temp_laptime_array = array();
						$temp_position_array = array();
						for ($i = 0; $i < count($total_data[$currRace][$carNum]["laptime_array"]); $i += 2) {
							array_push($temp_laptime_array, $total_data[$currRace][$carNum]["laptime_array"][$i]);
							array_push($temp_position_array, $total_data[$currRace][$carNum]["position_array"][$i]);
						}
						//echo var_dump($temp_position_array);
						$total_data[$currRace][$carNum]["laptime_array"] = $temp_laptime_array;
						$total_data[$currRace][$carNum]["position_array"] = $temp_position_array;
					}
				}
				
				$section = 4; // 4 means end of one race
			}
		}
	}

	return $total_data;

}

// get formatted time for ass subtitle
// input time float in second, output string of time
function get_time($input_time) {
	$mse = floor( ( fmod($input_time, 1) * 100 ) % 100 );
	$sec = str_pad(floor($input_time % 60), 2, "0",STR_PAD_LEFT);
	$min = str_pad(floor(($input_time / 60) % 60), 2, "0",STR_PAD_LEFT);
	$hrs = floor($input_time / 3600);
	
	return "{$hrs}:{$min}:{$sec}.{$mse}";
}

// get subtitle content
function get_personal_ass_content($total_data, $race_name, $racer_number, $init_time, $ass_format) {

	$ass_output = $ass_format;

	$start_time = $init_time;
	$fastest_lap_time = -1;
	foreach ($total_data[$race_name][$racer_number]["laptime_array"] as $i => $time) {
		
		if (isset($total_data[$race_name][$racer_number]["laptime_array"][$i+1]))
		{
			$next_lap_time = $total_data[$race_name][$racer_number]["laptime_array"][$i + 1];
			$position_time = 5;
		}
		else
		{
			$next_lap_time = 20;
			$position_time = 20;
		}
			
		$curr_start_time = get_time($start_time);
		
		$curr_end_time = get_time($start_time + $time);
		$curr_next_end_time = get_time($start_time + $time + $next_lap_time);
		$curr_after_end_time = get_time($start_time + $time + $position_time);
		
		// show current lap
		$ass_output .= "Dialogue: 0,{$curr_start_time},{$curr_end_time},DefaultVCD,NTP,0,0,0,,{\\an7\\fscx50\\fscy50}current lap: {$time} \n";
		
		// show position
		$ass_output .= "Dialogue: 0,{$curr_end_time},{$curr_after_end_time},DefaultVCD,NTP,0,0,0,,{\\an9}Position: {$total_data[$race_name][$racer_number]["position_array"][$i]} \n";
		
		// show last lap
		$ass_output .= "Dialogue: 0,{$curr_end_time},{$curr_next_end_time},DefaultVCD,NTP,0,0,0,,{\\an9}Last lap: {$time} \n";


		// show fastest lap
		$fastest_lap = "";
		if ($fastest_lap_time != -1 && $time < $fastest_lap_time) {
			$fastest_lap_time = $time;
			$fastest_lap = floatval($fastest_lap_time);
		} else if ($fastest_lap_time === -1) { // first lap, no show
			if ($time < $total_data[$race_name][$racer_number]["FastLap"]) {
				$fastest_lap = "N/A";
			} else {
				$fastest_lap_time = $time;
				$fastest_lap = floatval($fastest_lap_time);
			}
		} else { // keep original fastest lap
			$fastest_lap = floatval($fastest_lap_time);
		}
		$ass_output .= "Dialogue: 0,{$curr_end_time},{$curr_next_end_time},DefaultVCD,NTP,0,0,0,,{\\an9}Fastest lap: {$fastest_lap} \n";

		
		$start_time = $start_time + $time;

	}

	// show finish sign
	$finish_start_time = get_time($start_time);
	$finish_end_time = get_time($start_time + 3);
	$ass_output .= "Dialogue: 0,{$finish_start_time},{$finish_end_time},DefaultVCD,NTP,0,0,0,,{\\an5\\fscx200\\fscy200}Finish\n";

	return $ass_output;
}


function cmp_curr_time($a, $b) {
	return $a["curr_time"] > $b["curr_time"];
}

function cmp_pos($a, $b) {
	//echo var_dump($a);
	return $a["position"] > $b["position"];
}


// get time for any driver's lap change, sorted by the changing time
function get_total_curr_time($total_data, $race_name) {
	$retval = array();

	foreach ($total_data[$race_name] as $car_num => $car_data) {
		$curr_time = 0;
		foreach ($car_data["laptime_array"] as $i => $laptime) {
			$curr_time += $laptime;
			
			$curr_obj = array(
				"curr_time" => $curr_time,
				"car_num" => $car_num,
				"lap_i" => $i,
			);
			array_push($retval, $curr_obj);
		}
	}
	usort($retval, "cmp_curr_time");

	return $retval;
}

// get a driver's current lap index at input_time
function get_dri_lap_i_curr_time($input_time, $total_data, $race_name, $car_num) {
	$curr_time = 0;
	
	for ($i = 0; $i < count($total_data[$race_name][$car_num]["laptime_array"]); $i++) {
		$laptime = $total_data[$race_name][$car_num]["laptime_array"][$i];
		
		$prev_time = $curr_time;
		$curr_time += $laptime;
		
		// TODO: check edge value if logical
		if ($curr_time - $input_time > 0.001) {
			return $i-1;
		}
	}
	return $i-1;
}

// get all driver's current lap info at input_time, sorted by position
function get_all_dri_lap_i_curr_time($input_time, $total_data, $race_name) {
	$retval = array();
	foreach ($total_data[$race_name] as $car_num => $car_data) {
		$i = get_dri_lap_i_curr_time($input_time, $total_data, $race_name, $car_num);
		if ($i != -1 && !empty($car_data['laptime_array'])) {

			//echo var_dump($car_data);
			$curr_obj = array(
				"car_num" => $car_num,
				"lap_i" => $i,
				"driver_name" => $car_data['Driver'],
				"position" => $car_data['position_array'][$i],
				"laptime" => $car_data['laptime_array'][$i],
			);
			array_push($retval, $curr_obj);

		}
	}
	usort($retval, "cmp_pos");
	return $retval;
}

function get_overall_ass_content($total_data, $race_name, $init_time, $ass_format, $sub_pos) {
	$ass_output = $ass_format;

	$total_curr_time = get_total_curr_time($total_data, $race_name);
	//echo var_dump($total_curr_time);
	
	foreach ($total_curr_time as $i => $curr_data) {

		$sub_string = "";//subtitle string
		
		$start_time = get_time($init_time + $curr_data["curr_time"]);
		$end_time = (isset($total_curr_time[$i+1])) ? get_time($init_time + $total_curr_time[$i+1]["curr_time"]) : get_time(99999);
		
		//echo var_dump($curr_data["curr_time"]);
		$curr_time_data = get_all_dri_lap_i_curr_time($curr_data["curr_time"], $total_data, $race_name);
		// var_dump($curr_time_data);
		
		$sub_string .= "Pos - Name           - Lap# - Last lap\N";
		foreach ($curr_time_data as $k => $person_lap) {
			
			$this_pos = $k+1;
			$this_lap_i = $person_lap['lap_i'] + 1;
			$sub_string .= "{$this_pos} - {$person_lap['driver_name']} -{$this_lap_i}- {$person_lap['laptime']}\N";
		}
		
		$ass_output .= "Dialogue: 0,{$start_time},{$end_time},DefaultVCD,NTP,0,0,0,,{$sub_pos}{$sub_string}\n";

	}
	
	return $ass_output;
}

// limited to minumte:sec.ignored
function str_time_to_int_sec($time) {
	$t1 = explode(":", $time);
	$t2 = explode(".", $t1[1]);
	return intval($t1[0]) * 60 + intval($t2[0]) + 1;
}

function int_sec_to_str_time($input_time) {
	$time = "";
	if ($input_time < 0) {
		$time .= "-";
		$input_time = 0-$input_time;
	}
	$sec = str_pad(floor($input_time % 60), 2, "0",STR_PAD_LEFT);
	$min = str_pad(floor(($input_time / 60) % 60), 1, "0",STR_PAD_LEFT);
	return $time . $min . ":" . $sec;
}

function add_timer_ass($ass_input, $init_time, $race_min, $total_data, $race_name) {
	// get longest race_time
	$max_rt = "";
	foreach ($total_data[$race_name] as $i => $c_data) {
		//echo var_dump($c_data);
		if (isset($c_data['RaceTime']) && $c_data['RaceTime'] > $max_rt) {
			$max_rt = $c_data['RaceTime'];
		}
	}
	//echo var_dump($max_rt);
	$max_time = str_time_to_int_sec($max_rt);
	
	$race_time = $race_min * 60;
	
	$ass_output = "";
	for ($i = 0; $i < $max_time; $i++) {
		$curr_time = int_sec_to_str_time($race_time - $i);
		//echo var_dump($curr_time);
		
		$start_time = get_time($init_time + $i);
		$end_time = get_time($init_time + $i + 1);
		$ass_output .= "Dialogue: 0,{$start_time},{$end_time},DefaultVCD,NTP,0,0,0,,{\an8\\fscx150\\fscy150}{$curr_time}\n";
	}
	//echo var_dump($ass_output);
	return $ass_input.$ass_output;
}