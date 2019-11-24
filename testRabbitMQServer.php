#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once ('BMRFormulas.php');

//curl functions

    function curlGenerateMealPlanFromCalories($recommendedCalories, $diet, $intolerance){
        try {
            $url = "https://api.spoonacular.com/recipes/mealplans/generate?apiKey=23f5be8cdf814b51a9307dc2be2cbca3";

            $data = "&targetCalories=" . $recommendedCalories . $diet . $intolerance;
            $url .= $data;

            //initialize curl
            $ch = curl_init();

            // Check if initialization had gone wrong*
            if ($ch === false) {
                throw new Exception('failed to initialize');
            }
            curl_setopt($ch, CURLOPT_URL, $url);

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);

            if ($server_output === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            curl_close($ch);
            $result = json_decode($server_output);

            //var_dump($result);
            return $result;

        } catch (Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }

    }

    function curlGenerateMealPlanFromMissingCalories($missingCalories, $diet, $intolerance){
        try {
            $url = "https://api.spoonacular.com/recipes/mealplans/generate?apiKey=23f5be8cdf814b51a9307dc2be2cbca3";

            $data = "&targetCalories=" . $missingCalories . $diet . $intolerance;
            $url .= $data;

            //initialize curl
            $ch = curl_init();

            // Check if initialization had gone wrong*
            if ($ch === false) {
                throw new Exception('failed to initialize');
            }
            curl_setopt($ch, CURLOPT_URL, $url);

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);

            if ($server_output === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            curl_close($ch);
            $result = json_decode($server_output);

            return $result;
            //var_dump($result);

        } catch (Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }
    }

    function curlGetIndividualMealInformation($mealId){
        try {

            $url = "https://api.spoonacular.com/recipes/". $mealId . "/information?apiKey=23f5be8cdf814b51a9307dc2be2cbca3&includeNutrition=true";

            $ch = curl_init();

            // Check if initialization had gone wrong*
            if ($ch === false) {
                throw new Exception('failed to initialize');
            }
            curl_setopt($ch, CURLOPT_URL, $url);

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);

            if ($server_output === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            curl_close ($ch);
            $result = json_decode($server_output);

            return $result;
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }
    }

//login and registration


function doLogin($username,$password)
{
    $logindb = new mysqli("192.168.2.4","testUser","12345","testdb");
	if(mysqli_connect_errno())
	{
		echo "failed to connect to MYSQL:" . mysqli_connect_error();
		exit();
	}
	print "connected";

	mysqli_select_db($logindb, "testdb");
	$password = sha1($password);
	$query = "select * from users where username = '$username' and password = '$password'";
	$runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
	$row = mysqli_num_rows($runQuery);
	if ($row > 0)
	{
		echo "user exists logged in!";	
		return 1;
		}
	else
	{
		echo "user doesn't exist";	
		return 0;}

}

function doRegister($username, $password, $email, $firstname, $lastname){
	$logindb = new mysqli("192.168.2.4","testUser","12345","testdb");
	if(mysqli_connect_errno())
	{
		echo "failed to connect to MYSQL:" . mysqli_connect_error();
		exit();
	}
	else{
		print "connected";

		mysqli_select_db($logindb, "testdb");
		$query = "select * from users where username = '$username' or email = '$email'";
		$runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
		$row = mysqli_num_rows($runQuery);
		if ($row >= 1){
			echo "user already exists. please use a different email/username";
			return 1;
		}
		else
		{
		    $password = sha1($password);
			$query = "insert into users (username, password, email, firstname, lastname) values ('$username', '$password', '$email', '$firstname', '$lastname')";
			$runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
			echo "your account has been created!";
			return 0;
        }
	}
		
}
//register user info functions
function registerUserIntolerances($username, $gluten, $dairy, $peanut, $seafood){

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
        //insert user info using the userid
        $query = "insert into intolerances(userid, gluten, dairy, peanut, seafood) values ('$userid', '$gluten', '$dairy', '$peanut', '$seafood')";
        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
        return 0;

    }
}

function registerUserPreferences($username, $vegetarian, $nonvegetarian, $vegan, $pescetarian){
    //check what dietary preference the user chose and define which is "true"
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
        //insert user info using the userid
        $query = "insert into preferences(userid, vegetarian, nonvegetarian, vegan, pescetarian) values ('$userid', '$vegetarian', '$nonvegetarian', '$vegan', '$pescetarian')";
        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
        return 0;

}
}
function registerUserBMI($username, $age, $weight, $height, $gender, $lifestyle){
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
        //insert user info using the userid
        $query = "insert into bmi(userid, weight, height, age, gender, lifestyle) values ('$userid', '$weight', '$height', '$age', '$gender', '$lifestyle')";
        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
        //calculate recommended calories
        $recommendedCalories = BMRFormulas::calculateCalories($age, $weight, $height, $gender, $lifestyle);
        //insert recommended calories into bmi table
        $query = "update bmi set reccalories = '$recommendedCalories' where userid = '$userid'";
        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
        return 0;
    }

}

function registerUserInfo($username, $age, $weight, $height, $gender, $lifestyle, $vegetarian, $nonvegetarian, $vegan, $pescetarian, $gluten, $dairy, $peanut, $seafood){
    registerUserBMI($username, $age, $weight, $height, $gender, $lifestyle);
    registerUserPreferences($username, $vegetarian, $nonvegetarian, $vegan, $pescetarian);
    registerUserIntolerances($username, $gluten, $dairy, $peanut, $seafood);
    return 0;
}

//get user info
function getRecommendedCalories($userid, $logindb){
            $query = "select * from bmi where userid = '$userid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $recommendedCalories = $result["reccalories"];
            }
            return $recommendedCalories;
    }

function getMissingCalories($userid, $logindb)
    {
            $recommendedCalories = getRecommendedCalories($logindb, $userid);
            //get userid from users table using username
            $currentDateTime = date('Y-m-d');
            $query = "select * from calories where userid = '$userid' and date = '$currentDateTime'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $consumedCalories = $result["dailycalories"];
            }
            if ($consumedCalories == NULL) {
                return $missingCalories = 0;
            } else {
                $missingCalories = $recommendedCalories - $consumedCalories;
                return $missingCalories;
            }

    }
function getDietaryPreferences($userid, $logindb)
        {
                $query = "select * from preferences where userid = '$userid'";
                $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                    $vegetarian = $result["vegetarian"];
                    $vegan = $result["vegan"];
                    $nonvegetarian = $result["nonvegetarian"];
                    $pescetarian = $result["pescetarian"];
                }
                if ($vegetarian == true) {
                    $diet = "&diet=vegetarian";
                    return $diet;
                }
                if ($vegan == true) {
                    $diet = "&diet=vegan";
                    return $diet;
                }
                if ($nonvegetarian == true) {
                    $diet = "&diet=whole30";
                    return $diet;
                }
                if ($pescetarian == true) {
                    $diet = "&diet=pescetarian";
                    return $diet;
                }

        }

function getIntolerances($userid, $logindb)
        {
                $query = "select * from intolerances where userid = '$userid'";
                $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                    $dairy = $result["dairy"];
                    $gluten = $result["gluten"];
                    $peanut = $result["peanut"];
                    $seafood = $result["seafood"];
                }
                $intolerance = "";

                if ($dairy == true) {
                    $intolerance .= "&exclude=dairy";
                }
                if ($gluten == true) {
                    $intolerance .= "&exclude=gluten";
                }
                if ($peanut == true) {
                    $intolerance .= "&exclude=peanut";
                }
                if ($seafood == true) {
                    $intolerance .= "&exclude=seafood";
                }
                return $intolerance;

        }
function generateMealPlanFromCalories($username)
        {
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
                    //get recommended calories
                    $recommendedCalories = getRecommendedCalories($userid, $logindb);
                    $diet = getDietaryPreferences($userid, $logindb);
                    $intolerance = getIntolerances($userid, $logindb);
                    $result = curlGenerateMealPlanFromCalories($recommendedCalories, $diet, $intolerance);
                    $array = get_object_vars($result);

                    $mealIdsArray = array();
                    for ($i = 0; $i <= 9; $i++) {

                        $mealValue = json_decode($array["items"][$i]->value);
                        $mealId = $mealValue->id;
                        $mealIdsArray[] = $mealId;

                    }
                    return $mealIdsArray;
                }
        }

        function generateMealPlanFromMissingCalories($username)
        {
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
                $missingCalories = getMissingCalories($userid, $logindb);
                $diet = getDietaryPreferences($userid, $logindb);
                $intolerance = getIntolerances($userid, $logindb);
                $result = curlGenerateMealPlanFromMissingCalories($missingCalories, $diet, $intolerance);
                $array = get_object_vars($result);

                $mealIdsArray = array();
                for ($i = 0; $i <= 9; $i++) {

                    $mealValue = json_decode($array["items"][$i]->value);
                    $mealId = $mealValue->id;
                    $mealIdsArray[] = $mealId;

                }
                return $mealIdsArray;
            }
        }

        function getIndividualMealInformationAndDisplay($username)
        {
            $mealIdsArray = generateMealPlanFromCalories($username);
            $cardContent = "";
            for ($i = 0; $i <= 8; $i++) {
                $mealId = $mealIdsArray[$i];
                if ($i == 0) {
                    $cardContent .= BootstrapTags::startCardDeck();
                }

                if ($i == 3) {
                    $cardContent .= BootstrapTags::startCardDeck();
                }

                if ($i == 6) {
                    $cardContent .= BootstrapTags::startCardDeck();
                }

                $cardContent .= BootstrapTags::startCard();
                $result = curlGetIndividualMealInformation($mealId);
                //var_dump($result);

                $array = get_object_vars($result);
                //print_r($array);
                $mealTitle = $array["title"];
                $mealReadyInMinutes = $array["readyInMinutes"];
                $mealServings = $array["servings"];
                $mealImage = $array["image"];

                $cardContent .= BootstrapTags::startImageOfCard() . $mealImage . BootstrapTags::closeImageOfCard() . BootstrapTags::startBodyOfCard() . BootstrapTags::startTitleOfCard() . $mealTitle . BootstrapTags::closeTitleOfCard() . BootstrapTags::startTextOfCard();
                $cardContent .= "Servings: " . $mealServings . "</br>" . "Ready in " . $mealReadyInMinutes . "</br>" . BootstrapTags::closeTextOfCard() . BootstrapTags::createLikeButtonOfCard() . $mealId . BootstrapTags::closeLikeButtonOfCard() . BootstrapTags::closeBodyOfCard() . BootstrapTags::createButtonOfCard() . $mealId . BootstrapTags::closeButtonOfCard() . BootstrapTags::createMealButtonOfCard() . $mealId . BootstrapTags::closeMealButtonOfCard() . BootstrapTags::closeCard();

                if ($i == 2) {
                    $cardContent .= BootstrapTags::closeCardDeck();
                }

                if ($i == 5) {
                    $cardContent .= BootstrapTags::closeCardDeck();
                }

                if ($i == 8) {
                    $cardContent .= BootstrapTags::closeCardDeck();
                    break;
                }

            }
            return $cardContent;
        }

        function getIndividualMealInformationForMissingCaloriesAndDisplay($username)
        {
            $mealIdsArray = generateMealPlanFromMissingCalories($username);
            $cardContent = "";
            for ($i = 0; $i <= 8; $i++) {
                $mealId = $mealIdsArray[$i];
                if ($i == 0) {
                    $cardContent .= BootstrapTags::startCardDeck();
                }

                if ($i == 3) {
                    $cardContent .= BootstrapTags::startCardDeck();
                }

                if ($i == 6) {
                    $cardContent .= BootstrapTags::startCardDeck();
                }

                $cardContent .= BootstrapTags::startCard();
                $result = curlGetIndividualMealInformation($mealId);
                //var_dump($result);

                $array = get_object_vars($result);
                //print_r($array);
                $mealTitle = $array["title"];
                $mealReadyInMinutes = $array["readyInMinutes"];
                $mealServings = $array["servings"];
                $mealImage = $array["image"];

                $cardContent .= BootstrapTags::startImageOfCard() . $mealImage . BootstrapTags::closeImageOfCard() . BootstrapTags::startBodyOfCard() . BootstrapTags::startTitleOfCard() . $mealTitle . BootstrapTags::closeTitleOfCard() . BootstrapTags::startTextOfCard();
                $cardContent .= "Servings: " . $mealServings . "</br>" . "Ready in " . $mealReadyInMinutes . "</br>" . BootstrapTags::closeTextOfCard() . BootstrapTags::createLikeButtonOfCard() . $mealId . BootstrapTags::closeLikeButtonOfCard() . BootstrapTags::closeBodyOfCard() . BootstrapTags::createButtonOfCard() . $mealId . BootstrapTags::closeButtonOfCard() . BootstrapTags::createMealButtonOfCard() . $mealId . BootstrapTags::closeMealButtonOfCard() . BootstrapTags::closeCard();

                if ($i == 2) {
                    $cardContent .= BootstrapTags::closeCardDeck();
                }

                if ($i == 5) {
                    $cardContent .= BootstrapTags::closeCardDeck();
                }

                if ($i == 8) {
                    $cardContent .= BootstrapTags::closeCardDeck();
                    break;
                }

            }
            return $cardContent;
        }

        function displayRecommendedRecipes($username)
        {
            $missingCalories = getMissingCalories($username);

            if ($missingCalories > 0) {
                return getIndividualMealInformationForMissingCaloriesAndDisplay($username);
            } else {
                return getIndividualMealInformationAndDisplay($username);
            }
        }


        function addInformationToMealsAndCaloriesTables($username, $mealId)
        {
            $logindb = new mysqli("192.168.2.4", "testUser", "12345", "testdb");
            if (mysqli_connect_errno()) {
                echo "failed to connect to MYSQL:" . mysqli_connect_error();
                exit();
            } else {
                $result = curlGetIndividualMealInformation($mealId);
                $mealTitle = $result->title;
                $calories = $result->nutrition->nutrients[0]->amount;
                $mealDate = date('Y-m-d');

                mysqli_select_db($logindb, "testdb");
                //get userid from users table using username
                $query = "select * from users where username = '$username'";
                $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                    $userid = $result["userid"];
                }//check if its already in meals

                $query = "select * from meals where dishnameid = '$mealId' and date= '$mealDate' and userid='$userid'";
//$query = "select * from meals";
                $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                $row = mysqli_num_rows($runQuery);
                if ($row > 0) {
                    return 1;
                } else {
                    //insert into meals table
                    $query = "insert into meals(userid, dishnameid, dishname, calories, date) values ($userid, '$mealId', '$mealTitle' ,'$calories', '$mealDate')";

                    $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                    //select calories from meals with userid on date and get sum
                    $query = "select sum(calories) as dailycalories from meals where userid = '$userid' and date = '$mealDate'";
                    $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                    while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                        $dailyCalories = $result["dailycalories"];
                    }
                    echo $dailyCalories;
                    //check if there is an entry in the calories table for the given userid and date
                    $query = "select * from calories where userid='$userid' and date='$mealDate'";
                    $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                    $row = mysqli_num_rows($runQuery);
                    if ($row == 0) {
                        $query1 = "insert into calories(userid, dailycalories, date) values ('$userid', '$dailyCalories', '$mealDate')";
                        echo $query;
                        $runQuery = mysqli_query($logindb, $query1) or die(mysqli_error($logindb));
                    } else {
                        $query2 = "update calories set dailycalories = '$dailyCalories' where userid = '$userid' and date = '$mealDate'";
                        echo $query2;
                        $runQuery = mysqli_query($logindb, $query2) or die(mysqli_error($logindb));
                    }

                    echo "Daily calories calculated";
                    return 0;
                }
            }

        }

        function addMealToLikesTable($username, $mealId)
        {
            $logindb = new mysqli("192.168.2.4", "testUser", "12345", "testdb");
            if (mysqli_connect_errno()) {
                echo "failed to connect to MYSQL:" . mysqli_connect_error();
                exit();
            } else {
                $result = curlGetIndividualMealInformation($mealId);
                $mealTitle = $result->title;
                $mealCuisine = $result->cuisines[0];
                $mealDate = date('Y-m-d');


                mysqli_select_db($logindb, "testdb");
                //get userid from users table using username
                $query = "select * from users where username = '$username'";
                $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                    $userid = $result["userid"];
                }//check if its already in meals

                $query = "select * from likes where dishnameid = '$mealId' and date= '$mealDate' and userid='$userid'";
//$query = "select * from meals";
                $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                $row = mysqli_num_rows($runQuery);
                if ($row > 0) {
                    return 1;
                } else {
                    if ($mealCuisine == "") {
                        $mealCuisine = "American";
                        //insert into meals table
                        $query = "insert into likes(userid, dishnameid, dishname, cuisine, date) values ($userid, '$mealId', '$mealTitle' ,'$mealCuisine', '$mealDate')";
                        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                        return 0;
                    } else {
                        $query = "insert into likes(userid, dishnameid, dishname, cuisine, date) values ($userid, '$mealId', '$mealTitle' ,'$mealCuisine', '$mealDate')";
                        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                        return 0;
                    }
                }
            }

        }

        function returnRecipe($mealId)
        {
            $result = curlGetIndividualMealInformation($mealId);
            $ingredients = "";
            for ($i = 0; $i <= sizeof($result->nutrition->ingredients) - 1; $i++) {
                $ingredients .= (($i + 1) . ". " . $result->nutrition->ingredients[$i]->name) . " " . $result->nutrition->ingredients[$i]->amount . " " . $result->nutrition->ingredients[$i]->unit . "<br>";
            }

            $mealTitle = $result->title;
            $mealReadyInMinutes = $result->readyInMinutes . "mins.";

            $calories = $result->nutrition->nutrients[0]->amount . " " . $result->nutrition->nutrients[0]->unit;
            $fat = $result->nutrition->nutrients[1]->amount . " " . $result->nutrition->nutrients[1]->unit;
            $saturatedFat = $result->nutrition->nutrients[2]->amount . " " . $result->nutrition->nutrients[2]->unit;
            $carbohydrates = $result->nutrition->nutrients[3]->amount . " " . $result->nutrition->nutrients[3]->unit;
            $sugar = $result->nutrition->nutrients[4]->amount . " " . $result->nutrition->nutrients[4]->unit;
            $cholesterol = $result->nutrition->nutrients[5]->amount . " " . $result->nutrition->nutrients[5]->unit;
            $sodium = $result->nutrition->nutrients[6]->amount . " " . $result->nutrition->nutrients[6]->unit;
            $protein = $result->nutrition->nutrients[8]->amount . " " . $result->nutrition->nutrients[8]->unit;
            $fiber = $result->nutrition->nutrients[10]->amount . " " . $result->nutrition->nutrients[10]->unit;


            $recipeSteps = "";
            for ($i = 0; $i <= sizeof($result->analyzedInstructions[0]->steps) - 1; $i++) {
                $recipeStepNumber = $result->analyzedInstructions[0]->steps[$i]->number;
                $recipeStep = $result->analyzedInstructions[0]->steps[$i]->step;
                $recipeSteps .= $recipeStepNumber . ". " . $recipeStep . "<br>";

            }
            $recipe = BootstrapTags::createRecipePage($mealTitle, $mealReadyInMinutes, $ingredients, $recipeSteps, $calories, $fat, $saturatedFat, $carbohydrates, $sugar, $cholesterol, $sodium, $protein, $fiber);

            return $recipe;
        }

        function requestProcessor($request)
        {
            echo "received request" . PHP_EOL;
            var_dump($request);
            if (!isset($request['type'])) {
                return "ERROR: unsupported message type";
            }
            switch ($request['type']) {
                case "login":
                    return doLogin($request['username'], $request['password']);
                case "register":
                    return doRegister($request['username'], $request['password'], $request['email'], $request['firstname'], $request['lastname']);
                case "insertinfo":
                    return registerUserInfo($request['username'], $request['age'], $request['weight'], $request['height'], $request['gender'], $request['lifestyle'], $request['vegetarian'], $request['nonvegetarian'], $request['vegan'], $request['pescetarian'], $request['gluten'], $request['dairy'], $request['peanut'], $request['seafood']);
                case "insertmeal":
                    return addInformationToMealsAndCaloriesTables($request['username'], $request['mealId']);
                case "insertlike":
                    return addMealToLikesTable($request['username'], $request['mealId']);
            }
            return array("returnCode" => '0', 'message' => "Server received request and processed");
        }

        $server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

        $server->process_requests('requestProcessor');
        exit();

?>