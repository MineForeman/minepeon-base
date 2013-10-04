<footer class="container">
  <hr />
  Server Time: <?php echo date('D, d M Y H:i:s T') ?>

  <?php if(empty($settings['donateAmount'])) { echo $plea; } ?>
</footer>

<script type="text/javascript" src="js/jquery.min.js"></script> 
<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
<script type="text/javascript" id="js">
  $(document).ready(function() {
    $(".tablesorter").tablesorter();
    
    $('#chartToggle').click(function() {
      $('.chartMore').slideToggle('slow', function() {
          if ($(this).is(":visible")) {
              $('#chartToggle').text('Hide extended charts');
          } else {
              $('#chartToggle').text('Display extended charts');
          }
      });
    });
    $('#alertEnable').click(function() {
      $(".alert-enabled").toggle(this.checked);
    });
    $('#donateEnable').click(function() {
      $(".donate-enabled").toggle(this.checked);
    });
  });
</script>

</body>
</html>
