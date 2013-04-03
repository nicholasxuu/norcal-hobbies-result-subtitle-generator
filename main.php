<?php

require_once("lib/sub_generator.php");
require_once("lib/result_analyzer.php");
require_once("lib/core.php");

/***
 * setup section start
 */

$filename = ".\\data\\20130327.txt";
$subtitle_type = "all"; // all - all racer's data; personal - personal lap data
$race_name = "2wd Buggy 17.5 A Main";
$output_filename = "C:\\Users\\Nicholas\\Videos\\RC\\20130327-Others-17.5TBuggy-EX-MAH00248.ass";
$video_race_start_time = 1.6;
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