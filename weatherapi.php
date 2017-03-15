<?php
$dd = 15;
$cdd = 1;
for($cdd <= 5; $cdd++){
  $json_string = file_get_contents("http://api.wunderground.com/api/a6be6269233f1bc8/history_201703".$dd."/q/TH/Bangkok.json");
  $parsed_json = json_decode($json_string);
  $location = $parsed_json->{'location'}->{'city'};
  $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
  $local_time_rfc822 = $parsed_json->{'current_observation'}->{'local_time_rfc822'};
  echo "Sussecc Current temperature in ${location} is: ${temp_c} on: ${dd}\n ";
  $dd--;
}

/* $db = pg_connect ("host=ec2-54-235-173-161.compute-1.amazonaws.com port=5432 dbname=davg135f89ndd9 user=nssvhpjghqgfui password=b32e79395f1d23198ce048097efca1604cf445e0fcabac8ae48f95b15ace1d81");
if (!$db) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';
mysql_close($link);  
$date = pg_escape_string($_POST['local_time_rfc822']); 
  $temp = pg_escape_string($_POST['temp_c']); 
  $location = pg_escape_string($_POST['location']);
  $query = ("INSERT INTO weather_api VALUES('$date', '$temp', '$location')");
  $result = pg_query($query);
  if (!$result) { 
            $errormessage = pg_last_error(); 
            echo "Error with query: " . $errormessage; 
            exit(); 
   }  
  pg_close(); */
  
///
 
?>

