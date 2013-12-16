<?php

include('settings.inc.php');



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
        <th title="Settins">Settings</th>      
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

  
?>
  <tr>
    <td class='text-left'><?php echo $plugin->name; ?></td>
    <td><?php echo $plugin->maker; ?></td>
    <td><?php echo $plugin->description; ?></td>
    <td><?php echo "<a href=". $plugin->settings . ">Settins</a>"; ?></td>     
    </tr>


  
<?php
}

}
?>   

</tbody>
  </table>  
</blockquote>
        </div>
<?php

include('foot.php');