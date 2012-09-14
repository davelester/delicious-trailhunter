<?php

$id = $_REQUEST['id'];

if (empty($id)) {
		die('No id specified');
}
else {
#connection data
$username = 'iolabtrailhunter';
$pass = 'bobglushko';
$key = 'oeWKP--W6XeYKRHrgeKVvzBUzhucluq_ZBdxbKlhupU=';
$url = 'http://feeds.delicious.com/v2/json/' . $username;   //.'?private=' . $key; this will be required when we make the bookmarks private

$jsonBookmarks = file_get_contents($url);
$pBookmarks = json_decode($jsonBookmarks);

$latLong = Array();
	foreach($pBookmarks as $mark) {
		$currId = explode(':', $mark->t[1]);
		$currId = $currId[1];
		if ($currId == $id) {
			$coords = explode(':', $mark->t[0]);		//sigh...php
			$coords = $coords[1];
			$coords = explode('|', $coords);
			$latLong['lat'] = $coords[0];
			$latLong['long'] = $coords[1];
			break;
		}
	}
}

print(json_encode($latLong));
?>