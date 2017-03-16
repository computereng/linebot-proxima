<?php
$db = pg_connect ("postgres://flghpbnnuhfevu:835ecb49bf0c74bc09716dbecdd8aa5df0ff7fa84bde3876dba031b27d632abf@ec2-75-101-142-182.compute-1.amazonaws.com:5432/d5mmu71c2lbm9o");
    //$selectfields = array("imgid" => "");
    $records = pg_select($db,"mmsfiles");
    print_r($records);
pg_close();
?>
