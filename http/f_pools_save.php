<?php
/*
f_pools_save saves the pools data
returns success, bytes written and new pool data
this file should be called f_pools.php and should be built like f_settings.php
*/
header('Content-type: application/json');

// Check for POST or GET data
if (empty($_REQUEST['saving']) or !$_REQUEST['saving']) {
	echo json_encode(array('success' => false, 'debug' => "Not saving"));
	exit;
}

//initialize a limit to the number of pools that are added to the miner config file. is there an official limit?
$poolLimit = 20;

// Loop through all rows, stop after 3 empty rows or if poolLimit is exceeded, process the POST or GET data
$e = 0;
for($i=0;$i<$poolLimit || $e < 3;$i++) {
	if(!empty($_REQUEST['URL'.$i]) and !empty($_REQUEST['USER'.$i])){

		// Set pool data
		// Avoid empty pool passwords because it might be problematic if used in a command
		$dataPools[] = array(
			"url" => $_REQUEST['URL'.$i],
			"user" => $_REQUEST['USER'.$i],
			"pass" => empty($_REQUEST['PASS'.$i])?"none":$_REQUEST['PASS'.$i]
			);

		// reset empty
		$e = 0;
	}
	else{
		// increment empty count
		$e++;
	}

	// debug output
	// echo $_REQUEST['URL'.$i.''] . $_REQUEST['USER'.$i.''] . $_REQUEST['PASS'.$i.''];
}

$written = 0;

// Recode into JSON and save
// Never save if no pools given
if (!empty($dataPools)) {
	// Read current config, prefer miner.user.conf
	if(file_exists("/opt/minepeon/etc/miner.user.conf")){
		$data = json_decode(file_get_contents("/opt/minepeon/etc/miner.user.conf", true), true);
	}
	else{
		$data = json_decode(file_get_contents("/opt/minepeon/etc/miner.conf", true), true);
	}
	// Unset currect
	unset($data['pools']);
	// Set new pool data
	$data['pools']=$dataPools;
	// Write back to file
	$written = file_put_contents("/opt/minepeon/etc/miner.conf", json_encode($data, JSON_PRETTY_PRINT));
	$written = file_put_contents("/opt/minepeon/etc/miner.user.conf", json_encode($data, JSON_PRETTY_PRINT));
}

echo json_encode(array('success' => true, 'written' => $written, 'pools' => $dataPools));
?>