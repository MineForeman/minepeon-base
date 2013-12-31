<footer class="container">
  <hr />
  Server Time: <?php echo date('D, d M Y H:i:s T') ?>
  <?php $version = file_get_contents('/opt/minepeon/etc/version'); ?>
  <br>Version: <?=$version?>
  <?php if(empty($settings['donateAmount'])) { echo $plea; } ?>
</footer>



</body>
</html>
