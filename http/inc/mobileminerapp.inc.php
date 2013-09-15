<?php
/* MobileMinerApp Integration for MinePeon
 * @author tk1337 (me@tk1337.com)
 * @version 0.9a
 */

class mobileMinerApp{

  function __construct($argv=false){
    /* API Info for MobileMinerApp - ** DO NOT CHANGE ** */
    $this->minerName  = "MinePeon";
    $this->apiKey     = "NujIq2mbLN4L8P";
    $this->rootURL    = "https://api.mobileminerapp.com";
    
    /* Load options from minepeon.conf */
    $config           = json_decode(file_get_contents('/opt/minepeon/etc/minepeon.conf',false));
    date_default_timezone_set($config->userTimezone);
    
    /* check to see if MobileMinerApp module is enabled */
    if(@$config->mma_enabled === true){
      $this->moduleEnabled = true;
      /* check for email and appKey - exit if not found */
      if(@$config->userEmail && @$config->appKey){
        $this->userEmail  = $config->mma_userEmail;
        $this->appKey     = $config->mma_appKey;
        
        /* look for coinName setting, set default if not found */
        if(@$config->mma_coinName){
          $this->coinName = $config->mma_coinName;
        }else{
          $this->coinName = "Bitcoin"; 
        }
        
        /* look for coinSymbol setting, set default if not found */
        if(@$config->mma_coinSymbol){
          $this->coinSymbol = $config->mma_coinSymbol;
        }else{
          $this->coinSymbol = "BTC";
        }
        
        /* look for algorithm setting, set default if not found */
        if(@$config->mma_algorithm){
          $this->alogrithm  = $config->mma_algorithm;
        }else{
          $this->algorithm  = "SHA-256";
        }
        
        /* look for machineName setting, set default if not found */
        if(@$config->mma_machineName){
          $this->machineName= $config->mma_machineName;
        }else{
          $this->machineNAme = "MinePeon";
        }
        
        /* look for log setting, set default if not found */
        if(@$config->mma_cronLog){
          $this->cronLog    = $config->mma_cronLog;
        }else{
          $this->cronLog    = false;
        }
        
        /* look for interval setting, set default if not found */
        if(@$config->mma_checkInterval){
          $this->interval   = $config->mma_checkInterval;
        }else{
          $this->interval   = 20;
        }
        
        /* look for deviceType/kind setting, set default if not found */
        if(@$config->mma_deviceType){
          $this->kind   = $config->mma_deviceType;
        }else{
          $this->kind   = "PGA";
        }
      }else{
        exit;
      }
    }else{
      exit;
    }
  }
  
  
  /*
   * Gather information about the current statistics of MinePeon, then send format & send them to MMA's API.
   */
  public function updateStatus(){
    if($this->moduleEnabled === true){
      include_once '/opt/minepeon/http/inc/miner.inc.php';
      $mp = cgminer('devs',1);
      if(is_array($mp)){
        foreach($mp['DEVS'] as $device){
          $app[] = array(
            "MinerName"       => $this->minerName,
            "MachineName"     => $this->machineName,
            "Kind"            => $this->kind,
            "CoinSymbol"      => $this->coinSymbol,
            "CoinName"        => $this->coinName,
            "Algorithm"       => $this->alogrithm,
            "Index"           => $device['ID'], 
            "Enabled"         => true,
            "Status"          => $device['Status'],
            "Temperature"     => $device['Temperature'],
            "AverageHashrate" => $device['MHSav']*1000,
            "CurrentHashrate" => $device['MHS5s']*1000,
            "AcceptedShares"  => $device['Accepted'],
            "RejectedShares"  => $device['Rejected'],
            "HardwareErrors"  => $device['HardwareErrors'],
            "Utility"         => $device['Utility'],
            "Intensity"       => null,
          );
        }
        
        $statURL  ="/MiningStatisticsInput?emailAddress=".$this->userEmail."&applicationKey=".$this->appKey."&machineName=".$this->machineName."&apiKey=".$this->apiKey;
        $fullURL  = $this->rootURL.$statURL;
        $this->httpCall($fullURL,$app);
        return true;
      }
      return false;
    }
    return true;
  }
  
  
  /*
   * Build the URL to send to MMA's server, checking for any pending commands in queue.
   */
  public function checkRemoteCommand(){
    if($this->moduleEnabled === true){
      $cmdURL   = "/RemoteCommands?emailAddress=".$this->userEmail."&applicationKey=".$this->appKey."&machineName=".$this->machineName."&apiKey=".$this->apiKey;
      $fullURL  = $this->rootURL.$cmdURL;
      $this->httpCall($fullURL,NULL,"GET");
      return true;
    }
    return true;
  }
  
  
  /*
   * If there was a command found pending from the MMA, process said command, then remove it from the queue on MMA's server.
   */
  public function processCommand(){
    if($this->moduleEnabled === true){
      $command_data = $this->commandFound;
      $command_data = $command_data['0'];
      
      /* process command to CGMiner */
      if(in_array(strtoupper($command_data['CommandText']),array('STOP','START','RESTART'))){
        switch($command_data['CommandText']){
          case "STOP":
            exec('sudo systemctl disable cgminer');
            break;
          case "START":
            exec('sudo systemctl enable cgminer');
            break;
          case "RESTART":
            exec('sudo systemctl restart cgminer');
            //include_once '/opt/minepeon/http/inc/cgminer.inc.php';
            //cgminer('restart',true);
            break;
        }
      }
      
      $delURL   = "/RemoteCommands?emailAddress=".$this->userEmail."&applicationKey=".$this->appKey."&machineName=".$this->machineName."&commandId=".$command_data['Id']."&apiKey=".$this->apiKey;
      $fullURL  = $this->rootURL.$delURL;
      $this->__deleteCommand($fullURL);
    }
    return true;
  }
  
  
  /*
   * Execute an update to MMA servers.
   */
  public function cronUpdate(){
    if($this->moduleEnabled === true){
      if(!$this->interval){
        $this->interval = 20; // default 20sec
      }
      if($this->interval < 20){ //requests going too fast will hit throttle control.
        $this->interval  = 20;
      }elseif($this->interval > 60){ //requests going too slow will allow MobileMinerApp to think the machine is down.
        $this->interval = 60;
      }
      
      if($this->updateStatus()){
        if($this->cronLog){echo date('Y-m-d h:i:s')." - update statistics sent to server successfully.\r\n";}
        if($this->interval*2 < 60){ //the cron runs every minute so if the default check is slower than a minute, just ignore otherwise run again after sleep.
          sleep($this->interval);
          if($this->updateStatus()){
            if($this->cronLog){echo date('Y-m-d h:i:s')." - update statistics sent to server successfully.\r\n";}
            return true;
          }
        }else{
          return true;
        }
      }
      return false;
    }
    return true;
  }
  
  
  /*
   * Check MMA servers for incoming command.
   */
  public function cronCheck(){
    if($this->moduleEnabled === true){
      if(!$this->interval){
        $this->interval = 20; // default 20sec
      }
      if($this->interval < 20){ //requests going too fast will hit throttle control.
        $this->interval  = 20;
      }elseif($this->interval > 60){ //requests going too slow will allow MobileMinerApp to think the machine is down.
        $this->interval = 60;
      }
      
      if($this->checkRemoteCommand()){
        if($this->cronLog){echo date('Y-m-d h:i:s')." - checked server for incoming commands.\r\n";}
        if($this->interval*2 < 60){ //the cron runs every minute so if the default check is slower than a minute, just ignore otherwise run again after sleep.
          sleep($this->interval);
          if($this->checkRemoteCommand()){
            if($this->cronLog){echo date('Y-m-d h:i:s')." - checked server for incoming commands.\r\n";}
            return true;
          }
        }else{
          return true;
        }
      }
      return false;
    }
    return true;
  }
  
  
  /*
   * Simple little cURL function with some JSON decoding/encoding
   *
   * @return    null / object
   */
  private function httpCall($url,$data,$type="POST"){
    if($data){
      $data = json_encode($data);
    }

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    
    if($type == "POST"){
      curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/json'));
      curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
      curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }elseif($type == "GET"){
      curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
      curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
    }elseif($type == "DELETE"){
      curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
    }
    $result = curl_exec($ch);
    
    if($type == "GET" && $result && $result != "[]"){
      $this->commandFound   = json_decode($result,true);
      curl_close($ch);
      $this->processCommand();
    }else{
      curl_close($ch);
    }
  }
  
  
  /*
   * Made this as a separate function, as executing a cURL while still processing one, just turns out with negative results, so I broke it off
   * rather than calling the same function within itself.
   *
   * @param   string  $url  The url containing the response that a command has been processed so that MMA can remove it from it's queue.
   */
  private function __deleteCommand($url){
    $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
    $result = curl_exec($ch);
    curl_close($ch);
    // Later I will put in some error checking, to make sure an http 200/201 was returned, if so return a boolean.
  }

  
  /*
   * Just grab the tempature from the machine, even though this isn't really used - May consider setting this temp for all devices in the future.
   *
   * @return  int   This should always return the tempature as an integer rounded to the nearest hundredth or zero.
   */
  private function __getTempature(){
    $tmp  = trim(substr(substr(exec('/opt/vc/bin/vcgencmd measure_temp'),5),0,-2));
    if(is_numeric($tmp)){
      $tmp = round($tmp*9/5+32,2); //MobileMinerApp only supports degrees in fahrenheit.
      return $tmp;
    }
    return 0; //Call to API will fail if false or null, must be INT.
  }
}
?>