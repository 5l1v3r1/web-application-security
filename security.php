<?php
/*
	.......,,,,.............
	.      Simple Web      . @author: FilthyRoot
	. Application Security . Copyright (c) 2020 Jogjakarta Hacker Link
	........................
	
Tinggal include file ini ke daerah2 yg sensitif :D

Example : 

<?php
//ini ceritanya file config/koneksi
include("security.php"); // tambahkan include file security.php

$conn = mysqli_connect("localhost", "root", "secret", "database");
//........
?>

*/

$sqli 	= "order by|select\(|-- -|injected by|database\(\)|user\(\)|concat\(|'=''or'|'or''='";
$xss 	= "alert\(|onerror|document.|src=x|pastebin.com|javascript|.cookie|<iframe";
$rfi	= "http:\/\/|https:\/\/|data:\/\/|php:\/\/";
$rce 	= "wget|curl|rm -rf|python";
$lfi	= "..\/|\/etc\/passwd";
$global = "shell|indoxploit|ssi|backdoor|exploit|php5|phtml|pjpeg|php.black|php.ndsfx|php.fla|php.pjpeg|php7|php2|php_gif|.htaccess";
$deface = "hacked|owned by|pwndz by|pwnd by|passwd";

$str 	= $sqli."|".$xss."|".$lfi."|".$rfi."|".$rce."|".$global."|".$deface;

function do_Block($value){
	global $sqli, $xss, $rce, $lfi, $rfi, $global, $deface;
	$value = strtolower($value);
	header("HTTP/1.1 403 Forbidden");
	if(preg_match("/$sqli/", $value)){
		$attack_id = "SQL Injection";
	}elseif(preg_match("/$xss/", $value)){
		$attack_id = "Cross Site Scripting";
	}elseif(preg_match("/$deface/", $value)){
		$attack_id = "Defacement/Vandalism";
	}elseif(preg_match("/$rce/", $value)){
		$attack_id = "Remote Code Execution";
	}elseif(preg_match("/$lfi/", $value)){
		$attack_id = "Path Disclosure";
	}elseif(preg_match("/$rfi/", $value)){
		$attack_id = "PHP Stream Injection";
	}elseif($value == "banned"){
		$attack_id = "Banned IP";
	}else{
		$attack_id = "Backdoor/Dangerous Request";
	}

	file_put_contents("attack_log.txt", $_SERVER['REMOTE_ADDR']." Trying ".$attack_id." on ".$_SERVER['PHP_SELF']."\n", FILE_APPEND);
	return "<title>Request Blocked!</title>
<center>
<img src=\"https://img.freepik.com/free-vector/stop-sign-icon-notifications-that-anything_68708-468.jpg\" style=\"width: 300px;\">
<h1>Request Blocked!</h1>
<h2>Your IP : ".$_SERVER['REMOTE_ADDR']."</h2><h3>Attack ID : ".$attack_id."</h3><br>
<i>Application Security by <a target=_blank href='http://jogjakartahackerlink.github.io/'>Jogjakarta Hacker Link</a></i>
</center>
";
}

function do_Security(){
	global $str;
	if($_GET or $_POST){
		foreach($_GET as $key => $value){
			if(preg_match("/$str/", strtolower($value))){
				echo do_Block($value);
				exit();
			}
		}

		foreach ($_POST as $key => $value) {
			if(preg_match("/$str/", strtolower($value))){
				echo do_Block($value);
				exit();
			}
		}
	}
}

function do_Filter(){
	if ($_POST or $_GET){
		foreach ($_GET as $key => $value) {
			return $_GET[$key] = htmlentities(strip_tags(str_replace("'", "\'", $value)));
		}

		foreach ($_POST as $key => $value) {
			return $_POST[$key] = htmlentities(strip_tags(str_replace("'", "\'", $value)));
		}
	}
}

function do_Ban(){
	$banned_ip  = array('8.8.8.8','0.0.0.0','1.1.1.1','Add Here');
	$visitor_ip = $_SERVER['REMOTE_ADDR'];

	if(in_array($visitor_ip, $banned_ip)){
		echo do_Block("banned");
		exit();
	}
}

do_Ban();
do_Filter();
do_Security();

?>