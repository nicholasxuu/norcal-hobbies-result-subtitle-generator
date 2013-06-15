<?php

/****
 * for shared functions
 **/

function get_input_file_content($filename) {
	$input = file_get_contents($filename);
	return $input;
}
 
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
	$curr_race = "";
	
	foreach ($inputArr as $line)
	{
		if (trim($line) != "" || $section === 2)
		{
			if (strstr($line, "Round#") !== false && strstr($line, "Race#") !== false) // title
			{
				// start new race
				$section = 0;
				
				$race_name_line_arr = preg_split("/[\s]{5,}/", $line);
				
				$curr_race = trim($race_name_line_arr[0]);
				$total_data[$curr_race] = array();
				
			}
			else if (preg_match('/.*Driver.*Car#.*Laps.*/', $line)) 
			{
				
				$section = 1;
				
				//echo var_dump($line);
				$driSectArr = get_driver_section_header_array($line);
				//echo var_dump($driSectArr);
				
				$finish_position = 1;
				
			}
			else if (preg_match('/.*1\_.+2\_.+3\_.*/', $line))
			{
				$section = 2;
				
				$line = $line . " ";

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
				//$currData["position_array"] = array();
				$currData["finish_position"] = $finish_position;
				$finish_position++;
				$total_data[$curr_race][$currData["Car#"]] = $currData;
			}
			else if ($section === 2)
			{
				if (empty($line)) {
					$type2input = true;
				}
				else
				{
					$line = $line . " ";// add ending delimiter
					
					$first_ending_splitter_index = 1; // first delimiter is in the beginning of the line, so we start from 1. this index is fixed for $lapSectArr
					
					for ($i = $first_ending_splitter_index; $i < count($lapSectIndex); $i++) {
						$plap_section = substr($line, $lapSectIndex[$i-1], $lapSectIndex[$i] - $lapSectIndex[$i-1]);
						$plap_section = trim($plap_section);
						//echo var_dump($plap_section);
						if (!empty($plap_section)) {
							$plap_section = explode("/", $plap_section);
							array_push($total_data[$curr_race][$lapSectArr[$i-1]]["laptime_array"], $plap_section[1]); // fill lap time
							
							//array_push($total_data[$curr_race][$lapSectArr[$i-1]]["position_array"], $plap_section[0]); // fill position 
						}
						
					}
				}
			}
			else if ($section === 3)
			{
				// these are not important data
				//echo var_dump($line);
				//echo var_dump($lapSectArr);
				// when new race starts, finish last race first
				if ($type2input) { // if it's second type of input, need to remove un-needed lines of data
					foreach ($lapSectArr as $temp => $carNum) {
						if (isset($total_data[$curr_race][$carNum])) {
							$temp_laptime_array = array();
							//$temp_position_array = array();
							//echo var_dump($total_data[$curr_race][$carNum]["laptime_array"]);
							for ($i = 0; $i < count($total_data[$curr_race][$carNum]["laptime_array"]); $i += 2) {
								//echo var_dump($carNum);
								array_push($temp_laptime_array, $total_data[$curr_race][$carNum]["laptime_array"][$i]);
								//array_push($temp_position_array, $total_data[$curr_race][$carNum]["position_array"][$i]);
							}
							//echo var_dump($temp_position_array);
							//echo var_dump($temp_laptime_array);
							$total_data[$curr_race][$carNum]["laptime_array"] = $temp_laptime_array;
							//$total_data[$curr_race][$carNum]["position_array"] = $temp_position_array;
						}
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
function get_personal_ass($total_data, $race_name, $racer_number, $init_time, $tags="") {

	$ass_output = "";

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
	if (floor($a["lap_i"]) != floor($b["lap_i"])) {
		return $a["lap_i"] < $b["lap_i"];
	} else if (abs($a["lap_i"] - $b["lap_i"]) > 0.001) { // floor == floor, check residual here
		return (fmod($a['lap_i'], 1) * floatval($a['next_laptime'])) < (fmod($b['lap_i'], 1) * floatval($b['next_laptime']));
	} else if ($a["position"] != $b["position"]) {
		return $a["position"] > $b["position"];
	} else {
		
		if ($a['finish'] && !$b['finish']) {
			return true;
		} else if (!$a['finish'] && $b['finish']) {
			return false;
		} else {
			return $a['race_time'] < $b['race_time'];
		}
	}
	// can be ignored
	return false;
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
			return $i - 1 + (($input_time - $prev_time) / $laptime);
		}
	}
	return $i-1;
}

// get all driver's current lap info at input_time, sorted by position
function get_all_dri_lap_i_curr_time($input_time, $total_data, $race_name) {
	$retval = array();
	foreach ($total_data[$race_name] as $car_num => $car_data) {
		$f = get_dri_lap_i_curr_time($input_time, $total_data, $race_name, $car_num);
		$i = floor($f);
		if ($i != -1 && !empty($car_data['laptime_array'])) {
			//echo var_dump($car_data);
			// if last lap finished, load driver's result instead
			if ($i == count($car_data['laptime_array']) - 1) { //finish result
				$this_position = intval($car_data["finish_position"]);
				$this_laps = intval($car_data["Laps"]) - 1; // index value, start from 0, will be corrected later
				$this_race_time = $car_data['RaceTime'];
				$this_fast_lap = isset($car_data['FastLap']) ? $car_data['FastLap'] : "";
				$this_finish = true;
				//echo var_dump($car_data);
				$this_behind = isset($car_data['Behind']) ? $car_data['Behind'] : "";
				$this_consistency = isset($car_data['consistency']) ? $car_data['consistency'] : "";
			} else {
				$this_position = intval($car_data["finish_position"]);
				$this_laps = $f;
				$this_race_time = "";
				$this_fast_lap = "";
				$this_finish = false;
				$this_behind = "";
				$this_consistency = "";
			}
		
			//echo var_dump($car_data);
			
			// Pos Name             LastLap Lap# RaceTime FastLap\N
			$curr_obj = array(
				"car_num" => $car_num, // 
				"lap_i" => $this_laps, // Lap#
				"driver_name" => $car_data['Driver'], // Name
				"position" => $this_position, // Pos
				"laptime" => $car_data['laptime_array'][$i], // LastLap
				"next_laptime" => isset($car_data['laptime_array'][$i+1]) ? $car_data['laptime_array'][$i+1] : "0", // currentLap
				"race_time" => $this_race_time, // RaceTime
				"fast_lap" => $this_fast_lap, // FastLap
				"finish" => $this_finish,
				"behind" => $this_behind, // Behind
				"consistency" => $this_consistency,
			);
			array_push($retval, $curr_obj);

		}
	}
	usort($retval, "cmp_pos");
	
	// calculate behind
	
	
	for ($i = 1; $i < count($retval); $i++) {
		if (floor($retval[$i]['lap_i']) == floor($retval[$i-1]['lap_i']) && empty($retval[$i]['behind'])) {
			$this_behind = (fmod($retval[$i-1]['lap_i'], 1) * floatval($retval[$i-1]['next_laptime'])) - (fmod($retval[$i]['lap_i'], 1) * floatval($retval[$i]['next_laptime']));
			$retval[$i]['behind'] = substr(strval($this_behind), 0, 4);
		}
	}
	
	//echo var_dump($retval);
	
	return $retval;
}

function make_string_length($input, $length, $type="str") {

	$output = str_repeat(" ", $length);
	if ($type == "int" || is_int($input) || $type == "float" || is_float($input)) {
		$input = strval($input);
		$type = "str";
	}

	
	// with lastname, firstname, no need to handle now
	if ($type == "name") {
		if (strpos($input, ",") !== false) {
			
		}
		$type = "str";
	}
	
	// don't need to handle, maybe handle later
	if ($type == "laptime") {
		$type = "str";
	}
	
	if ($type == "str") {
		while (strlen($input) < $length) {
			$input = $input . " ";
		}
		if (strlen($input) > $length) {
			//echo var_dump($input);
			$output = substr($input, 0, $length-3) . "...";
		} else {
			$output = $input;
		}
	}
	return $output;
}

function get_all_live_info_ass($total_data, $race_name, $init_time, $race_mins, $tags="\\an7") {
	$ass_output = "";
	$total_data = set_std_dev($total_data, $race_name);
	echo var_dump($total_data[$race_name]);
	
	$total_curr_time = get_total_curr_time($total_data, $race_name);
	//echo var_dump($total_data[$race_name]);
	
	foreach ($total_curr_time as $i => $curr_data) {

		$sub_string = "";//subtitle string
		
		$sub_string_0 = "";
		
		$blink_diff = isset($total_curr_time[$i+1]) ? $total_curr_time[$i+1]["curr_time"] - $total_curr_time[$i]["curr_time"] : 0.1;
		$start_time = get_time($init_time + $curr_data["curr_time"]);
		$mid_time = get_time($init_time + $curr_data["curr_time"] + min(0.1, $blink_diff)); // for time change user blink off
		$end_time = (isset($total_curr_time[$i+1])) ? get_time($init_time + $total_curr_time[$i+1]["curr_time"]) : "";
		
		//echo var_dump($curr_data);
		$curr_time_data = get_all_dri_lap_i_curr_time($curr_data["curr_time"], $total_data, $race_name);
		//var_dump($curr_time_data);
		
		// check if final lap session first
		$final_lap = false;
		for ($k = 0; $k < count($curr_time_data) && $final_lap == false; $k++) {
			if ($curr_time_data[$k]['finish'] && $curr_data["curr_time"] > ($race_mins * 60)) {
				$final_lap = true;
			}
		}
		
		// make up lap data
		foreach ($curr_time_data as $k => $person_lap) {
			// Pos Name               LastLap Lap# Behind\N
			// 123 123456789012345678 1234567 1234 123456\N
			
			// Pos Name               LastLap Lap# RaceTime  Behind FastLap\N
			// 123 123456789012345678 1234567 1234 123456789 123456 1234567\N
			
			//echo var_dump($person_lap);
			
			

			$this_driver_name = make_string_length($person_lap['driver_name'], 18, "name");
			$this_lap_i = make_string_length(floor($person_lap['lap_i']) + 1, 4);
			$this_laptime = make_string_length($person_lap['laptime'], 7);
			$this_behind = make_string_length($person_lap['behind'], 6);
			
			
			if ($final_lap) {
				
				
				$this_pos = make_string_length($person_lap['position'], 3);
				$this_race_time = make_string_length($person_lap['race_time'], 9);
				$this_fast_lap = make_string_length($person_lap['fast_lap'], 7);
				$this_consistency = make_string_length($person_lap['consistency'], 11);
				//$this_consistency = "123456789012";
				

				$temp_sub_string = "{$this_pos} {$this_driver_name} {$this_laptime} {$this_lap_i} {$this_race_time} {$this_behind} {$this_fast_lap} {$this_consistency}\N";
				
				
			} else {
				$this_pos = make_string_length($k + 1, 3);

				$temp_sub_string = "{$this_pos} {$this_driver_name} {$this_laptime} {$this_lap_i} {$this_behind}\N";
			}
			
			$sub_string .= $temp_sub_string;
			if ($curr_data["car_num"] == $person_lap["car_num"]) { // if it's the car crossing the link, give only this car blink effect
				$sub_string_0 .= "_\N";
			} else {
				$sub_string_0 .= $temp_sub_string;
			}

		}
		if ($final_lap) {
			$header_string = "Pos Name               LastLap Lap# RaceTime  Behind FastLap Consistency\N";
		} else {
			$header_string = "Pos Name               LastLap Lap# Behind\N";
		}
		
		$sub_string = $header_string . $sub_string;
		$sub_string_0 = $header_string . $sub_string_0;
		
		if ($end_time == "") {
			$end_time = get_time($init_time + $curr_data["curr_time"] + 3);
		}
		$ass_output .= "Dialogue: 0,{$start_time},{$mid_time},DefaultVCD,NTP,0,0,0,,{{$tags}}{$sub_string_0}\n";
		$ass_output .= "Dialogue: 0,{$mid_time},{$end_time},DefaultVCD,NTP,0,0,0,,{{$tags}}{$sub_string}\n";

	}
	
	return $ass_output;
}

// limited to minumte:sec.ignored
function str_time_to_int_sec($time) {
	//echo var_dump($time);
	if (strpos($time, ":") == false) {
		$time = "0:" . $time;
	}
	if (strpos($time, ".") == false) {
		$time = $time . ".0";
	} 
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

function get_timer_ass($init_time, $race_min, $total_data, $race_name, $tags="\\an9\\fscx200\\fscy200") {
	$ass_output = "";

	// get longest race_time
	$max_rt = "";
	foreach ($total_data[$race_name] as $i => $c_data) {
		//echo var_dump($c_data);
		if (isset($c_data['RaceTime']) && str_time_to_int_sec($c_data['RaceTime']) > str_time_to_int_sec($max_rt)) {
			$max_rt = $c_data['RaceTime'];
		}
	}
	//echo var_dump($max_rt);
	$max_time = str_time_to_int_sec($max_rt);
	//echo var_dump("---".$max_time);
	
	$race_time = $race_min * 60;
	//echo var_dump($race_time);
	
	for ($i = 0; $i < $max_time; $i++) {
		$curr_time = int_sec_to_str_time($race_time - $i);
		if ($race_time - $i <= 0) {
			$color_code = "\\c&H0000FF&";
			
		} else {
			$color_code = "\\c&H00FF00&";
			
			$start_time = get_time($init_time + $i);
			$end_time = get_time($init_time + $i + 1);
			$ass_output .= "Dialogue: 0,{$start_time},{$end_time},DefaultVCD,NTP,0,50,10,,{{$color_code}{$tags}}{$curr_time}\n";
		}
		//echo var_dump($curr_time);
		
		
	}
	//echo var_dump($ass_output);
	return $ass_output;
}

function get_average($arr) {
	$sum = 0;
	foreach ($arr as $laptime_str) {
		$sum += floatval($laptime_str);
	}
	//echo var_dump($sum / count($arr));
	return $sum / count($arr);
}

function set_std_dev($total_data, $race_name) {
	foreach($total_data[$race_name] as $i => $c_data) {
		//echo var_dump($c_data['laptime_array']);
		
		
		
		$average = get_average($c_data['laptime_array']);
		
		$sum = 0;
		foreach ($c_data['laptime_array'] as $laptime_str) {
			$sum += pow( (floatval($laptime_str) - $average) , 2);
		}
		$std_dev = sqrt($sum / count($c_data['laptime_array']));
		
		//echo var_dump($std_dev);
		
		$consistency = 100 - (floatval(intval(($std_dev/$average * 10000))) / 100);
		
		//echo var_dump($consistency);
		$total_data[$race_name][$i]['consistency'] = strval($consistency) . "%";
		//echo var_dump("---------");
		
		//echo var_dump($total_data[$race_name][$i]['consistency']);
	}
	//echo var_dump($total_data[$race_name]);
	return $total_data;
}