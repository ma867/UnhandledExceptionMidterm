<?php
/**
 * Created by PhpStorm.
 * User: MAlzate
 * Date: 10/23/2019
 * Time: 5:04 PM
 */

class BootstrapTags
{
    public static function startCardDeck(){
        return "<div class=\"container\" ><div class=\"row\"><div class=\"card-deck\">";
    }
    public static function closeCardDeck(){
        return "</div></div></div></br>";
    }
    public static function startCard(){
        return "<div class=\"col-lg-4\"><div class=\"card\">";
    }
    public static function closeCard(){
        return "</div></div>";
    }

    public static function startImageOfCard(){
        return "<img class=\"card-img-top\" src=\"";
    }

    public static function closeImageOfCard(){
        return "\" alt=\"Card image cap\">";
    }
    public static function startBodyOfCard(){
        return "<div class=\"card-body\">";
    }
    public static function closeBodyOfCard(){
        return "</div>";
    }
    public static function startTitleOfCard(){
        return "<h5 class=\"card-title\">";
    }
    public static function closeTitleOfCard(){
        return "</h5>";
    }
    public static function startTextOfCard(){
        return "<p class=\"card-text\">";
    }
    public static function closeTextOfCard(){
        return "</p>";
    }
    public static function createButtonOfCard(){
        return " <div class=\"btn-block\" style=\"text-align: center;\"><a href=\"recipeBackend.php?mealId=";
    }

    public static function closeButtonOfCard(){
        return "\" class=\"btn btn-danger\">recipe</a>" ;
    }
    public static function createMealButtonOfCard(){
        return "<a href=\"addmeal.php?mealId=";
    }

    public static function closeMealButtonOfCard(){
        return "\" class=\"btn btn-danger\">add meal</a></div><br>" ;
    }
    public static function createLikeButtonOfCard(){
        return " <div class=\"btn-block\" style=\"text-align: center;\"><a href=\"like.php?mealId=";
    }
    public static function closeLikeButtonOfCard(){
        return "\" class=\"btn btn-danger\">Like</a></div><br>" ;
    }

    public static function createRecipePage($mealTitle, $mealReadyInMinutes, $ingredients, $recipeSteps, $calories, $fat, $saturatedFat, $carbohydrates, $sugar, $cholesterol, $sodium, $protein, $fiber){
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