<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$username = $_POST["username"];
$password = $_POST["password"];

$request = array();
$request['type'] = "login";
$request['username'] = $username;
$request['password'] = $password;
//$request['message'] = "HI";
$response = $client->send_request($request);

if($response == 1)
{
session_start();
$_SESSION["username"] = $username;
$_SESSION["password"] = $password;
header("Location: home.php");
exit;}
else{
header("Location: unsuccessful.php");
exit;}

?>
