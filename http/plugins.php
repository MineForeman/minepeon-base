<?php

require_once('settings.inc.php');
require_once('miner.inc.php');

// This need some testing now...

if (isset($_FILES["file"]["tmp_name"])) {
	exec("tar -xzf " . $_FILES["file"]["tmp_name"] . " -C /opt/minepeon/ ");
       if (is_dir(instal_temp)) {
	exec("./opt/minepeon/http/instal_temp/instal.sh");          
}
}

if (isset($_POST["wget"])) {
       exec("wget -P /opt/minepeon/http/ -O Tmpfile.tar.gz " . $_POST["wget"]);
	exec("tar -xzf /opt/minepeon/http/Tmpfile.tar.gz -C /opt/minepeon/ ");
       unlink('Tmpfile.tar.gz');
       if (is_dir(instal_temp)) {
	exec("./opt/minepeon/http/instal_temp/instal.sh");          
}
}

if (isset($_POST['delpl'])) {
$delpl = $_POST['delpl'];
rrmdir($_POST['delpl']);

list($delpl1,$delpl2) = explode('/', $delpl, 2);

unlink('plugins/api_menu/' . $delpl2 . '_apimenu.xml');
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
        <th title="Made my">Description</th>
        <th title="Settings">Settings</th>   
        <th title="Delete">Delete</th>  
      </tr>
    </thead>
    <tbody>
         <?php

//path to directory to scan
$directory = "plugins/";



//get all files in specified directory
$files = glob($directory . "*");
 
 //check to see if the file is a folder/directory
//else{
//echo "<center><h2>There is not any plugins is installed!</h2></center>";
//}

//print each file name
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
    <td><?php echo '<a class="btn btn-default" href="'. $plugin->settings . '">Settings</a>'; ?></td>    
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

        </div>


<?php

include('foot.php');
