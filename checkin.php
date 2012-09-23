<?php
include('delicious_proxy.php');
$trailName = $_REQUEST['trail'];
$locationId = $_REQUEST['id'];
$lat = $_REQUEST['lat'];
$long = $_REQUEST['long'];
$date = new DateTime();
$time = $date->getTimestamp();

if (empty($trailName) || empty($locationId) || empty($lat) || empty($long)) {
print('Error: must supply trail, id, lat, long');
die();
}

$bookmarkUrl = 'http://feeds.delicious.com/v2/json/';
$bundleUrl = 'https://api.del.icio.us/v1/json/tags/bundles/all';
$bundleSetUrl = 'https://api.del.icio.us/v1/tags/bundles/set?';
$bundleGetUrl = 'https://api.del.icio.us/v1/json/tags/bundles/all?';

$proxy = new DeliciousProxy($bookmarkUrl);
$pBookmarks = $proxy->public_get(false);

//scan through bookmarks and find the one matching the posted id
$correctLat = 0;
$correctLong = 0;
$locationName = "";

	foreach ($pBookmarks as $mark) {
			$id = splitUp(':', $mark->t[0], 1); //id is first tag on the bookmark
			if ($locationId == $id) 
			{	//coordinates are second tag
				$locationName = $mark->d;
				$coords = explode(':', $mark->t[1]);		
				$coords = $coords[1];
				$coords = explode('|', $coords);
				$correctLat = $coords[0];
				$correctLong = $coords[1];
				break;
			}
	}
$res = true;
$metersAway = haversineGreatCircleDistance($lat, $long, $correctLat, $correctLong);
$temp = getTemp($metersAway);

if ($metersAway > 30.48) {		//has to be within 100 ft
	$res = false;
}
if (!$res) {
	$arr =  Array('valid'=> $res, 'temp'=> $temp);
	print(json_encode($arr));
}
else {
$proxy->set_url($bundleUrl);
$pBundles = $proxy->authenticated_post();

	//find out what the group's next step # is
	$stepNumber = 0;
	
	foreach($pBundles['bundles'] as $bundle) {  
		$tagString = $bundle['bundle']['tags'];
		$name = $bundle['bundle']['name'];
		$tags = explode(' ', $tagString); // tags in tag bundles are separated by a space
		
		for ($i = 1; $i < count($tags); $i++) { //the first tag is the location name, the rest are user-added
			$userData = explode('|', $tags[$i]);
			$trail = splitUp(':', $userData[1], 1);
			if ($trail == $trailName) {
				$step = splitUp(':', $userData[0], 1); 
				preg_match('/[0-9]/', $step, $matches); //get just the digit
				if (intval($matches[0]) > $stepNumber) {
					$stepNumber = intval($matches[0]);
				}
			}
		}
	}
	//get current bundle.
	$proxy->set_url($bundleGetUrl);
	$data = 'bundle=' . $locationId;
	$currentBundle = $proxy->authenticated_post($data);
	$currentTags = str_replace(" ",",", $currentBundle['bundles'][0]['bundle']['tags']);
	
	//update the bundle.
	$newTag = 'step:step' . ($stepNumber + 1) . '|trail:' . $trailName . '|time:' . $time;
	$data = 'bundle=' . $locationId . '&tags=' . $currentTags . ',' . $newTag;
	$proxy->set_url($bundleSetUrl);
	$result = $proxy->authenticated_post($data);

	//return to client
	$arr =  Array('valid'=> $res, 'locationName' => $locationName, 'step'=> ($stepNumber + 1));
	print(json_encode($arr));
}

function splitUp($separator, $item, $index) {
	$items = explode($separator, $item);
	return $items[$index];                    
}



/**
source: http://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
*/
function haversineGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}

function getTemp($distance) {

if ($distance > 30.48 && $distance <= 60.96) { //200ft
	return "hot";
}
if ($distance > 60.96 && $distance <= 121.92) { //400ft
	return "warm";
}
if ($distance > 121.92 && $distance <= 182.88) { //600ft
	return "tepid";
}
if ($distance > 182.88 && $distance <= 243.84) { //800ft
	return "cold";
}
return "freezing";
}

?>

