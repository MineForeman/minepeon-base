<?php

include_once('settings.inc.php');
include_once('functions.inc.php');


this_session_start();


if(isset($_POST['username'], $_POST['password'])) { 
   $username = $_POST['username'];
   $password = $_POST['password'];
   if(login($username, $password) == true) {
      if(login($username, $password) == "x1"){
      header('Location: login.php?error=2');
   }else{
	$settings['loginTry'] = $settings['loginTry'] + 1;
       writeSettings($settings);
      header('Location: login.php?error=1');
      }
   } else {
  $settings['loginTry'] = 0;
        writeSettings($settings);
      header('Location: /');
      }
}

include('head.php');
?>

<style>
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}
.login
{
    position: fixed;
    left: 50%;
    top: 50%;
    z-index: 100;
    height: 400px;
    margin-top: -200px;
    width: 600px;
    margin-left: -300px;
}
.logo
{
    position: fixed;

    left: 5%;

}
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

<div class="logo">
<img src="img/Logo.png" height="146" widht="239">

<div>


<div class="login">

<?php

if (isset($_GET['error'])) {
if ($_GET['error'] == "1"){
?>
<div class="alert alert-danger">Username and password did not match</div>
<?php
}
if ($_GET['error'] == "2"){
?>
<div class="alert alert-danger">Your account is Blocked!</div>
<?php
}
}



if (isset($_GET['newuser'])) {
?>
<div class="alert alert-sucsess">Your account is Created, you can now login</div>
<?php
}
?>
      <form action="" method="POST" class="form-signin" name="login_form" role="form">
        <h2 class="form-signin-heading">Please sign in</h2>
<div class="input-group">
  <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
        <input type="text" name="username" id='username' class="form-control" placeholder="Username">
</div>
<div class="input-group">
  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password">
</div>
        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit" onClick="formhash(this.form, this.form.password);"">Sign in</button>
      </form>

    </div>





</body>
</html>
