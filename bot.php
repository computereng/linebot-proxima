<?php
$access_token = 'SiZyVVTPIPP4Qn9VwKKKCI0YA3yjbfpk/mjb4Az4bbnrd275417q/2+JV0XGZca29KQ1F0S1Gh4Tx3DC8mLjQYGnbVLsJzmI2AA7kRlq+983S/bm6h0u4bsEu4Iyb6sl2E8PQnm7d0wguJ3kz6pEhwdB04t89/1O/w1cDnyilFU=';

//condb
$db = pg_connect ("postgres://iombvspzcccped:98bb14b78cf29ce3da93a7f502e3a0988eb83d9918d8e00c236b5f091a4fbe86@ec2-54-225-119-223.compute-1.amazonaws.com:5432/d9ittrcjc990n6");
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

			if ($text == "เหนื่อยไหม"){
			//select//
				
			//////////
			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => "สภาพอากาศวันนี้"
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
