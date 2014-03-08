<footer class="container">
  <hr />
  <?php echo $lang["servertime"]; ?> <?php echo date('D, d M Y H:i:s T') ?>
  <?php $version = file_get_contents('/opt/minepeon/etc/version'); ?>
  <br><?php echo $lang["version"]; ?> <?=$version?>
  <?php if(empty($settings['donateAmount'])) { echo $plea; } ?>
</footer>
</body>
</html>
