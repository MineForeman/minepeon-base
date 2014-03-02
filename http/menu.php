<?php
//The language system is Work in progress
if ($settings['lang'] == "no"){
include("lang/no/lang.no.php");
}else{
include("lang/en/lang.en.php");
}
?>

<div class="navbar">
  <div class="container">
    <a class="navbar-brand" href="http://mineforeman.com/minepeon/">MinePeon</a>
    <ul class="nav navbar-nav">
      <li><a href="/"><?php echo $lang["status"]; ?></a></li>
      <li><a href="/pools.php"><?php echo $lang["pools"]; ?></a></li>
      <li><a href="/settings.php"><?php echo $lang["settings"]; ?></a></li>
      <li><a href="/plugins.php"><?php echo $lang["plugins"]; ?></a></li> 
      <li><a href="/about.php"><?php echo $lang["about"]; ?></a></li>
      <li><a href="/contact.php"><?php echo $lang["contact"]; ?></a></li>
      <li><a href="/license.php"><?php echo $lang["license"]; ?></a></li> 
<?php 
   if ($handle = opendir('plugins/api_menu/')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
               $menuadd=simplexml_load_file("plugins/api_menu/" . $entry);
echo "<li><a href='" . $menuadd->pl_folder . "'>" . $menuadd->Menu_text . "</a></li>";

            }
        }
        closedir($handle);
   }
?>
    </ul>
  </div>
</div>

