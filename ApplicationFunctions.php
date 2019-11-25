<?php
require_once ('BootstrapTags.php');
require_once ('BMRFormulas.php');
require_once ('CurlFunctions.php');
class ApplicationFunctions{

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
                $recommendedMeals =  ApplicationFunctions::displayRecommendedRecipes($missingCalories, $diet, $intolerance, $recommendedCalories);
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

public static function returnRecipe($mealId)
    {
        $result =  CurlFunctions::curlGetIndividualMealInformation($mealId);
        $ingredients = "";
        for ($i = 0; $i <= sizeof($result->nutrition->ingredients) - 1; $i++) {
            $ingredients .= (($i + 1) . ". " . $result->nutrition->ingredients[$i]->name) . " " . $result->nutrition->ingredients[$i]->amount . " " . $result->nutrition->ingredients[$i]->unit . "<br>";
        }

        $mealTitle = $result->title;
        $mealReadyInMinutes = $result->readyInMinutes . "mins.";

        $calories = $result->nutrition->nutrients[0]->amount . " " . $result->nutrition->nutrients[0]->unit;
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
        $recipe = BootstrapTags::createRecipePage($mealTitle, $mealReadyInMinutes, $ingredients, $recipeSteps, $calories, $fat, $saturatedFat, $carbohydrates, $sugar, $cholesterol, $sodium, $protein, $fiber);

        return $recipe;
    }
}

?>
