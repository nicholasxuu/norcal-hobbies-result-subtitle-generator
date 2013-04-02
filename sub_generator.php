<?php

require_once("core.php");
require_once("result_analyzer.php");

/***
 * subtitle creation
 */

$race_name = "Sportsman 17.5 Buggy A Main                                   Round# 3, Race# 6";
$racer_number = 7;
$init_time = 3.7;
$output_file_name = "C:\\Users\\Nicholas\\Videos\\RC\\20130327-TLR22-17.5TBuggy-SP-MAH00249.ass";
$input = $sample_input_5;

$total_data = result_analyzer($input);
$ass_output = get_personal_ass_content($total_data, $race_name, $racer_number, $init_time);


$fp = fopen($output_file_name, "w");
fwrite($fp, $ass_output);
fclose($fp);