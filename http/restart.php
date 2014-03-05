<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>Restarting Miner</title>
<script type="text/javascript">
var start = new Date();
start = Date.parse(start)/1000;
var seconds = 15;
function CountDown(){
    var now = new Date();
    now = Date.parse(now)/1000;
    var counter = parseInt(seconds-(now-start),10);
    document.getElementById('countdown').innerHTML = counter;
    if(counter > 0){
        timerID = setTimeout("CountDown()", 100)
    }else{
        location.href = "/"
    }
}
window.setTimeout('CountDown()',100);
</script>
  </head>
  <body>
  <center>
  <p><h1>Restarting Miner</h1></p>
  <p>You will be redirected in</p> 
  <p><h1 id="countdown">15</h1></p>  
  <p>seconds.</p> 
  </center>
  </body>
</html>

<?php

include('miner.inc.php');

miner('restart','');
