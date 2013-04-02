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

	$totalData = array();
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
				$totalData[$currRace] = array();
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
				$totalData[$currRace][$currData["Car#"]] = $currData;
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
							array_push($totalData[$currRace][$lapSectArr[$i-1]]["laptime_array"], $plap_section[1]); // fill lap time
							array_push($totalData[$currRace][$lapSectArr[$i-1]]["position_array"], $plap_section[0]); // fill position 
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
					foreach ($totalData[$currRace] as $carNum => $whatever) {
						$temp_laptime_array = array();
						$temp_position_array = array();
						for ($i = 0; $i < count($totalData[$currRace][$carNum]["laptime_array"]); $i += 2) {
							array_push($temp_laptime_array, $totalData[$currRace][$carNum]["laptime_array"][$i]);
							array_push($temp_position_array, $totalData[$currRace][$carNum]["position_array"][$i]);
						}
						echo var_dump($temp_position_array);
						$totalData[$currRace][$carNum]["laptime_array"] = $temp_laptime_array;
						$totalData[$currRace][$carNum]["position_array"] = $temp_position_array;
					}
				}
				
				$section = 4; // 4 means end of one race
			}
		}
	}

	return $totalData;

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
function get_personal_ass_content($totalData, $race_name, $racer_number, $init_time) {
	$ass_format = "
[Script Info]
; Script generated by Aegisub 3.0.2
; http://www.aegisub.org/
Title:
Original Script:
Script Updated By: version 2.8.01
ScriptType: v4.00+
Collisions: Normal
PlayDepth: 0
Timer: 100,0000
Video Aspect Ratio: c1.77778
Video Zoom: 6
Scroll Position: 0
Active Line: 0
Video Zoom Percent: 0.5
Video File: 
YCbCr Matrix: TV.601

[V4+ Styles]
Format: Name, Fontname, Fontsize, PrimaryColour, SecondaryColour, OutlineColour, BackColour, Bold, Italic, Underline, StrikeOut, ScaleX, ScaleY, Spacing, Angle, BorderStyle, Outline, Shadow, Alignment, MarginL, MarginR, MarginV, Encoding
Style: DefaultVCD,Arial,39,&H00B4FCFC,&H00B4FCFC,&H01000008,&H80000008,-1,0,0,0,100,100,0,0,1,1,0,9,30,30,30,0

[Events]
Format: Layer, Start, End, Style, Name, MarginL, MarginR, MarginV, Effect, Text
";

	$ass_output = $ass_format;

	$start_time = $init_time;
	$fastest_lap_time = -1;
	foreach ($totalData[$race_name][$racer_number]["laptime_array"] as $i => $time) {
		
		if (isset($totalData[$race_name][$racer_number]["laptime_array"][$i+1]))
		{
			$next_lap_time = $totalData[$race_name][$racer_number]["laptime_array"][$i + 1];
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
		$ass_output .= "Dialogue: 0,{$curr_end_time},{$curr_after_end_time},DefaultVCD,NTP,0,0,0,,{\\an9}Position: {$totalData[$race_name][$racer_number]["position_array"][$i]} \n";
		
		// show last lap
		$ass_output .= "Dialogue: 0,{$curr_end_time},{$curr_next_end_time},DefaultVCD,NTP,0,0,0,,{\\an9}Last lap: {$time} \n";


		// show fastest lap
		$fastest_lap = "";
		if ($fastest_lap_time != -1 && $time < $fastest_lap_time) {
			$fastest_lap_time = $time;
			$fastest_lap = floatval($fastest_lap_time);
		} else if ($fastest_lap_time === -1) { // first lap, no show
			if ($time < $totalData[$race_name][$racer_number]["FastLap"]) {
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

// get the total time sheet for subtitle
// get time interval for each lap change, and the 
function get_total_sub_time_sheet($totalData, $race_name) {
	$retval = array();
	
	return $retval;
}