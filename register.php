#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$username = $_POST["username"];
$password = $_POST["password"];
$email = $_POST["email"];
$firstname = $_POST["firstname"];
$lastname = $_POST["lastname"];


$request = array();
$request['type'] = "register";
$request['username'] = $username;
$request['password'] = $password;
$request['email'] = $email;
$request['firstname'] = $firstname;
$request['lastname'] = $lastname;
$response = $client->send_request($request);
//$response = $client->publish($request);

if($response == 1)
{
header("Location: runsuccessful.php");
exit;}
else{
session_start();
$_SESSION["username"] = $username;
$_SESSION["password"] = $password;
header("Location: BMI.php");
exit;}



?>
