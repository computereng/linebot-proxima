<?php
$db = pg_connect ("postgres://flghpbnnuhfevu:835ecb49bf0c74bc09716dbecdd8aa5df0ff7fa84bde3876dba031b27d632abf@ec2-75-101-142-182.compute-1.amazonaws.com:5432/d5mmu71c2lbm9o");
    //$selectfields = array("imgid" => "");
     $query = "SELECT * FROM weather_botline_proxima"; 
        $result = pg_query($query); 
        if (!$result) { 
            echo "Problem with query " . $query . "<br/>"; 
            echo pg_last_error(); 
            exit(); 
        } 
            while($myrow = pg_fetch_assoc($result)) { 
            echo "Weather on : "$myrow['date']."<br>Temp is : ".$myrow['tempc']."<br>Weather is : ".$myrow['weather']."<br>Pressure is : ".$myrow['pressure'];
        } 
//<td>%s</td><td>%s</td><td>%s</td>   , htmlspecialchars($myrow['tempc']), htmlspecialchars($myrow['weather']), htmlspecialchars($myrow['pressure'])
pg_close();
?>
