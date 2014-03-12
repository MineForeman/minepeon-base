<?php

require_once('settings.inc.php');
require_once('miner.inc.php');

$pluginlist = simplexml_load_file("https://raw.github.com/MineForeman/MinePeon-Updates/master/plugins.xml");

if (isset($_FILES["file"]["tmp_name"])) {
	exec("tar -xzf " . $_FILES["file"]["tmp_name"] . " -C /opt/minepeon/ ");
       if (is_dir("instal_temp")) {
	   $plugin=simplexml_load_file("instal_temp/instal.xml");
	 	if ($plugin->run_sh_file !== "False"){
	 	exec("/opt/minepeon/http/instal_temp/" . $plugin->run_sh_file); 
	 	rrmdir('instal_temp');		 
         if ($plugin->redirect_after != "False"){
         	header( 'Location: /plugins/' . $plugin->redirect_after ) ;
      }		 
    }
  }
}

if (isset($_POST["wget"])) {
       exec("wget -P /opt/minepeon/http/ -O Tmpfile.tar.gz " . $_POST["wget"]);
	exec("tar -xzf /opt/minepeon/http/Tmpfile.tar.gz -C /opt/minepeon/ ");
       unlink('Tmpfile.tar.gz');
      if (is_dir("instal_temp")) {
	     $plugin=simplexml_load_file("instal_temp/instal.xml");
	     if ($plugin->run_sh_file !== "False"){
	     exec("/opt/minepeon/http/instal_temp/" . $plugin->run_sh_file);  	
		 rrmdir('instal_temp');
         if ($plugin->redirect_after != "False"){
         header( 'Location: /plugins/' . $plugin->redirect_after ) ;
      }		 
    }
  } 
}

if (isset($_POST['delpl'])) {
$delpl = $_POST['delpl'];
list($delpl1,$delpl2) = explode('/', $delpl, 2);
if (file_exists($delpl . "/uninstal.xml")){
	$plugin=simplexml_load_file($delpl . "/uninstal.xml");
    exec("/opt/minepeon/http/" . $delpl . "/" . $plugin->run_sh_file); 
}
rrmdir($_POST['delpl']);



unlink('plugins/api_menu/' . $delpl2 . '_apimenu.xml');
  header('Location: /plugins.php');
}



  function rrmdir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir") 
           rrmdir($dir."/".$object); 
        else unlink   ($dir."/".$object);
      }
    }
    reset($objects);
    rmdir($dir);
  }
 }


include('head.php');
include('menu.php');

?>
        <div class="container">
 </center>

  <h3>Plugins</h3>
  <table id="Plugins" class="table table-striped">
    <thead> 
      <tr>
        <th>Name</th>
        <th>Made by</th>
        <th>Description</th>
        <th>Settings</th>   
        <th>Delete</th>  
      </tr>
    </thead>
    <tbody>

<?php

$directory = "plugins/";
$files = glob($directory . "*");
 
foreach($files as $file)
{
$plugin=simplexml_load_file($file . "/plugin.xml");
 if(is_dir($file)){
 if($file != "plugins/api_menu"){
 if($file != "plugins/api_settings"){
 if($file != "plugins/api_pools"){

?>
  <tr>
    <td class='text-left'><?php echo $plugin->name; ?></td>
    <td><?php echo $plugin->maker; ?></td>
    <td><?php echo $plugin->description; ?></td>
    <td><?php echo '<a class="btn btn-default" href="'. $plugin->settings . '">Settins</a>'; ?></td>    
    <td>
  <form name="delpls" action="/plugins.php" method="post">
<input type="hidden" name="delpl" id="delpl" value="<?php echo $file; ?>" />
		  <button type="submit" class="btn btn-default">Delete</button>
  </form>

</td>
    </tr>


  
<?php
        }
      }
    }
  }
}
?>   

</tbody>
  </table>  
</blockquote>
  <form name="instal" action="/plugins.php" method="post" enctype="multipart/form-data" class="form-horizontal">
    <fieldset>
      <legend>Install plugin</legend>

      <div class="form-group">
		<div class="col-lg-9 col-offset-3">
		  <input type="file" name="file" id="file" class="btn btn-default" data-input="false">
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-lg-9 col-offset-3">
		  <button type="submit" name="submit" class="btn btn-default">Install</button>
		</div>
      </div>
    </fieldset>
  </form>

</blockquote>
  <form name="instal" action="/plugins.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Install plugin from web</legend>

      <div class="form-group">
        <label for="downloadUrl" class="control-label col-lg-3">Download url</label>
		<div class="col-lg-9">
		<input type="text" name="wget" class="form-control">
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-lg-9 col-offset-3">
		  <button type="submit" name="submit" class="btn btn-default">Install</button>
		</div>
      </div>
    </fieldset>
  </form>

</blockquote>
  <legend>Click and install</legend>
  <table id="Plugins" class="table table-striped">
    <thead> 
      <tr>
        <th>Name</th>
        <th>Made by</th>
        <th title="Made my">Description</th>
        <th title="Install">Install</th>    
      </tr>
    </thead>
    <tbody>

<?php

foreach($pluginlist->pl as $pli) {     

?>
  <tr>
    <td class='text-left'><?php echo $pli->name; ?></td>
    <td><?php echo $pli->maker; ?></td>
    <td><?php echo $pli->description; ?></td>  
    <td>
	<form name="instal" action="/plugins.php" method="post" class="form-horizontal">
	<input type="hidden" name="wget" value="<?php echo $pli->downloadurl; ?>" />
	<button type="submit" name="submit" class="btn btn-default">Install</button>
  </form>

</td>
    </tr>

<?php
}
?>
</tbody>
  </table>  

        </div>


<?php

include('foot.php');
