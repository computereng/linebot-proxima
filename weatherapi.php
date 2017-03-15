<?php
$json_string = file_get_contents("http://api.wunderground.com/api/aded58d2c14cec26/forecast10day/q/TH/Bangkok.json");
  $parsed_json = json_decode($json_string);
  $local_time_rfc822 = $parsed_json->{'current_observation'}->{'local_time_rfc822'};
  $location = $parsed_json->{'location'}->{'city'};
  $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
  echo "Current temperature in ${location} is: ${temp_c} on ${local_time_rfc822}";
