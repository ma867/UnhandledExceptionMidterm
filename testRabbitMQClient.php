#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");


$request = array();
$request['type'] = "register";
$request['username'] = $argv[1];
$request['password'] = $argv[2];
$request['email'] = $argv[3];
$request['firstname'] = $argv[4];
$request['lastname'] = $argv[5];
$response = $client->send_request($request);
//$response = $client->publish($request);



echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

