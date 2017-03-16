<?php

//Connect to Line API
$access_token = 'LZArldUUHwHc6ROvqoAeGz5Kdft2ShdvagfCoiaoPaTpxqjvtA4ImaLk6hbkVguSX6pqlYaJFRB/pLt/q/Ct5w4chCz8hShgIVBOzZYuYM1YPHg8FJ0KS4G8GD3T0iFv7qAbmBvIfFYElhJ+MRgXtQdB04t89/1O/w1cDnyilFU=';

//Connect to Database
$db = pg_connect ("postgres://flghpbnnuhfevu:835ecb49bf0c74bc09716dbecdd8aa5df0ff7fa84bde3876dba031b27d632abf@ec2-75-101-142-182.compute-1.amazonaws.com:5432/d5mmu71c2lbm9o");
			//echo $resultsql;

//
//Get data from line api
$content = file_get_contents('php://input');
//Decode json to php
$events = json_decode($content, true);


//ถ้ามีการรับค่าจาก line api
if (!is_null($events['events'])) {
	//วนลูปการทำงาน
	foreach ($events['events'] as $event) {
		// ตรวจสอบเงื่อนไขว่าค่าที่รับมาเป็นข้อความหรือไม่
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			
			// เก็บข้อความที่รับมาในตัวแปร text
			$text = $event['message']['text'];
			// เก็บค่า Token [ข้อมูลยืนยันตนของ line] ในตัวแปร replytoken
			$replyToken = $event['replyToken'];
			
			//ถ้าข้อความที่ส่งมาคือ "คำสั่ง"
			if ($text == "คำสั่ง"){
				//เก็บข้อความไว้ในตัวแปร output
				$output = "========คำสั่งทั้งหมด=========\n|  weather  | เช็คสภาพอากาศปัจจุบัน\n|   history   | ดูประวัติการเช็คสภาพอากาศ\n|clearhistory| ล้างประวัติ\n========================";
			}
			//ถ้าข้อความที่ส่งมาคือ "data"
			if ($text == "weather"){
			 //รับข้อมูล รูปแบบ Json จากเว็บพยากรอากาศ
			 $json_string = file_get_contents("http://api.wunderground.com/api/a6be6269233f1bc8/conditions/astronomy/q/TH/Bangkok.json");
  			// Decode json
			$parsed_json = json_decode($json_string);
			//นำค่าจากข้อมูล Json ในเซ็ตของ current_observertion{local_time_rfc822} เก็บไว้ในตัวแปร อ่านค่าเซ็นได้จาก url ด้านบน
 			 $date = $parsed_json->{'current_observation'}->{'local_time_rfc822'};
			 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};	
 			 $weather = $parsed_json->{'current_observation'}->{'weather'};
 			 $pressure = $parsed_json->{'current_observation'}->{'pressure_mb'};
			 
			//นำค่าพยาการอากาศที่ได้ เตรียมส่งขึ้น Database
			$pushdate = pg_escape_string($date); 
			$pushtemp = pg_escape_string($temp_c);
  			$pushweather = pg_escape_string($weather); 
  			$pushpressure = pg_escape_string($pressure); 
			//นำข้อมูลที่จะเก็บไส้ใน Database ใส่่ในตัสแปร query
  			$query = ("INSERT INTO weather_botline_proxima VALUES('$pushdate', '$pushtemp', '$pushweather', $pushpressure,'','');");
  			//ส่งค่าไปยัง Database โดยเก็บไว้ในตาราง weather_botline_proxima
			$result = pg_query($query);
				//เก็บข้อความไว้ในตัวแปร output
				$output = "Weather on\n ${date} \n=======================\nTemp is: ${temp_c}C \nWeather is:  ${weather} \nPressure is :  ${pressure}\n======================= ";
			}
			if ($text == "history"){
			//นำคำสั่งที่จะใช้เก็บไว้ในตัวแปร query
			 $query = "SELECT * FROM weather_botline_proxima"; 
			//ทำการดึงข้อมูลจาก Database ใน table weather_botline_proxima	
       			 $result = pg_query($query); 
			 $output = "  -:-History Get Weather-:-\n=======================\n";
           		 //ทำการนำข้อมูลออกมา โดยเรียงจากแถว บนสุดลงล่าง
			while($myrow = pg_fetch_assoc($result)) { 
				//เก็บไว้ในตัวแปร output
              			$output = $output."Weather on : ".$myrow['date']."\nTemp is : ".$myrow['tempc']."\nWeather is : ".$myrow['weather']."\nPressure is : ".$myrow['pressure']."\n=======================\n";
       			 } 
			}
			if ($text == "clearhistory"){
			
			 $query = "DELETE FROM weather_botline_proxima"; 
			//ทำการเคลียข้อมูลทั้งในใน table weather_botline_proxima
       			 $result = pg_query($query);
				$output = "ทำการลบประวัติเรียบร้อยแล้ว";
			}
			//เตรียมข้อความที่จะส่งกลับไว้ในตัวแปร อาเรย์ messege
			$messages = [
				'type' => 'text',
				'text' => $output
			];
			//ยกเลิกการ Connect Database
			pg_close();
			//เก็บค่า link ของไลน์bot
			$url = 'https://api.line.me/v2/bot/message/reply';
			//รวมข้อมูลทั้งหมดไว้ใน อาเรย์ data เตรียมเข้ารหัสเป็น Json
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			//เข้ารหัสเป็น Json เพื่อเตรียมส่งกลับไปใน line
			$post = json_encode($data);
			//สร้าง Header ในการส่งสำหรับ line
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
			// ทำการส่งค่าที่ทำการ เข้ารหัสเป็น Json ไปยังแชทไลน์
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
