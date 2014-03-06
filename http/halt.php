<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>Halting MinePeon</title>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/bootstrap-minepeon.css" rel="stylesheet">
        <style>
   html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        body {
            display: table;
        }

        .center-page {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }
        </style>
<script type="text/javascript">
var start = new Date();
start = Date.parse(start)/1000;
var seconds = 30;
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
  <div class="center-page">
  <p><h1>Shutting Down MinePeon</h1></p>
  <p>It should be safe to unplug in</p> 
  <p><h1 id="countdown">30</h1></p>  
  <p>seconds.</p> 
  </div>
  </body>
</html>

<?php

exec('/usr/bin/sudo /usr/bin/halt > /dev/null 2>&1 &');
