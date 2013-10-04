<?php
/*
f_copy issues a command to the api of the miner
returns success, command, miner response and possible errors
*/
header('Content-type: application/json');

// Check for POST or GET data
if (empty($_REQUEST['command'])) {
	echo json_encode(array('success' => false, 'debug' => "No command given"));
	exit;
}

$command["command"] = $_REQUEST['command'];

// Check for parameters
if (!empty($_REQUEST['parameter'])) {
	$command['parameter']=$_REQUEST['parameter'];
}

// Prepare socket
$host = "127.0.0.1";
$port = 4028;

// Setup socket
$client = stream_socket_client("tcp://$host:$port", $errno, $errorMessage);

// Socket failed
if ($client === false) {
	$r['error']=$errno." ".$errorMessage;
}
// Socket success
else{
	fwrite($client, json_encode($command));
	$response = stream_get_contents($client);
	fclose($client);

	// Cleanup json
	$response = preg_replace("/[^[:alnum:][:punct:]]/","",$response);

	// Add api response
	$r = json_decode($response, true);
}

$r['success'] = ($client === false);
$r['command'] = $command;
echo json_encode($r);
?>