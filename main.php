<?php

require_once("lib/sub_generator.php");
require_once("lib/result_analyzer.php");
require_once("lib/core.php");

/***
 * setup section start
 */

$filename = ".\\data\\20130404.txt";
//$subtitle_type = "all"; // all - all racer's data; personal - personal lap data
$race_name = "4wd Buggy Open A Main";
$output_filename = "C:\\Users\\Nicholas\\Videos\\20130403-{$race_name}.ass";
$video_race_start_time = 3;
$race_length = 5; // in minutes

// set if subtitle_type = personal
$car_number = 0;

/***
 * setup section ends
 */





$input = get_input_file_content($filename);
$result_data = result_analyzer($input);
//echo var_dump($result_data);
sub_generator($result_data, $race_name, $video_race_start_time, $race_length, $output_filename);