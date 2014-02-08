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
$date = "20140115";//
$filename = ".\\data\\{$date}.txt"; // race result's file name



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

$setupArr = Array();

/*** wed night
$setupArr[] = Array(
	"race_name" => "Rookie A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Rookie A Main.ass",
	"video_race_start_time" => 3,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "Staduim Truck Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Staduim Truck Open A Main.ass",
	"video_race_start_time" => 8.6,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "Sportsman 17.5 Buggy A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Sportsman 17.5 Buggy A Main.ass",
	"video_race_start_time" => 3,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "2wd Buggy 17.5 A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Buggy 17.5 A Main.ass",
	"video_race_start_time" => 10,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "Sportsman 2wd Buggy Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Sportsman 2wd Buggy Open A Main.ass",
	"video_race_start_time" => 3,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "Sportsman 4x4 ShortCourse A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Sportsman 4x4 ShortCourse A Main.ass",
	"video_race_start_time" => 3,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "2wd Short Course Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Short Course Open A Main.ass",
	"video_race_start_time" => 37.7,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "4x4 Short Course Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-4x4 Short Course Open A Main.ass",
	"video_race_start_time" => 0.96,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "2wd Buggy Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Buggy Open A Main.ass",
	"video_race_start_time" => 8.5,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "4wd Buggy Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-4wd Buggy Open A Main.ass",
	"video_race_start_time" => 18.9,
	"race_length" => 6,
);
$setupArr[] = Array(
	"race_name" => "1-8 E Buggy A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-1-8 E Buggy A Main.ass",
	"video_race_start_time" => 3,
	"race_length" => 6,
);


// on-road
$setupArr[] = Array(
	"race_name" => "F-1 Slivercan-21.5 NR A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-F-1 Slivercan-21.5 NR A2 Main.ass",
	"video_race_start_time" => 0.2,
	"race_length" => 6,
);

$setupArr[] = Array(
	"race_name" => "Touring Car 17.5 Sportsman A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Touring Car 17.5 Sportsman A2 Main.ass",
	"video_race_start_time" => 8.528,
	"race_length" => 6,
);

$setupArr[] = Array(
	"race_name" => "Touring Car NR 17.5 Expert A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Touring Car NR 17.5 Expert A2 Main.ass",
	"video_race_start_time" => 19.9,
	"race_length" => 6,
);

$setupArr[] = Array(
	"race_name" => "Vintage Trans-Am A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Vintage Trans-Am A2 Main.ass",
	"video_race_start_time" => 3.77,
	"race_length" => 8,
);

$setupArr[] = Array(
	"race_name" => "Mini (FWD or 4WD) 21.5 A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Mini 21.5 A2 Main.ass",
	"video_race_start_time" => 2.05,
	"race_length" => 6,
);

$setupArr[] = Array(
	"race_name" => "Touring Car Open A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Touring Car Open A2 Main.ass",
	"video_race_start_time" => 4.43,
	"race_length" => 6,
);


//saturday a2
$setupArr[] = Array(
	"race_name" => "2wd Buggy 17.5 A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Buggy 17.5 A2 Main.ass",
	"video_race_start_time" => 0.5,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "4x4 Short Course Open A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-4x4 Short Course Open A2 Main.ass",
	"video_race_start_time" => 0.96,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "2wd Buggy Open A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Buggy Open A2 Main.ass",
	"video_race_start_time" => 3.33,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "4wd Buggy Open A2 Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-4wd Buggy Open A2 Main.ass",
	"video_race_start_time" => 1.4,
	"race_length" => 5,
);


*/


$setupArr[] = Array(
	"race_name" => "2wd Buggy Open B Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Buggy Open B Main.ass",
	"video_race_start_time" => 6.14,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "4wd Buggy Open B Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-4wd Buggy Open B Main.ass",
	"video_race_start_time" => 3.45,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "Staduim Truck Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Staduim Truck Open A Main.ass",
	"video_race_start_time" => 3.034,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "Sportsman 17.5 Buggy A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Sportsman 17.5 Buggy A Main.ass",
	"video_race_start_time" => 15.42,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "2wd Buggy 17.5 A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Buggy 17.5 A Main.ass",
	"video_race_start_time" => 15.91,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "Sportsman 4x4 ShortCourse A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-Sportsman 4x4 ShortCourse A Main.ass",
	"video_race_start_time" => 17.672,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "2wd Short Course Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Short Course Open A Main.ass",
	"video_race_start_time" => 7.969,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "4x4 Short Course Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-4x4 Short Course Open A Main.ass",
	"video_race_start_time" => 20.635,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "2wd Buggy Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-2wd Buggy Open A Main.ass",
	"video_race_start_time" => 15.22,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "4wd Buggy Open A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-4wd Buggy Open A Main.ass",
	"video_race_start_time" => 34.05,
	"race_length" => 5,
);
$setupArr[] = Array(
	"race_name" => "1-8 E Buggy A Main",
	"car" => "",
	"output_filename" => "C:\\Users\\Nicholas\\Videos\\rc_temp\\{$date}-1-8 E Buggy A Main.ass",
	"video_race_start_time" => 26.565,
	"race_length" => 5,
);







/***
 * setup section ends
 */


foreach ($setupArr as $setup)
{


	$input = get_input_file_content($filename);
	$result_data = result_analyzer($input, $race_type);


	sub_generator($result_data, $setup["race_name"], $setup["video_race_start_time"], $setup["race_length"], $setup["output_filename"]);

}