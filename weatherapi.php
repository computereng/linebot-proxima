<?php
$dd = 15;

for($cdd = 1; $cdd <= 5; $cdd++){
  $json_string = file_get_contents("http://api.wunderground.com/api/a6be6269233f1bc8/history_201703".$dd."/q/TH/Bangkok.json");
  $parsed_json = json_decode($json_string);
  $date = $parsed_json->{'history'}->{'dailysummary'}[0]->{'date'}->{'pretty'};
  $tzname = $parsed_json->{'history'}->{'dailysummary'}[0]->{'date'}->{'tzname'};
  $maxtempm = $parsed_json->{'history'}->{'dailysummary'}[0]->{'maxtempm'};
  $mintempm = $parsed_json->{'history'}->{'dailysummary'}[0]->{'mintempm'};
  echo "Current temperature in ${tzname} maxtemp is: ${maxtempm} mintemp is: ${mintempm} on: ${date} <br>";

 $db = pg_connect ("postgres://iombvspzcccped:98bb14b78cf29ce3da93a7f502e3a0988eb83d9918d8e00c236b5f091a4fbe86@ec2-54-225-119-223.compute-1.amazonaws.com:5432/d9ittrcjc990n6");
  $pushdate = pg_escape_string($date); 
  $pushmaxtemp = pg_escape_string($maxtempm); 
  $pushmintemp = pg_escape_string($mintempm); 
  $pushlocation = pg_escape_string($tzname);
  $query = ("INSERT INTO weather_obsidian VALUES('$pushdate', '$pushmaxtemp', '$pushmintemp', '$pushlocation');");
  $result = pg_query($query);

  echo 'Send Data successfully';
  pg_close(); 
  $dd--;
}
///
//CREATE TABLE WEATHER_HUMIDITY (DATE VARCHAR(30), TEMP VARCHAR(10), WEATHER VARCHAR(20), AIR_P VARCHAR(10), HUM VARCHAR(10), PIC VARCHAR(30));
?>

