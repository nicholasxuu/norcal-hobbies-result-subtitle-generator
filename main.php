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



$date = "20131215";//
$filename = ".\\data\\{$date}.txt"; // race result's file name


/*
$race_name = "17.5 2wd Short Course A Main"; 
$car= "DESC210";
$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-{$car}-{$race_name}-.ass"; 
$video_race_start_time = 3; 
$race_length = 5; 

$race_name = "2wd Buggy 17.5 A Main"; 
$car= "TLR22";
$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-{$car}-{$race_name}-.ass"; 
$video_race_start_time = 3; 
$race_length = 5; 

$race_name = "4wd Buggy Open A Main"; 
$car= "DEX410V3";
$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-{$car}-{$race_name}-.ass"; 
$video_race_start_time = 3; 
$race_length = 5; 

$race_name = "2wd Short Course Open A Main"; 
$car= "DESC210";
$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-{$car}-{$race_name}-.ass"; 
$video_race_start_time = 3; 
$race_length = 5; 

$race_name = "4x4 Short Course Open A Main"; 
$car= "DESC410";
$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-{$car}-{$race_name}-.ass"; 
$video_race_start_time = 3; 
$race_length = 5; 


*/
$race_name = "4wd Buggy Open A2 Main"; 
$car= "DEX410V3";
$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-{$car}-{$race_name}-.ass"; 
$video_race_start_time = 3; 
$race_length = 6; 

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