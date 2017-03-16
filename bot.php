<?php
$access_token = 'LZArldUUHwHc6ROvqoAeGz5Kdft2ShdvagfCoiaoPaTpxqjvtA4ImaLk6hbkVguSX6pqlYaJFRB/pLt/q/Ct5w4chCz8hShgIVBOzZYuYM1YPHg8FJ0KS4G8GD3T0iFv7qAbmBvIfFYElhJ+MRgXtQdB04t89/1O/w1cDnyilFU=';

//condb
$db = pg_connect ("host=ec2-54-235-173-161.compute-1.amazonaws.com port=5432 dbname=davg135f89ndd9 user=nssvhpjghqgfui password=b32e79395f1d23198ce048097efca1604cf445e0fcabac8ae48f95b15ace1d81");
//
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data


if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			
			// Get text sent
			$text = $event['message']['text'];
			// Get replyToken
			$replyToken = $event['replyToken'];

			if ($text == "data"){
			//select//
			 $json_string = file_get_contents("http://api.wunderground.com/api/a6be6269233f1bc8/conditions/astronomy/q/TH/Bangkok.json");
  			 $parsed_json = json_decode($json_string);
 			 $date = $parsed_json->{'current_observation'}->{'local_time_rfc822'};
 			 $weather = $parsed_json->{'current_observation'}->{'weather'};
 			 $pressure = $parsed_json->{'current_observation'}->{'pressure_mb'};
			 
			$pushdate = pg_escape_string($date); 
  			$pushmaxtemp = pg_escape_string($weather); 
  			$pushmintemp = pg_escape_string($pressure); 
  			$query = ("INSERT INTO weather_proxima VALUES('$date', '$weather', '$pressure', '','');");
  			$result = pg_query($query);
			pg_close();
			//////////
			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => "Weather on ${date} \nis ${weather} and pressure is : ${pressure} "
			];
			}

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			echo $result . "\r\n";
		}
	}
}
echo "OK";
