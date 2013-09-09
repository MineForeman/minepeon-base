<?php
/*
api/copy issues a command to the api of the miner
returns success, command, miner response and possible errors
*/
header('Content-type: application/json');

include('cgminer.inc.php');

if (empty($_REQUEST['command']) && empty($_REQUEST['parameter'])) {
  $r = cgminer();
}
elseif(empty($_REQUEST['parameter'])){
  $r = cgminer($_REQUEST['command']);
}
else{
  $r = cgminer($_REQUEST['command'],$_REQUEST['parameter']);
}

echo json_encode($r);
?>