<?php
include('settings.inc.php');
include('functions.inc.php');
$writeSettings=false;

this_session_start();

if(login_setup() == true){
header('Location index.php');
}
if(login_setup() == false){


if(isset($_POST['username'], $_POST['password'], $_POST['password2'])) {
if($_POST['password'] == $_POST['password2']){
        $settings['loginUsername'] = $_POST['username'];
        $salt = md5(uniqid(mt_rand(1, mt_getrandmax())));
 	$settings['loginSalt'] = $salt;
        $password = md5($_POST['password'].$salt); 
 	$settings['loginPassword'] = $password;
        $settings['loginTry'] = 0;
        ksort($settings);
        writeSettings($settings);
      header('Location: login.php?newuser');
   }else{
      header('Location: setup.php?error=1');
      }
}

include('head.php');
include('menu-nologin.php');
?>

<div class="container">



<style>
.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
</style>

</div>
<div class="container">

<?php
if (isset($_GET['error'])) {
if ($_GET['error'] == "1"){
?>
<div class="alert alert-danger">Password did not match</div>
<?php
}
}
?>
      <form action="" method="POST" class="form-signin" name="login_form" role="form">
        <h2 class="form-signin-heading">Set up your user</h2>
<div class="input-group">
  <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
        <input type="text" name="username" id='username' class="form-control" placeholder="Username">
</div>
<div class="input-group">
  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password">
</div>
<div class="input-group">
  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
        <input type="password" name="password2" id="password2" class="form-control" placeholder="Repeat Password">
</div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Save user</button>
      </form>

    </div>

      <?php   
include('foot.php');

}
?>



</body>
</html>
