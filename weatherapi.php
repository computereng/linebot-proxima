<?php
$json_string = file_get_contents("http://api.wunderground.com/api/aded58d2c14cec26/geolookup/conditions/q/TH/Bangkok.json");
  $parsed_json = json_decode($json_string);
  $location = $parsed_json->{'location'}->{'city'};
  $temp_f = $parsed_json->{'current_observation'}->{'temp_f'};
  echo "Current temperature in ${location} is: ${temp_f}\n";
