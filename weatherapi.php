<?php
  $json_string = file_get_contents("http://api.wunderground.com/api/aded58d2c14cec26/forecast10day/q/TH/Bangkok.json");
  $parsed_json = json_decode($json_string);
  $location = $parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'};
  //$temp_f = $parsed_json->{'current_observation'}->{'temp_f'};
  echo ${location[0]}";
?>

