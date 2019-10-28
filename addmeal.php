<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

session_start();
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$username = $_SESSION["username"];
$mealId = $_GET["mealId"];


$request = array();
$request['type'] = "insertmeal";
$request['username'] = $username;
$request['mealId'] = $mealId;
$response = $client->send_request($request);


if($response == 1)
{
   header("Location: addMealUnsucessful.php");
   exit;
}
else{

//echo "request received";
    header("Location: addMealSucessful.php");
    exit;
}
?>
