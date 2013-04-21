<?php

require_once("lib/sub_generator.php");
require_once("lib/result_analyzer.php");
require_once("lib/core.php");

/***
 * setup section start
 */

//$subtitle_type = "all"; // all - all racer's data; personal - personal lap data // function currently un-developed

$date = "20130421";
$filename = ".\\data\\{$date}.txt"; // race result's file name
$race_name = "2wd Buggy Open A Main"; // race wanted to be generated into output

$car= "TLR22";
$output_filename = "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-{$car}-{$race_name}.ass"; // output subtitle's file name
$video_race_start_time = 3; // in seconds, time in video where the tone starts, i.e 3 seconds into the video the tone starts. (note: tone's long, so make sure it's right at the beginning of the tone, maybe 0.1s before the tone starts, otherwise the timing board may looks a bit un-synced.
$race_length = 6; // in minutes

// set if subtitle_type = personal
// $car_number = 0;



/***
 * setup section ends
 */





$input = get_input_file_content($filename);
$result_data = result_analyzer($input);
//echo var_dump($result_data);
//echo var_dump($result_data[$race_name]);
sub_generator($result_data, $race_name, $video_race_start_time, $race_length, $output_filename);