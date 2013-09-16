<?php
/* MobileMinerApp Integration for MinePeon
 * @author tk1337 (me@tk1337.com)
 * @version 0.1a
 */

include '/opt/minepeon/http/inc/mobileminerapp.inc.php';
$mma  = new mobileMinerApp();

if(@$argv[1]){
  if($argv[1] == "update"){
    $mma->cronUpdate();
  }elseif($argv[1] == "check"){
    $mma->cronCheck();
  }
}else{
  exit("No valid arguments were passed. Must be either update or check.");
}
?>