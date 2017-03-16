<?php
$html = new DOMDocument();
$html->loadHTMLFile('testh.html');
$id2 = $html->getElementById('data_name2');
print $id2"\n";
?>
