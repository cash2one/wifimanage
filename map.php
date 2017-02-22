<?php
$db = D('shop');
$sql=" select * from wifi_shop";
$info = $db->query($sql);
$count = $db->execute($sql);
for($i=0;$i<$count;$i++){
    //$data.= "[".$info[$i]['lng'].",".$info[$i]['lat']."]".",";
    $data.= "lng: ".$info[$i]['lng'].",lat: ".$info[$i]['lat'].",shopName: ".$info[$i]['shopname'].",address: ".$info[$i]['address'].",";
}

$file = './map-data.js';
$data = 'var data = {"data":[{'.$data.'}]}';
file_put_contents($file, $data, LOCK_EX);