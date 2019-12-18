<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

session_start();
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");


$username = $_SESSION["username"];
$mealId= $_SESSION["mealId"];
$ingredients = $_POST['data'];

$includedIngredients = $ingredients[1];
$excludedIngredients = $ingredients[0];

echo $includedIngredients;
echo $includedIngredients;

/*for ($x = 0; $x <= 1; $x++) {
    for ($y = 0; $y <= 1; $y++){
        echo $ingredients[$x][$y] ."\n";
    }

}*/



$request = array();
$request['type'] = "returnmodifiedrecipe";
$request['username'] = $username;
$request['mealId'] = $mealId;
$request['includedingredients'] = $includedIngredients;
$request['excludedingredients'] = $excludedIngredients;
$response = $client->send_request($request);


if($response == 1)
{
    header("Location: home.php");
    exit;
}
else{

//echo "request received";
    $_SESSION['mealId'] = $mealId;
    $_SESSION['recipe'] = $response;
    header("Location: recipeModified.php");
    exit;
}

?>