<?php

$url = 'http://www.forbestproperties.co.th/amphoelist.php?province=';
$destination = 'province.txt';
$id = 1;
$handle = fopen($destination, "w");
while($id<=77) {

    $source = $url . sprintf("%02d",$id);
    $data = file_get_contents($source);
    $a = array('option','value','กรุณาเลือก','...','<','/',"'",'=');
    $result = str_replace(">"," ",str_replace($a,"",$data));
    fwrite($handle, $result);
    echo $id;
    $id++;
}
fclose($handle);