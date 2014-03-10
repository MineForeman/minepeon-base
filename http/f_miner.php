<?php

header('Content-type: application/json');
require('miner.inc.php');

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

miner($command["command"], $command['parameter']);


$r['success'] = "true";
$r['command'] = $command;
echo json_encode($r);
?>
