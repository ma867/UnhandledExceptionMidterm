<?php
/**
 * Created by PhpStorm.
 * User: MAlzate
 * Date: 11/18/2019
 * Time: 2:58 PM
 */

class CurlFunctions
{
    public static function curlGenerateMealPlanFromCalories($recommendedCalories, $diet, $intolerance){
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

    public static function curlGenerateMealPlanFromMissingCalories($missingCalories, $diet, $intolerance){
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

    public static function curlGetIndividualMealInformation($mealId){
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
}