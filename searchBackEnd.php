<?php
$query = $_GET["query"];

$url = "https://api.spoonacular.com/recipes/search?apiKey=23f5be8cdf814b51a9307dc2be2cbca3&query=" . $query;

$fp = fopen ( $url , "r" );
$contents = "";
while ( $more = fread ( $fp, 1000  ) ) {
    $contents .=  $more ;
}

echo $contents;

?>
