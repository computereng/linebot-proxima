<?php
$json_string = file_get_contents("http://api.wunderground.com/api/aded58d2c14cec26/forecast10day/q/TH/Bangkok.json");
  $parsed_json = json_decode($json_string);
  print_r($parsed_json);
  //$local_date = $parsed_json->{'forecast'}->{'txt_forecast'};
//foreach($local_date['list'] as $forecastday => $title) {
  //$location = $parsed_json->{'location'}->{'city'};
  //$temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
  //echo "Current temperature in $forecastday['title'][";
//};
