<?php
require_once ('BootstrapTags.php');
require_once ('BMRFormulas.php');
class curlFunctions{

    public static function getRecommendedCalories($username){

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
            $query = "select * from bmi where userid = '$userid'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $recommendedCalories = $result["reccalories"];
            }
            return $recommendedCalories;
        }
    }
    public static function getMissingCalories($username){
        $recommendedCalories = self::getRecommendedCalories($username);
        $logindb = new mysqli("192.168.2.4","testUser","12345","testdb");
        if(mysqli_connect_errno())
        {
            echo "failed to connect to MYSQL:" . mysqli_connect_error();
            exit();
        }
        else {
            mysqli_select_db($logindb, "testdb");
            //get userid from users table using username
            $currentDateTime= date('Y-m-d');
            $query = "select * from users where username = '$username'";
            $runQuery = mysqli_query($logindb, $query) or die(mysqli_error($logindb));
            while ($result = mysqli_fetch_array($runQuery, MYSQLI_ASSOC)) {
                $userid = $result["userid"];

            }
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
    }
    public static function getDietaryPreferences($username){
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


    }

    public static function getIntolerances($username){
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

    }
public static function generateMealPlanFromCalories($username)
{
    //get recommended calories
    $recommendedCalories = self::getRecommendedCalories($username);
    $diet = self::getDietaryPreferences($username);
    $intolerance = self::getIntolerances($username);
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

         } catch (Exception $e) {

        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);

    }
    $array = get_object_vars($result);

    $mealIdsArray = array();
    for($i=0; $i <= 9; $i++) {

        $mealValue = json_decode($array["items"][$i]->value);
        $mealId = $mealValue->id;
        $mealIdsArray[]= $mealId;

    }
    return $mealIdsArray;
}

public static function generateMealPlanFromMissingCalories($username)
{
    //get recommended calories
    $missingCalories = self::getMissingCalories($username);
    $diet = self::getDietaryPreferences($username);
    $intolerance = self::getIntolerances($username);
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

        //var_dump($result);

    } catch (Exception $e) {

        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);

    }
    $array = get_object_vars($result);

    $mealIdsArray = array();
    for($i=0; $i <= 9; $i++) {

        $mealValue = json_decode($array["items"][$i]->value);
        $mealId = $mealValue->id;
        $mealIdsArray[]= $mealId;

    }
    return $mealIdsArray;
}

public static function getIndividualMealInformationAndDisplay($username){
        $mealIdsArray = self::generateMealPlanFromCalories($username);
        $cardContent = "";
        for($i=0; $i<= 8; $i++ ){
            $mealId = $mealIdsArray[$i];
            if($i==0){$cardContent .= BootstrapTags::startCardDeck();}

            if($i==3){$cardContent .= BootstrapTags::startCardDeck();}

            if($i==6){$cardContent .= BootstrapTags::startCardDeck();}

            $cardContent .= BootstrapTags::startCard();
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
                } catch(Exception $e) {

                    trigger_error(sprintf(
                        'Curl failed with error #%d: %s',
                        $e->getCode(), $e->getMessage()),
                        E_USER_ERROR);

                }
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

    public static function getIndividualMealInformationForMissingCaloriesAndDisplay($username){
        $mealIdsArray = self::generateMealPlanFromMissingCalories($username);
        $cardContent = "";
        for($i=0; $i<= 8; $i++ ){
            $mealId = $mealIdsArray[$i];
            if($i==0){$cardContent .= BootstrapTags::startCardDeck();}

            if($i==3){$cardContent .= BootstrapTags::startCardDeck();}

            if($i==6){$cardContent .= BootstrapTags::startCardDeck();}

            $cardContent .= BootstrapTags::startCard();
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
            } catch(Exception $e) {

                trigger_error(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage()),
                    E_USER_ERROR);

            }
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

    public static function displayRecommendedRecipes($username){
        $missingCalories = self::getMissingCalories($username);

        if($missingCalories > 0){
            return self::getIndividualMealInformationForMissingCaloriesAndDisplay($username);
        }
        else{
            return self::getIndividualMealInformationAndDisplay($username);
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
            try {

                $url = "https://api.spoonacular.com/recipes/" . $mealId . "/information?apiKey=23f5be8cdf814b51a9307dc2be2cbca3&includeNutrition=true";

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
		
		 $mealTitle = $result->title;
           	 $calories = $result->nutrition->nutrients[0]->amount;
           	 $mealDate = date('Y-m-d');

            } catch (Exception $e) {

                trigger_error(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage()),
                    E_USER_ERROR);

            }
           

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
          
           /*     if ($dailyCalories == NULL) {
		    
                    
                }  
                else {
                    $query2 = "update calories set dailycalories = '$dailyCalories' where userid = '$userid' and date = '$mealDate'";
		echo $query2;
                    $runQuery = mysqli_query($logindb, $query2) or die(mysqli_error($logindb));
                }
*/
echo "Daily calories calculated";
                return 0;
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
            try {

                $url = "https://api.spoonacular.com/recipes/" . $mealId . "/information?apiKey=23f5be8cdf814b51a9307dc2be2cbca3&includeNutrition=true";

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

                $mealTitle = $result->title;
                $mealCuisine = $result->cuisines[0];
                $mealDate = date('Y-m-d');

            } catch (Exception $e) {

                trigger_error(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage()),
                    E_USER_ERROR);

            }


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
        try {

            $url = "https://api.spoonacular.com/recipes/" . $mealId . "/information?apiKey=23f5be8cdf814b51a9307dc2be2cbca3&includeNutrition=true";

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




        } catch (Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);


        }
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
        $recipe = "<div class=\"container\"><h2>" . $mealTitle . "</h2><div class=\"w3-card-4\" style=\"width:70%\"><p>Cook time:" .  $mealReadyInMinutes . "</p></div><section><h4>Recipe</h4><h6><b>Ingredients</b></h6><p>" . $ingredients;
        $recipe .= "<p></section><section><h6><b>Directions</b></h6><p>" . $recipeSteps . "</p></section><section>";
        $recipe .= "<h4>Nutrition info</h4><table class=\"table table-borderless\"><tbody><tr><th scope=\"row\">Calories:</th><td>" . $calories;
        $recipe .= "</td></tr><tr><th scope=\"row\">Fat:</th><td>" . $fat;
        $recipe .= "</td></tr><tr><th scope=\"row\">Saturated Fat:</th><td>" . $saturatedFat;
        $recipe .= "</td></tr><tr><th scope=\"row\">Carbohydrates:</th><td>" . $carbohydrates;
        $recipe .= "</td></tr><tr><th scope=\"row\">Sugar:</th><td>" . $sugar;
        $recipe .= "</td></tr><tr><th scope=\"row\">Cholesterol:</th><td>" . $cholesterol;
        $recipe .= "</td></tr><tr><th scope=\"row\">Sodium:</th><td>" . $sodium;
        $recipe .= "</td></tr><tr><th scope=\"row\">Protein:</th><td>" . $protein;
        $recipe .= "</td></tr><tr><th scope=\"row\">Fiber:</th><td>" . $fiber;
        $recipe .= "</td></tr></tbody></table></section></div>";

        return $recipe;
    }
}
?>
