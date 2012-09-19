<?php

include('delicious_proxy.php');
$trailName = $_REQUEST['trail'];


#connection data
$bundleUrl = 'https://api.del.icio.us/v1/json/tags/bundles/all';
$bookmarkUrl = 'http://feeds.delicious.com/v2/json/';

$proxy = new DeliciousProxy($bookmarkUrl);
$pBookmarks = $proxy->public_get(false);

$proxy->set_url($bundleUrl);
$pBundles = $proxy->authenticated_get();

$hints = array();
foreach($pBookmarks as $mark) {
		$hint = $mark->n;
		$id = splitUp(':', $mark->t[1], 1);
		$item = array('id'=> $id, 'hint' => $hint);
		$hints[] = $item;
	}
	

if (empty($trailName)) { 	    //skip all this if we only we want the blank list...
	print(json_encode($hints));
}	
else {
	foreach($pBundles['bundles'] as $bundle) {  //get the status for a particular group
		$tagString = $bundle['bundle']['tags'];
		$name = $bundle['bundle']['name'];
		$tags = explode(' ', $tagString); // tags in tag bundles are separated by a space
		
		for ($i = 1; $i < count($tags); $i++) { //the first tag is the location name, the rest are user-added
			
			$userData = explode('|', $tags[$i]);
			$trail = splitUp(':', $userData[1], 1);
			$stepNumber = splitUp(':', $userData[0], 1); 
			$time = splitUp(':', $userData[2], 1);
			
			if ($trailName == $trail ) { //trail(group) name was provided so we only get their data

				foreach($hints as $key=>$val) {		//if there is a tag for the current trail/group, add the stepnumber and time to the status array
					if  ($val['id']== $name) {
						 $hints[$key]['completed'] = true;
						 $hints[$key]['step'] = $stepNumber;
						 $hints[$key]['time'] = $time;
					}
				}
			}
			else if (strtolower($trailName) == 'all')  { //build leaderboard for all groups
					foreach($hints as $key=>$val) {	
						if ($hints[$key]['status'] == null) {
							$hints[$key]['status'] = Array();
						}
						if  ($val['id']== $name) {
							$groupStatus = Array( 'trail' => $trail, 'stepNumber' => $stepNumber, 'time' => $time );	
							$hints[$key]['status'][] = $groupStatus;
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