#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once ('ApplicationFunctions.php');

session_start();
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$username = $_SESSION["username"];
$age = $_POST["age"];
$weight = $_POST["weight"];
$height = ($_POST["feet"] * 12) + $_POST["inches"] ;
$gender = $_POST["gender"];
$lifestyle = $_POST["lifestyle"];

if(isset($_POST["vegetarian"])){
    $vegetarian = 1;
}
else{
    $vegetarian = 0;
}
if(isset($_POST["nonvegetarian"])){
    $nonvegetarian = 1;
}
else{
    $nonvegetarian = 0;
}
if(isset($_POST["vegan"])){
    $vegan = 1;
}
else{
    $vegan = 0;
}
if(isset($_POST["pescetarian"])){
    $pescetarian = 1;
}
else{
    $pescetarian = 0;
}

if(isset($_POST["gluten"])){
    $gluten = 1;
}
else{
    $gluten = 0;
}
if(isset($_POST["dairy"])){
    $dairy = 1;
}
else{
    $dairy = 0;
}
if(isset($_POST["peanut"])){
    $peanut = 1;
}
else{
    $peanut = 0;
}
if(isset($_POST["seafood"])){
    $seafood = 1;
}
else{
    $seafood = 0;
}

$request = array();
$request['type'] = "insertinfo";
$request['username'] = $username;
$request['age'] = $age;
$request['weight'] = $weight;
$request['height'] = $height;
$request['gender'] = $gender;
$request['lifestyle'] = $lifestyle;
$request['vegetarian'] = $vegetarian;
$request['nonvegetarian'] = $nonvegetarian;
$request['vegan'] = $vegan;
$request['pescetarian'] = $pescetarian;
$request['gluten'] = $gluten;
$request['dairy'] = $dairy;
$request['peanut'] = $peanut;
$request['seafood'] = $seafood;
$response = $client->send_request($request);

if($response == 1)
{

header("Location: bmiunsuccessful.php");
exit;
}
else{
    $username= $_SESSION["username"];
    $logindb = new mysqli("192.168.2.4","testUser","12345","testdb");
    if(mysqli_connect_errno())
    {
        echo "failed to connect to MYSQL:" . mysqli_connect_error();
        exit();
    }
    else {
        mysqli_select_db($logindb, "testdb");
        //get userid from users table using username
        $query = "select * from users where username = '$username'";
        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
        while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
            $userid = $result["userid"];

        }
        $recommendedCalories = ApplicationFunctions::getRecommendedCalories($userid, $logindb);
        $diet = ApplicationFunctions::getDietaryPreferences($userid, $logindb);
        $intolerance = ApplicationFunctions::getIntolerances($userid, $logindb);

    }

    $_SESSION["recommendedcalories"] = $recommendedCalories;
    $_SESSION["diet"] = $diet;
    $_SESSION["intolerance"] = $intolerance;
    header("Location: home.php");
    exit;
}
?>
