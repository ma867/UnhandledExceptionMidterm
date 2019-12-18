<?php
require_once ('BootstrapTags.php');
require_once ('BMRFormulas.php');
require_once ('CurlFunctions.php');
class ApplicationFunctions{

    public static function doLogin($username,$password)
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
            //return 1;
            $query = "select * from users where username = '$username'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $userid = $result["userid"];
            }
            $recommendedCalories = self::getRecommendedCalories($userid, $logindb);
            $diet = self::getDietaryPreferences($userid, $logindb);
            $intolerance = self::getIntolerances($userid, $logindb);
            $missingCalories = self::getMissingCalories($recommendedCalories, $userid, $logindb);

            $meals = self::getIndividualMealInformationAndDisplay($recommendedCalories, $diet, $intolerance);
            $recommendedMeals =  self::displayRecommendedRecipes($missingCalories, $diet, $intolerance, $recommendedCalories);

            $reponseArray = array();
            $responseArray['recommendedCalories'] = $recommendedCalories;
            $responseArray['diet'] = $diet;
            $responseArray['intolerance'] = $intolerance;
            $responseArray['meals'] = $meals;
            $responseArray['recommendedMeals'] = $recommendedMeals;
            $responseArray['userid'] = $userid;
            $responseArray['missingCalories'] = $missingCalories;

            return $responseArray;
        }
        else
        {
            echo "user doesn't exist";
            return 0;}

    }

    public static function getAllTheInfoForApi($username){
        $logindb = new mysqli("192.168.2.4","testUser","12345","testdb");
        if(mysqli_connect_errno())
        {
            echo "failed to connect to MYSQL:" . mysqli_connect_error();
            exit();
        }
        else {
            $query = "select * from users where username = '$username'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $userid = $result["userid"];
            }

            $recommendedCalories = self::getRecommendedCalories($userid, $logindb);
            $missingCalories = self::getMissingCalories($recommendedCalories, $userid, $logindb);
            $diet = self::getDietaryPreferences($userid, $logindb);
            $intolerance = self::getIntolerances($userid, $logindb);


            $meals = self::getIndividualMealInformationAndDisplay($recommendedCalories, $diet, $intolerance);
            $recommendedMeals =  self::displayRecommendedRecipes($missingCalories, $diet, $intolerance, $recommendedCalories);

            $reponseArray = array();
            $responseArray['recommendedCalories'] = $recommendedCalories;
            $responseArray['missingCalories'] = $missingCalories;
            $responseArray['diet'] = $diet;
            $responseArray['intolerance'] = $intolerance;
            $responseArray['meals'] = $meals;
            $responseArray['recommendedMeals'] = $recommendedMeals;
            $responseArray['userid'] = $userid;


            return $responseArray;

        }

    }

    public static function doRegister($username, $password, $email, $firstname, $lastname){
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
    public static function registerUserIntolerances($username, $gluten, $dairy, $peanut, $seafood){

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

    public static function registerUserPreferences($username, $vegetarian, $nonvegetarian, $vegan, $pescetarian){
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
    public static function registerUserBMI($username, $age, $weight, $height, $gender, $lifestyle){
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

    public static function registerUserInfo($username, $age, $weight, $height, $gender, $lifestyle, $vegetarian, $nonvegetarian, $vegan, $pescetarian, $gluten, $dairy, $peanut, $seafood){
        self::registerUserBMI($username, $age, $weight, $height, $gender, $lifestyle);
        self::registerUserPreferences($username, $vegetarian, $nonvegetarian, $vegan, $pescetarian);
        self::registerUserIntolerances($username, $gluten, $dairy, $peanut, $seafood);
        $infoArray = self::getAllTheInfoForApi($username);
        return $infoArray;
    }

    public static function getRecommendedCalories($userid, $logindb){

            $query = "select * from bmi where userid = '$userid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $recommendedCalories = $result["reccalories"];
            }
            return $recommendedCalories;
    }

    public static function getMissingCalories($recommendedCalories,$userid, $logindb){
        //$recommendedCalories = self::getRecommendedCalories($userid, $logindb);
            //get userid from users table using username
        $currentDateTime= date('Y-m-d');

        $query = "select * from calories where userid = '$userid' and date = '$currentDateTime'";
        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
        while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
            $consumedCalories = $result["dailycalories"];
        }
        if($consumedCalories == NULL){
           return $missingCalories = 0;
        }
        else {
            $missingCalories = $recommendedCalories - $consumedCalories;
            return $missingCalories;
        }
    }

    public static function getDietaryPreferences($userid, $logindb){
            $query = "select * from preferences where userid = '$userid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $vegetarian = $result["vegetarian"];
                $vegan = $result["vegan"];
                $nonvegetarian = $result["nonvegetarian"];
                $pescetarian = $result["pescetarian"];
            }
            if ($vegetarian == true){
                $diet = "&diet=vegetarian";
                return $diet;
            }
            if($vegan == true){
                $diet = "&diet=vegan";
                return $diet;
            }
            if($nonvegetarian == true){
                $diet = "&diet=whole30";
                return $diet;
            }
            if($pescetarian == true){
                $diet = "&diet=pescetarian";
                return $diet;
            }
    }

    public static function getIntolerances($userid, $logindb){
            $query = "select * from intolerances where userid = '$userid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $dairy = $result["dairy"];
                $gluten = $result["gluten"];
                $peanut = $result["peanut"];
                $seafood = $result["seafood"];
            }
            $intolerance = "";

            if ($dairy == true){
                $intolerance .= "&exclude=dairy";
            }
            if($gluten == true){
                $intolerance .= "&exclude=gluten";
            }
            if($peanut == true){
                $intolerance .= "&exclude=peanut";
            }
            if($seafood == true){
                $intolerance .= "&exclude=seafood";
            }
            return $intolerance;

    }

public static function generateMealPlanFromCalories($recommendedCalories, $diet, $intolerance)
{
    //get recommended calories
    $result = CurlFunctions::curlGenerateMealPlanFromCalories($recommendedCalories, $diet, $intolerance);
    $array = get_object_vars($result);

    $mealIdsArray = array();
    for($i=0; $i <= 9; $i++) {

        $mealValue = json_decode($array["items"][$i]->value);
        $mealId = $mealValue->id;
        $mealIdsArray[]= $mealId;

    }
    return $mealIdsArray;
}

public static function generateMealPlanFromMissingCalories($missingCalories, $diet, $intolerance)
{
    $result = CurlFunctions::curlGenerateMealPlanFromMissingCalories($missingCalories, $diet, $intolerance);
    $array = get_object_vars($result);

    $mealIdsArray = array();
    for($i=0; $i <= 9; $i++) {

        $mealValue = json_decode($array["items"][$i]->value);
        $mealId = $mealValue->id;
        $mealIdsArray[]= $mealId;

    }
    return $mealIdsArray;
}

public static function getIndividualMealInformationAndDisplay($recommendedCalories, $diet, $intolerance){
        $mealIdsArray = self::generateMealPlanFromCalories($recommendedCalories, $diet, $intolerance);
        $cardContent = "";
        for($i=0; $i<= 8; $i++ ){
            $mealId = $mealIdsArray[$i];
            if($i==0){$cardContent .= BootstrapTags::startCardDeck();}

            if($i==3){$cardContent .= BootstrapTags::startCardDeck();}

            if($i==6){$cardContent .= BootstrapTags::startCardDeck();}

            $cardContent .= BootstrapTags::startCard();
            $result = CurlFunctions::curlGetIndividualMealInformation($mealId);
                          //var_dump($result);

                $array = get_object_vars($result);
                //print_r($array);
                $mealTitle = $array["title"];
                $mealReadyInMinutes = $array["readyInMinutes"];
                $mealServings = $array["servings"];
                $mealImage = $array["image"];

                $cardContent .= BootstrapTags::startImageOfCard() . $mealImage . BootstrapTags::closeImageOfCard() . BootstrapTags::startBodyOfCard() . BootstrapTags::startTitleOfCard() . $mealTitle . BootstrapTags::closeTitleOfCard() . BootstrapTags::startTextOfCard();
                $cardContent .= "Servings: " . $mealServings . "</br>" .  "Ready in " . $mealReadyInMinutes . "</br>" . BootstrapTags::closeTextOfCard() . BootstrapTags::createLikeButtonOfCard() . $mealId . BootstrapTags::closeLikeButtonOfCard() . BootstrapTags::closeBodyOfCard() . BootstrapTags::createButtonOfCard() . $mealId . BootstrapTags::closeButtonOfCard() .  BootstrapTags::createMealButtonOfCard() . $mealId . BootstrapTags::closeMealButtonOfCard() . BootstrapTags::closeCard();

                if($i==2){$cardContent .= BootstrapTags::closeCardDeck();}

                if($i==5){$cardContent .= BootstrapTags::closeCardDeck();}

                if($i==8){$cardContent .= BootstrapTags::closeCardDeck(); break;}

        }
        return $cardContent;
}

    public static function getIndividualMealInformationForMissingCaloriesAndDisplay($missingCalories, $diet, $intolerance){
        $mealIdsArray = self::generateMealPlanFromMissingCalories($missingCalories, $diet, $intolerance);
        $cardContent = "";
        for($i=0; $i<= 8; $i++ ){
            $mealId = $mealIdsArray[$i];
            if($i==0){$cardContent .= BootstrapTags::startCardDeck();}

            if($i==3){$cardContent .= BootstrapTags::startCardDeck();}

            if($i==6){$cardContent .= BootstrapTags::startCardDeck();}

            $cardContent .= BootstrapTags::startCard();
            $result = CurlFunctions::curlGetIndividualMealInformation($mealId);
            //var_dump($result);

            $array = get_object_vars($result);
            //print_r($array);
            $mealTitle = $array["title"];
            $mealReadyInMinutes = $array["readyInMinutes"];
            $mealServings = $array["servings"];
            $mealImage = $array["image"];

            $cardContent .= BootstrapTags::startImageOfCard() . $mealImage . BootstrapTags::closeImageOfCard() . BootstrapTags::startBodyOfCard() . BootstrapTags::startTitleOfCard() . $mealTitle . BootstrapTags::closeTitleOfCard() . BootstrapTags::startTextOfCard();
            $cardContent .= "Servings: " . $mealServings . "</br>" .  "Ready in " . $mealReadyInMinutes . "</br>" . BootstrapTags::closeTextOfCard() . BootstrapTags::createLikeButtonOfCard() . $mealId . BootstrapTags::closeLikeButtonOfCard() . BootstrapTags::closeBodyOfCard() . BootstrapTags::createButtonOfCard() . $mealId . BootstrapTags::closeButtonOfCard().  BootstrapTags::createMealButtonOfCard() . $mealId . BootstrapTags::closeMealButtonOfCard() . BootstrapTags::closeCard();

            if($i==2){$cardContent .= BootstrapTags::closeCardDeck();}

            if($i==5){$cardContent .= BootstrapTags::closeCardDeck();}

            if($i==8){$cardContent .= BootstrapTags::closeCardDeck(); break;}

        }
        return $cardContent;
    }

    public static function displayRecommendedRecipes($missingCalories, $diet, $intolerance, $recommendedCalories){
       // $missingCalories = self::getMissingCalories($userid, $logindb);
        if($missingCalories > 0){
            return self::getIndividualMealInformationForMissingCaloriesAndDisplay($missingCalories, $diet, $intolerance);
        }
        else{
            return self::getIndividualMealInformationAndDisplay($recommendedCalories, $diet, $intolerance);
        }
    }


    public static function addInformationToMealsAndCaloriesTables($username, $mealId)
    {
        $logindb = new mysqli("192.168.2.4", "testUser", "12345", "testdb");
        if (mysqli_connect_errno()) {
            echo "failed to connect to MYSQL:" . mysqli_connect_error();
            exit();
        }
        else {
            $result = CurlFunctions::curlGetIndividualMealInformation($mealId);
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
            }
            else{
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
                 }
                else {
                        $query2 = "update calories set dailycalories = '$dailyCalories' where userid = '$userid' and date = '$mealDate'";
                        echo $query2;
                        $runQuery = mysqli_query($logindb, $query2) or die(mysqli_error($logindb));
                 }

                $recommendedCalories = self::getRecommendedCalories($userid, $logindb);
                $diet = self::getDietaryPreferences($userid, $logindb);
                $intolerance = self::getIntolerances($userid, $logindb);
                $missingCalories = self::getMissingCalories($recommendedCalories, $userid, $logindb);
                $recommendedMeals =  self::displayRecommendedRecipes($missingCalories, $diet, $intolerance, $recommendedCalories);
                echo "Daily calories calculated";
                echo $recommendedMeals;
                return $recommendedMeals;
            }
        }

    }

    public static function addMealToLikesTable($username, $mealId)
    {
        $logindb = new mysqli("192.168.2.4", "testUser", "12345", "testdb");
        if (mysqli_connect_errno()) {
            echo "failed to connect to MYSQL:" . mysqli_connect_error();
            exit();
        }
        else {
            $result = CurlFunctions::curlGetIndividualMealInformation($mealId);
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
            }
            else{
                if($mealCuisine=="") {
                    $mealCuisine = "American";
                    //insert into meals table
                    $query = "insert into likes(userid, dishnameid, dishname, cuisine, date) values ($userid, '$mealId', '$mealTitle' ,'$mealCuisine', '$mealDate')";
                    $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                    return 0;
                }
                else {
                    $query = "insert into likes(userid, dishnameid, dishname, cuisine, date) values ($userid, '$mealId', '$mealTitle' ,'$mealCuisine', '$mealDate')";
                    $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                    return 0;
                }
            }
        }

    }

    /*
public static function returnIngredientInformation($username, $mealId, $includedingredients, $discardedingredients){
        $logindb = new mysqli("192.168.2.4", "testUser", "12345", "testdb");
        if (mysqli_connect_errno()) {
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
            echo "got userid";
            //cll api to get json result and store it

            $result = CurlFunctions::curlGetIndividualMealInformation($mealId);
            echo "got curl result";
            //get total calories
            $totalcalories = $result->nutrition->nutrients[0]->amount;
            //get datetime
            echo "got calories from curl";
            $datetime = date('Y-m-d H:i:s');
            echo "datetime is" . $datetime . "\n";

            echo "got datetime";
            //insert to modified meals to create modmealid
            $query = "insert into modifiedmeals(userid, dishnameid, totalcalories, datetime) values ('$userid', '$mealId' ,'$totalcalories', '$datetime')";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));


            echo "inserted into modifiedmeals query is" . $query . "\n";


            $query = "select * from modifiedmeals where userid = '$userid' and datetime = '$datetime'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $modmealid = $result["modmealid"];
            }

            echo "got modifiedmealid" . $modmealid;


            $result = CurlFunctions::curlGetIndividualMealInformation($mealId);
             $modifiedIngredientList = "";

            for ($i = 0; $i <= sizeof($result->nutrition->ingredients) - 1; $i++) {

                $ingredientName = $result->nutrition->ingredients[$i]->name;
                $ingredientAmount =  $result->nutrition->ingredients[$i]->amount . " " . $result->nutrition->ingredients[$i]->unit;

                 if (! in_array($ingredientName, $discardedingredients)){

                    $modifiedIngredientList .= "- " . $ingredientName . " " . $ingredientAmount . "<br>";

                 }
                else {
                    for ($i = 0; $i <= sizeof($result->nutrition->ingredients) - 1; $i++) {
                        $discardedIngredientCalories = $discardedingredients[$i][0];
                        $discardedIngredientName = $discardedingredients[$i][1];
                        $query = "insert into discardedingredients(modmealid, userid, ingredientname, ingredientcalories) values ('$mealId', '$userid', '$discardedIngredientName', '$discardedIngredientCalories')";
                        $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
                    }
                }
            }
            echo "got modifiedmealid" . $modmealid . "</br>";
            $query = "select sum(ingredientcalories) as totalingredientcalories from discardedingredients where modmealid= '$modmealid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $totalingredientcalories = $result["totalingredientcalories"];
            }
            echo "totalingredients" . $totalingredientcalories;
            $newCaloricAmount = $totalcalories - $totalingredientcalories;
            echo "totalcal" . $totalcalories;
            $query = "update modifiedmeals set totalcalories = '$newCaloricAmount' where modmealid = '$modmealid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));

            $modifiedCaloriesAndIngredients = array();
            $modifiedCaloriesAndIngredients['calories'] = $newCaloricAmount;
            $modifiedCaloriesAndIngredients['ingredients'] = $modifiedIngredientList;

            $newRecipe = self::returnModifiedRecipe($mealId, $modifiedCaloriesAndIngredients);
            return $newRecipe;

        }
}

    */

    public static function returnIngredientInformation($username, $mealId, $includedingredients, $discardedingredients){
        $logindb = new mysqli("192.168.2.4", "testUser", "12345", "testdb");
        if (mysqli_connect_errno()) {
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
               //cll api to get json result and store it

            $result = CurlFunctions::curlGetIndividualMealInformation($mealId);
            //get total calories
            $totalcalories = $result->nutrition->nutrients[0]->amount;
            //get datetime
            $datetime = date('Y-m-d H:i:s');
            //insert to modified meals to create modmealid
            $query = "insert into modifiedmeals(userid, dishnameid, totalcalories, datetime) values ('$userid', '$mealId' ,'$totalcalories', '$datetime')";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));

            $query = "select * from modifiedmeals where userid = '$userid' and datetime = '$datetime'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $modmealid = $result["modmealid"];
            }

            $result = CurlFunctions::curlGetIndividualMealInformation($mealId);

           // echo $result ."\n";
            $modifiedIngredientList = array();
            print_r($includedingredients);
            print_r($discardedingredients);
            $il = array();
            for ($i = 0; $i < sizeof($includedingredients); $i++) {

               // echo $includedingredients[$i][1] ." \n";
                for ($j = 0; $j < sizeof($result->nutrition->ingredients); $j++) {
                    $ingredientName = $result->nutrition->ingredients[$j]->name;
                    $ingredientAmount = $result->nutrition->ingredients[$j]->amount . " " . $result->nutrition->ingredients[$j]->unit;

                    if($ingredientName != $includedingredients[$i][1]){
                        continue;
                    }
                    else{
                        $modifiedIngredientList[]=$ingredientName."-".$ingredientAmount;
                       break;
                    }
                }

            }

            print_r($modifiedIngredientList) ."\n";

            /*for ($i = 0; $i < sizeof($discardedingredients); $i++) {

               // echo $discardedingredients[$i][1] ." \n";

            }*/
            for ($i = 0; $i <sizeof($discardedingredients); $i++) {
                $discardedIngredientCalories = $discardedingredients[$i][0];
                $discardedIngredientName = $discardedingredients[$i][1];
                $query = "insert into discardedingredients(modmealid, userid, ingredientname, ingredientcalories) values ('$modmealid', '$userid', '$discardedIngredientName', '$discardedIngredientCalories')";
                $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            }

            $query = "select sum(ingredientcalories) as totalingredientcalories from discardedingredients where modmealid= '$modmealid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $totalingredientcalories = $result["totalingredientcalories"];
            }

            $newCaloricAmount = $totalcalories - $totalingredientcalories;
            //echo $modifiedIngredientList ."\n";
              //  return $newRecipe;

            echo $totalcalories . "total & ing " . $totalingredientcalories;


            echo "new cal amount" .  $newCaloricAmount;
            $query = "update modifiedmeals set totalcalories = '$newCaloricAmount' where modmealid = '$modmealid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));

            $modIngredients="";
            foreach($modifiedIngredientList as &$ingredient){
                $modIngredients.= "-".$ingredient."<br>";
            }

            echo "\n\nFinal Modified List\n\n";
            echo $modIngredients."\n";

            $modifiedCaloriesAndIngredients = array();
            $modifiedCaloriesAndIngredients['calories'] = $newCaloricAmount;
            $modifiedCaloriesAndIngredients['ingredients'] = $modIngredients;

           // echo "yo modified calories lit yo i hate this ssssshit \n";
            $newRecipe = self::returnModifiedRecipe($mealId, $modifiedCaloriesAndIngredients);
            echo "THis shit better work soon\n\n";

            echo $newRecipe;
            //return $newRecipe;
            return 1;

            }
            //got through list of ingredients



    }

public static function returnRegularRecipe($mealId)
{

        $result = CurlFunctions::curlGetIndividualMealInformation($mealId);
        $ingredients = "";
        for ($i = 0; $i <= sizeof($result->nutrition->ingredients) - 1; $i++) {
            $ingredients .= (($i + 1) . ". " . $result->nutrition->ingredients[$i]->name) . " " . $result->nutrition->ingredients[$i]->amount . " " . $result->nutrition->ingredients[$i]->unit . "<br>";
        }

        $modifiedIngredients = "<form name=\"thisForm\">";
        for ($i = 0; $i <= sizeof($result->nutrition->ingredients) - 1; $i++) {

            for ($j = 0; $j < sizeof($result->nutrition->ingredients[$i]->nutrients); $j++) {
                if ($result->nutrition->ingredients[$i]->nutrients[$j]->name == "Calories") {
                    $modifiedIngredientCalories = $result->nutrition->ingredients[$i]->nutrients[$j]->amount;
                    break;
                }
            }

            //  $modifiedIngredients .= "<div class=\"form-check\"><input class=\"form-check-input\" type=\"checkbox\" name=\"" . $result->nutrition->ingredients[$i]->name . "\" id=\"" . $result->nutrition->ingredients[$i]->name ;
            //  $modifiedIngredients .= "\"><label class=\"form-check-label\" for=\"" . $result->nutrition->ingredients[$i]->name . "\">" . $result->nutrition->ingredients[$i]->name .  " " . $result->nutrition->ingredients[$i]->amount . " " . $result->nutrition->ingredients[$i]->unit . " | " . " Calories:" . $result->nutrition->ingredients[$i]->nutrients[21]->amount . " </label></div></br>";
            $modifiedIngredients .= "<input type=\"checkbox\" name=\"" . $result->nutrition->ingredients[$i]->name . "\" value=\"" . $modifiedIngredientCalories . "\">";
            $modifiedIngredients .= $result->nutrition->ingredients[$i]->name . " " . $result->nutrition->ingredients[$i]->amount . " " . $result->nutrition->ingredients[$i]->unit . " | " . " Calories:" . $modifiedIngredientCalories . "</br>";
        }

        $modifiedIngredients .= "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button><input type=\"button\" value=\"Submit\" onclick=\"loopForm(document.thisForm);\" class=\"btn btn-danger\"></form>";


        $mealId = $mealId;
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
        $recipe = BootstrapTags::createRecipePage($mealId, $mealTitle, $mealReadyInMinutes, $modifiedIngredients, $ingredients, $recipeSteps, $calories, $fat, $saturatedFat, $carbohydrates, $sugar, $cholesterol, $sodium, $protein, $fiber);

        return $recipe;

    }

    public static function returnModifiedRecipe($mealId, $modifiedCaloriesAndIngredients)
    {
        $result =  CurlFunctions::curlGetIndividualMealInformation($mealId);
        $ingredients = $modifiedCaloriesAndIngredients['ingredients'];

        $mealTitle = $result->title;
        $mealReadyInMinutes = $result->readyInMinutes . "mins.";
        $calories = $modifiedCaloriesAndIngredients['calories'] . " " . $result->nutrition->nutrients[0]->unit;
        $fat = $result->nutrition->nutrients[1]->amount . " " . $result->nutrition->nutrients[1]->unit;
        $saturatedFat = $result->nutrition->nutrients[2]->amount . " " . $result->nutrition->nutrients[2]->unit;
        $carbohydrates =  $result->nutrition->nutrients[3]->amount . " " . $result->nutrition->nutrients[3]->unit;
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
        $recipe = BootstrapTags::createModifiedRecipePage($mealTitle, $mealReadyInMinutes, $ingredients, $recipeSteps, $calories, $fat, $saturatedFat, $carbohydrates, $sugar, $cholesterol, $sodium, $protein, $fiber);

        return $recipe;
    }

}

?>
