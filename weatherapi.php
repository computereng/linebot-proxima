<?php
$city="Bangkok";
$country="TH"; 
$url="http://api.openweathermap.org/data/2.5/forecast/daily?q=".$city.",".$country."&units=metric&cnt=7&lang=en&appid=c0c4a4b4047b97ebc5948ac9c48c0559";
$json=file_get_contents($url);
$data=json_decode($json,true);
$data['city']['name'];
?>

