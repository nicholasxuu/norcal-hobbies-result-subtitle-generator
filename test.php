<?php


$version_str = "Version Information: APIVersion=2.7.0.4939;APIRelease=2013Q2.0;GKRVersion=1";

$version = array();
		$temp = explode(":", $version_str);
		$temp[1] = trim($temp[1]);
		$sect_arr = explode(";", $temp[1]);
		foreach ($sect_arr as $sect) {
			$part_arr = explode("=", $sect);
			$version[$part_arr[0]] = $part_arr[1];
		}
echo var_dump($version);