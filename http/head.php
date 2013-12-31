<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>MinePeon, from MineForeman</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta http-equiv="refresh" content="600">

  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/bootstrap-minepeon.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

  <!-- Fav and touch icons
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
  <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
  <link rel="shortcut icon" href="ico/favicon.png">-->
  
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/themes/gray.js"></script>
<script type="text/javascript" src="js/chart.js" ></script>

<script type="text/javascript">
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
    $('#alertSMTPAuth').click(function() {
      $(".smtpauth-enabled").toggle(this.checked);
    });
  });
</script>
  
</head>
<body>