#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once ('BMRFormulas.php');
require_once('ApplicationFunctions.php');

function requestProcessor($request)
{
    echo "received request".PHP_EOL;
    var_dump($request);
    if(!isset($request['type']))
    {
        return "ERROR: unsupported message type";
    }
    switch ($request['type'])
    {
        case "login":
            return ApplicationFunctions::doLogin($request['username'],$request['password']);
        case "register":
            return ApplicationFunctions::doRegister($request['username'],$request['password'],$request['email'],$request['firstname'], $request['lastname']);
        case "insertinfo":
            return ApplicationFunctions::registerUserInfo($request['username'],$request['age'],$request['weight'],$request['height'], $request['gender'], $request['lifestyle'], $request['vegetarian'], $request['nonvegetarian'], $request['vegan'], $request['pescetarian'],$request['gluten'],$request['dairy'], $request['peanut'], $request['seafood']);
        case "insertmeal":
            return ApplicationFunctions::addInformationToMealsAndCaloriesTables($request['username'], $request['mealId']);
        case "insertlike":
            return ApplicationFunctions::addMealToLikesTable($request['username'], $request['mealId']);
        case "returnrecipe":
            return ApplicationFunctions::returnRegularRecipe($request['mealId']);
        case "returnmodifiedrecipe":
            return ApplicationFunctions::returnIngredientInformation($request['username'], $request['mealId'], $request['ingredients']);
    }
    return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>

