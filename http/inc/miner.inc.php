<?php


function miner($command, $parameter) {

        $command = array (
                "command"  => $command,
                "parameter" => $parameter
        );

        $jsonCmd = json_encode($command);

        $host = "127.0.0.1";
        $port = 4028;

        $client = @stream_socket_client("tcp://$host:$port", $errno, $errorMessage);

        if ($client === false) {
                return false;
        }
        fwrite($client, $jsonCmd);
        $response = stream_get_contents($client);
        fclose($client);
        $response = preg_replace("/[^[:alnum:][:punct:]]/","",$response);
        $response = json_decode($response, true);
        return $response;

}

function promotePool($addr, $user){
  $pools = cgminer('pools','')['POOLS'];
  $pool = 0;
  // echo "changeing";
  foreach ($pools as $key => $value) {
    if(isset($value['User']) && $value['URL']==$addr && $value['User']==$user){
	  // echo "found";
	  cgminer('switchpool',$pool);
    }
	$pool = $pool + 1;
  }
  
}
