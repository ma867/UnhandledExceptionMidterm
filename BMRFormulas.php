<?php
/**
 * Created by PhpStorm.
 * User: MAlzate
 * Date: 10/10/2019
 * Time: 1:58 PM
 */

class BMRFormulas
{

    public static function calculateCalories($age, $weight, $height, $gender, $lifestyle){
        $bmr = self::genderCalories($age, $weight, $height, $gender);
        $recommendedCalories = self::lifestyleCalories($bmr, $lifestyle);
        return $recommendedCalories;
    }

    public static function genderCalories($age, $weight, $height, $gender){

        switch($gender){
            case "male":
                $bmr = (66 + (6.3 * $weight) + (12.9 * $height) - (6.8 * $age));
                return $bmr;
            case "female":
                $bmr = (655 + (4.3 * $weight) + (4.7 * $height) - (4.7 * $age));
                return $bmr;
            case "other":
                $bmr = (66 + (6.3 * $weight) + (12.9 * $height) - (6.8 * $age));
                return $bmr;
            default:
                $bmr = (66 + (6.3 * $weight) + (12.9 * $height) - (6.8 * $age));
                return $bmr;
        }
    }

    public static function lifestyleCalories($bmr, $lifestyle){
       switch($lifestyle){
           case "sedentary":
               return (round($bmr * 1.2));
           case "lightlyactive":
                return (round($bmr * 1.375));
           case "moderatelyactive":
               return (round($bmr * 1.55));
           case "veryactive":
               return (round($bmr * 1.725));
           case "extraactive":
               return (round($bmr * 1.9));
           default:
               return (round($bmr * 1.2));
       }
    }
}
