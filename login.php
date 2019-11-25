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

if($response == 0)
{
header("Location: unsuccessful.php");
exit;}
else{
session_start();
$_SESSION["username"] = $username;
$_SESSION["password"] = $password;
$_SESSION['recommendedcalories'] = $response['recommendedCalories'];
$_SESSION['diet'] =  $response['diet'];
$_SESSION['intolerance'] =  $response['intolerance'];
$_SESSION['meals'] =  $response['meals'];
$_SESSION['userid'] = $response['userid'];
$_SESSION['missingcalories'] = $response['missingCalories'];
$_SESSION['recommendedmeals'] = $response['recommendedMeals'];
header("Location: home.php");
exit;}

?>
