

<div class="navbar">
  <div class="container">
    <a class="navbar-brand" href="http://mineforeman.com/minepeon/">MinePeon</a>
    <ul class="nav navbar-nav">
      <li><a href="/">Status</a></li>
      <li><a href="/pools.php">Pools</a></li>
      <li><a href="/settings.php">Settings</a></li>
      <li><a href="/plugins.php">Plugins</a></li> 
      <li><a href="/about.php">About</a></li>
      <li><a href="/contact.php">Contact</a></li>
      <li><a href="/license.php">License</a></li> 
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

