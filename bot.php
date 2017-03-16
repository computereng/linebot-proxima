<?php
$access_token = 'SiZyVVTPIPP4Qn9VwKKKCI0YA3yjbfpk/mjb4Az4bbnrd275417q/2+JV0XGZca29KQ1F0S1Gh4Tx3DC8mLjQYGnbVLsJzmI2AA7kRlq+983S/bm6h0u4bsEu4Iyb6sl2E8PQnm7d0wguJ3kz6pEhwdB04t89/1O/w1cDnyilFU=';
//condb
$db = pg_connect ("postgres://krdookwgbudwkq:337d29bb2b87f471b47f286fcb7fa1fb885b4b063f9ea5197805f4f679e7d9b8@ec2-54-221-255-153.compute-1.amazonaws.com:5432/dd6j72nr8uanuq");
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
				 $temp = $parsed_json->{'current_observation'}->{'temp_c'};
				 $weather = $parsed_json->{'current_observation'}->{'weather'};
				 $pressure = $parsed_json->{'current_observation'}->{'pressure_mb'};
				 
				$pushdate = pg_escape_string($date);
				$pushtemp = pg_escape_string($temp); 			
				$pushweather = pg_escape_string($weather); 
				$pushpressure = pg_escape_string($pressure); 
				$pushhum = pg_escape_string($pressure); 
				$pushurl = pg_escape_string($pressure); 
				$query = ("INSERT INTO WEATHER_HUMIDITY VALUES('$pushdate', '$pushtemp', '$pushweather', '$pushpressure', '$pushhum', '$pushurl');");
				$result = pg_query($query);
				pg_close();
				//////////
				// Build message to reply back
				//$messages = [
				//	'type' => 'text',
				//	'text' => "Weather on\n ${date} \n=======================\nTemperature is:  ${temp} \nWeather is:  ${weather} \nPressure is :  ${pressure}\n======================="
				//];
				$messages = [
					'type' => 'template',
					'altText' => 'this is a buttons template',
					'template' => ['type' => "buttons", 'thumbnailImageUrl' => "https://linebot-obsidian.herokuapp.com/pic/test.jpg", 'title' => "Menu", 'text' => "Weather on\n ${date} \n=======================\nTemperature is:  ${temp} \nWeather is:  ${weather} \nPressure is :  ${pressure}\n======================="]
				];
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
}
echo "OK";