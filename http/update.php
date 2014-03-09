
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>Restarting Miner</title>
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

  </head>
  <body>
  <div class="center-page">
<div id="update" style="width"></div><div id="image" style="width"></div><div id="output" style="width"></div>
  </div>

  </body>
</html>

<?php
include('settings.inc.php');
if ($settings['update'] == "true"){
echo '<script language="javascript">
    document.getElementById("update").innerHTML="<p>Downloading...</p>";
    document.getElementById("image").innerHTML="<img src=\'/img/loader.gif\' width=\'42\' height=\'42\'>";
    </script>';
echo str_repeat(' ',1024*64);
flush();
exec('git fetch origin ' . $settings['updatebranch'] . ' 2>&1', $outputa);
foreach ($outputa as $outputb) {
     $output = $output . $outputb;
}
echo '<script language="javascript">
    document.getElementById("update").innerHTML="<p>Installing...</p>";
    document.getElementById("output").innerHTML="<br><div class=\'panel panel-danger\'><div class=\'panel-heading\'><h3 class=\'panel-title\'>ERROR:</h3></div><div class=\'panel-body\'>' . $output . '</div></div>";
    </script>';
echo str_repeat(' ',1024*64);
flush();
exec('git merge ' . $settings['updatebranch'] . ' 2>&1', $outputa);
foreach ($outputa as $outputb) {
     $output = $output . $outputb;
}
echo '<script language="javascript">
    document.getElementById("update").innerHTML="<p>Rebooting system... (might take a while)</p>";
    document.getElementById("image").innerHTML="<img src=\'/img/ok.png\' width=\'42\' height=\'42\'>";
  document.getElementById("output").innerHTML="<br><div class=\'panel panel-danger\'><div class=\'panel-heading\'><h3 class=\'panel-title\'>ERROR:</h3></div><div class=\'panel-body\'>' . $output . '</div></div>";
    </script>';
}else{
echo '<script language="javascript">
    document.getElementById("update").innerHTML="<p>Your system is up to date ;)</p>";
    document.getElementById("image").innerHTML="<img src=\'/img/ok.png\' width=\'42\' height=\'42\'>";
    </script>';
}
?>


