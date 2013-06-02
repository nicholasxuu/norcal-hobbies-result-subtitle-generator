<?php

require_once("lib/sub_generator.php");
require_once("lib/result_analyzer.php");
require_once("lib/core.php");

/***
 * setup section start
 */
/* ------------------rc racing------------------- */
//$subtitle_type = "all"; // all - all racer's data; personal - personal lap data // function currently un-developed
$race_type = "rc_racing";



$date = "";//20130601
$filename = ".\\data\\20130601.txt"; // race result's file name
$race_name = "1-8 Buggy Electric A Main"; //1-8 Buggy Electric A Main race wanted to be generated into output

$car= "";//EB48
$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\18ebuggy.ass"; // output subtitle's file name
$video_race_start_time = 3; // in seconds, time in video where the tone starts, i.e 3 seconds into the video the tone starts. (note: tone's long, so make sure it's right at the beginning of the tone, maybe 0.1s before the tone starts, otherwise the timing board may looks a bit un-synced.
$race_length = 12; // in minutes

// set if subtitle_type = personal
// $car_number = 0;

/* ---------------------karting --------------- 
$race_type = "kart_racing";
$date = "20130521";
$filename = ".\\data\\kart{$date}.txt"; // race result's file name
$race_name = "Monza Enduro";

$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-{$race_name}.ass";
$video_race_start_time = 3;
$race_length = 45;
*/


/***
 * setup section ends
 */





$input = get_input_file_content($filename);
$result_data = result_analyzer($input, $race_type);
//echo var_dump($result_data);
//echo var_dump($result_data[$race_name][5]);


sub_generator($result_data, $race_name, $video_race_start_time, $race_length, $output_filename);