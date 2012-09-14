<?php

$groupName = $_REQUEST['groupName'];

#connection data
$username = 'iolabtrailhunter';
$pass = 'bobglushko';
$key = 'oeWKP--W6XeYKRHrgeKVvzBUzhucluq_ZBdxbKlhupU=';
$url = 'http://feeds.delicious.com/v2/json/' . $username;   //.'?private=' . $key; this will be required when we make the bookmarks private

#connect to delicious
$jsonBookmarks = file_get_contents($url);
$pBookmarks = json_decode($jsonBookmarks);


$hints = array();
	foreach($pBookmarks as $mark) {
		$hint = $mark->n;
		$id = splitUp(':', $mark->t[1], 1);
		$item = array('id'=> $id, 'hint' => $hint, 'completed' => false);
		$hints[] = $item;
	}

	//if no group  name is supplied we send back a default list with the hints
if (empty($groupName)) {
	print(json_encode($hints));	
}
else {
	foreach($pBookmarks as $mark) {  //get the status for a particular group

		for ($i = 2; $i < count($mark->t); $i++) {
			
			$userTags = explode('|', $mark->t[$i]);
			$group = splitUp(':', $userTags[1], 1);

			if ($group == $groupName) {
				$stepNumber = splitUp(':', $userTags[0], 1);
				$time = splitUp(':', $userTags[2], 1);
				
			
				foreach($hints as $key=>$val) {
					if  ($val['hint']== $mark->n) {
						 $hints[$key]['completed'] = true;
						 $hints[$key]['step'] = $stepNumber;
						 $hints[$key]['time'] = $time;
					}
			}
		}
	}
}
print(json_encode($hints));
			
}

function splitUp($separator, $item, $index) {
	$items = explode($separator, $item);
	return $items[$index];                    
}

?>