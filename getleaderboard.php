<?php

#connection data
$username = 'iolabtrailhunter';
$pass = 'bobglushko';
$key = 'oeWKP--W6XeYKRHrgeKVvzBUzhucluq_ZBdxbKlhupU=';
$url = 'http://feeds.delicious.com/v2/json/' . $username;   //.'?private=' . $key; this will be required when we make the bookmarks private

#connect to delicious
$jsonBookmarks = file_get_contents($url);
$pBookmarks = json_decode($jsonBookmarks);

	$leaderboard = Array();
	
	foreach($pBookmarks as $mark) {  //cycle through each bookmark

		$hint = $mark->n;
		$id = splitUp(':', $mark->t[1], 1);
		$completionStatus = Array();
		
		for ($i = 2; $i < count($mark->t); $i++) { //cycle through each dynamically created tag on the bookmark
			
			$userTags = explode('|', $mark->t[$i]);
			$group = splitUp(':', $userTags[1], 1);
			$stepNumber = splitUp(':', $userTags[0], 1);
			$time = splitUp(':', $userTags[2], 1);
			
			$groupStatus = Array( 'group' => $group, 'stepNumber' => $stepNumber, 'time' => $time );
			$completionStatus[] = $groupStatus;
		}
		
		$item = Array('hint' => $hint, 'id' => $id, 'status' => $completionStatus);
		$leaderboard[] = $item;
		
	}
	
print(json_encode($leaderboard));
			

function splitUp($separator, $item, $index) {
	$items = explode($separator, $item);
	return $items[$index];                    
}

?>