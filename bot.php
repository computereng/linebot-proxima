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
			$messages = [
				'type' => 'text',
				'text' => $output
			];
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];	
			}
			//ถ้าข้อความที่ส่งมาคือ "data"
			if ($text == "weather"){
			 $query = "SELECT * FROM weather_botline "; 
				$result = pg_query($query); 
				while($myrow = pg_fetch_assoc($result)) { 
					$output = "Weather on : ".$myrow['date']."\nTemp is : ".$myrow['tempc']."\nWeather is : ".$myrow['weather']."\nPressure is : ".$myrow['pressure']."\nHumidity is : ".$myrow['humidity'];					
					$imagename = $myrow['image'];
				} 
				
				//////////
				// Build message to reply back
				$messages = [
					'type' => 'text',
					'text' => $output
				];
				$image = [
					'type' => 'image',
					"originalContentUrl" => "https://raw.githubusercontent.com/computereng/linebot-proxima/master/pic/".$imagename.".jpg",
					"previewImageUrl" => "https://raw.githubusercontent.com/computereng/linebot-proxima/master/pic/".$imagename.".jpg"
				];
				$data = [
					'replyToken' => $replyToken,
					'messages' => [$messages, $image],
				];
				pg_close();
			}
			if ($text == "history"){
			//นำคำสั่งที่จะใช้เก็บไว้ในตัวแปร query
			 $query = "SELECT * FROM weather_botline"; 
			//ทำการดึงข้อมูลจาก Database ใน table weather_botline_proxima	
       			 $result = pg_query($query); 
			 $output = "  -:-History Get Weather-:-\n=======================\n";
           		 //ทำการนำข้อมูลออกมา โดยเรียงจากแถว บนสุดลงล่าง
			while($myrow = pg_fetch_assoc($result)) { 
				//เก็บไว้ในตัวแปร output
              			$output = $output."Weather on : ".$myrow['date']."\nTemp is : ".$myrow['tempc']."\nWeather is : ".$myrow['weather']."\nPressure is : ".$myrow['pressure']."\nHumidity : ".$myrow['Humidity']."\n=======================\n";
       			 } 
			$messages = [
				'type' => 'text',
				'text' => $output
			];
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];	
			pg_close();
			}
			if ($text == "clearhistory"){
			
			 $query = "DELETE FROM weather_botline"; 
			//ทำการเคลียข้อมูลทั้งในใน table weather_botline_proxima
       			 $result = pg_query($query);
				$output = "ทำการลบประวัติเรียบร้อยแล้ว";
			$messages = [
				'type' => 'text',
				'text' => $output
			];
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			pg_close();
			}
			//เตรียมข้อความที่จะส่งกลับไว้ในตัวแปร อาเรย์ messege
			
			//ยกเลิกการ Connect Database
			//เก็บค่า link ของไลน์bot
			$url = 'https://api.line.me/v2/bot/message/reply';
			//รวมข้อมูลทั้งหมดไว้ใน อาเรย์ data เตรียมเข้ารหัสเป็น Json
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

			//echo $result . "\r\n";
		}
	}
}
echo "OK";
