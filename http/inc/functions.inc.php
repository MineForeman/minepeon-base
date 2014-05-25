<?php

require_once('Mail.php');


function this_session_start() {
        ini_set('session.use_only_cookies', 1); 
        $cookieParams = session_get_cookie_params(); 
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], 'false', 'true'); 
        session_name('Minepeon'); 
        session_start(); 
        session_regenerate_id(true);     
}


function login($username, $password) {
$settings = json_decode(file_get_contents("/opt/minepeon/etc/minepeon.conf", true), true);
 	$user = $settings['loginUsername'];
 	$pass = $settings['loginPassword'];
 	$salt = $settings['loginSalt'];
        $password = md5($password.$salt); 
         if ($settings['loginTry'] < 20){
         if($pass == $password && $user == $username){ 
               $_SESSION['user'] = $username;
               $_SESSION['login_string'] = hash('sha512', $password.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
               return false; 
         } else {
            return "x2";
         }
}else{
return "x1";
}
   
}

function login_setup(){
$settings = json_decode(file_get_contents("/opt/minepeon/etc/minepeon.conf", true), true);
   if(isset($settings['loginPassword'])){
   return true;
 }else{
   return false;
}
}

function login_check($mode) {
$settings = json_decode(file_get_contents("/opt/minepeon/etc/minepeon.conf", true), true);
   if(isset($settings['loginPassword'], $settings['loginUsername'])){
   if(isset($_SESSION['user'], $_SESSION['login_string'])) {
     $user = $_SESSION['user'];
     $login_string = $_SESSION['login_string'];
     $pass = $settings['loginPassword'];
           $login_check = hash('sha512', $pass.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
           if($login_check == $login_string) {
              return true;
           } else {
if ($mode == "quick"){
header('Location: login.php?error=3');
}
if ($mode == "normal"){
return false;
}
           }
   } else {
if ($mode == "quick"){
header('Location: login.php?error=3');
}
if ($mode == "normal"){
return false;
}
   }
   }else{
if ($mode == "quick"){
header('Location: setup.php');
}
if ($mode == "normal"){
return false;
}
}
}

function sendEmail($settings, $subject, $body) {

		$mailSettings = array(
				'host' => $settings['alertSmtp']
			);

		if ($settings['alertSMTPAuth']) {
			$mailSettings['auth'] = true;
			$mailSettings['username'] = $settings['alertSmtpAuthUser'];
			$mailSettings['password'] = $settings['alertSmtpAuthPass'];
			$mailSettings['port'] = $settings['alertSmtpAuthPort'];
		}

		//$settings['alertDevice']

		$mail = Mail::factory("smtp", $mailSettings );

		$headers = array("From"=>$settings['alertEmail'], "Subject"=>$subject);
		$mail->send($settings['alertEmail'], $headers, $body);		

}

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

// https://www.owasp.org/index.php/PHP_CSRF_Guard

function store_in_session($key,$value)
{
	if (isset($_SESSION))
	{
		$_SESSION[$key]=$value;
	}
}
function unset_session($key)
{
	$_SESSION[$key]=' ';
	unset($_SESSION[$key]);
}
function get_from_session($key)
{
	if (isset($_SESSION))
	{
		return $_SESSION[$key];
	}
	else {  return false; } //no session data, no CSRF risk
}

function csrfguard_generate_token($unique_form_name)
{
	if (function_exists("hash_algos") and in_array("sha512",hash_algos()))
	{
		$token=hash("sha512",mt_rand(0,mt_getrandmax()));
	}
	else
	{
		$token=' ';
		for ($i=0;$i<128;++$i)
		{
			$r=mt_rand(0,35);
			if ($r<26)
			{
				$c=chr(ord('a')+$r);
			}
			else
			{ 
				$c=chr(ord('0')+$r-26);
			} 
			$token.=$c;
		}
	}
	store_in_session($unique_form_name,$token);
	return $token;
}
function csrfguard_validate_token($unique_form_name,$token_value)
{
	$token=get_from_session($unique_form_name);
	if ($token===false)
	{
		return true;
	}
	elseif ($token===$token_value)
	{
		$result=true;
	}
	else
	{ 
		$result=false;
	} 
	unset_session($unique_form_name);
	return $result;
}

function csrfguard_replace_forms($form_data_html)
{
	$count=preg_match_all("/<form(.*?)>(.*?)<\\/form>/is",$form_data_html,$matches,PREG_SET_ORDER);
	if (is_array($matches))
	{
		foreach ($matches as $m)
		{
			if (strpos($m[1],"nocsrf")!==false) { continue; }
			$name="CSRFGuard_".mt_rand(0,mt_getrandmax());
			$token=csrfguard_generate_token($name);
			$form_data_html=str_replace($m[0],
				"<form{$m[1]}>
<input type='hidden' name='CSRFName' value='{$name}' />
<input type='hidden' name='CSRFToken' value='{$token}' />{$m[2]}</form>",$form_data_html);
		}
	}
	return $form_data_html;
}

function csrfguard_inject()
{
	$data=ob_get_clean();
	$data=csrfguard_replace_forms($data);
	echo $data;
}

function csrfguard_start()
{
	if (count($_POST))
	{
		if ( !isset($_POST['CSRFName']) or !isset($_POST['CSRFToken']) )
		{
			trigger_error("No CSRFName found, probable invalid request.",E_USER_ERROR);		
		} 
		$name =$_POST['CSRFName'];
		$token=$_POST['CSRFToken'];
		if (!csrfguard_validate_token($name, $token))
		{ 
			trigger_error("Invalid CSRF token.",E_USER_ERROR);
		}
	}
	ob_start();
	/* adding double quotes for "csrfguard_inject" to prevent: 
          Notice: Use of undefined constant csrfguard_inject - assumed 'csrfguard_inject' */
	register_shutdown_function("csrfguard_inject");	
}
