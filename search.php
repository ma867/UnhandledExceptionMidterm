<?php
session_start();
?>
<!doctype html>
<html lang="en">

<head>
    <title>Search</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="jquery/jquery.min.js">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <style>

    #about{
        color:red;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }

    #button{
        background-color: #f44336;
        border-color:#f44336;
        color: #ffffff;
    }

    .container{

        padding: 2px 16px;

    }
    .card{

    }
    .card-img-top {
        width: 100%;
        height: 40vh;
        object-fit: cover;
    }
    .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }



    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script type="text/javascript">
    $(document).ready( function(){

        $("#button").click( function() {

            var s = $('#A').val() ;
            var ingredients = $('#B').val() ;
            var cleanIngredients = ingredients.replace(/ /g,'').split(",");
            var i;
            var stuff="%26excludeIngredients=";
            for(i = 0; i < cleanIngredients.length; i++){

                stuff += "," + cleanIngredients[i];
            }

            $.ajax({

                    type: 		"GET"  ,
                    url: 		"searchBackEnd.php", //url of php script
                    data: 		"query=" + s+ stuff +"",

                    beforeSend: function(){ $("#C").html("waiting....") ;},

                    error: 		function(xhr, status, error) {
                alert( "Error Message:  \r\nNumeric code is: "  + xhr.status + " \r\nError is " + error); 	},

                    success: 	function(result){
                w = JSON.parse(result);
                cardContent= "";
                for(i=0; i<=8; i++) {
                    if(i==0){cardContent += "<div class=\"container\" ><div class=\"row\"><div class=\"card-deck\">"}
                    if(i==3){cardContent += "<div class=\"container\" ><div class=\"row\"><div class=\"card-deck\">"}
                    if(i==6){cardContent += "<div class=\"container\" ><div class=\"row\"><div class=\"card-deck\">"}

                    cardContent += "<div class=\"col-lg-4\"><div class=\"card\">";
                    imageurl = w.baseUri;
                    mealId = w.results[i].id;
                    mealTitle = w.results[i].title;
                    mealReadyInMinutes = w.results[i].readyInMinutes;
                    mealServings = w.results[i].servings;
                    mealImage = w.results[i].image;



                    cardContent += "<img class=\"card-img-top\" src=\"" + imageurl + mealImage + "\" alt=\"Card image cap\">" + "<div class=\"card-body\">" + "<h5 class=\"card-title\">" + mealTitle + "</h5>" + "<p class=\"card-text\">";
                    cardContent += "Servings: " + mealServings + "</br>" +  "Ready in " + mealReadyInMinutes + "</br>" + "</p>" + "<a href=\"like.php?mealId=" + mealId + "\" class=\"btn btn-danger\">like</a>" +"</div>"+  "<div class=\"btn-block\" style=\"text-align: center;\"><a href=\"recipeBackend.php?mealId=" + mealId + "\" class=\"btn btn-danger\">recipe</a><a href=\"addmeal.php?mealId=" + mealId + "\" class=\"btn btn-danger\">add meal</a></div><br></div></div>";

                    if(i==2){cardContent += "</div></div></div></br>";}
                    if(i==5){cardContent += "</div></div></div></br>";}
                    if(i==8){cardContent += "</div></div></div></br>";}

                }
                $("#C").html(cardContent);
            },
                });
            });
    });

    </script>

</head>
<body>
<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <a class="navbar-brand"  href="#">Our project</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar7">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-collapse collapse justify-content-stretch" id="navbar7">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="home.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="search.php">Search</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php" >Log Out</a>
            </li>

        </ul>
    </div>
</nav><br>

<div class="container">
    <div class="row">
        <div class="col-sm-5">
            <input type=text id="A" placeholder="ie. Fettucine Alfredo" class="form-control"  style="display:inline-block;"><br><br>
        </div>
        <div class="col-sm-5">
            <input type=text id="B" placeholder="ie. Fettucine Alfredo" class="form-control"  style="display:inline-block;"><br><br>
        </div>
        <div class="col-sm-4">
    <input type=submit id = "button" style="display:inline-block;" class="btn btn-danger btn-search"></p>
        </div>
    </div>
  </div>
<div id="C" ></div><br>

<!--recommended Meals-->
<section class="page-section bg-danger" id="about">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h2 class="text-white mt-4">Recommended Meals</h2>
                <hr class="divider light my-4">
                <p class="text-white-50 mb-4">Below are some recommended meals for your target calories. </p>
                <!--Cards-->
                <?php
                include('ApplicationFunctions.php');
                $username= $_SESSION["username"];
                $recommendedCalories = $_SESSION["recommendedcalories"];
                $diet = $_SESSION["diet"];
                $intolerance = $_SESSION["intolerance"];
                $recommendedMeals = $_SESSION['recommendedmeals'];
                echo $recommendedMeals;
                ?>
            </div>
        </div>
    </div>
</section>
<!--Footer section-->
<footer class="container-fluid py-5" style="background-color: white;">
    <div class="row">
        <div class="col-12 col-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="d-block mb-2"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>
            <small class="d-block mb-3 text-muted">&copy; 2017-2018</small>
        </div>
        <div class="col-6 col-md">
            <h5>Features</h5>
            <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="#">Cool stuff</a></li>
                <li><a class="text-muted" href="#">Random feature</a></li>
                <li><a class="text-muted" href="#">Team feature</a></li>
                <li><a class="text-muted" href="#">Stuff for developers</a></li>
                <li><a class="text-muted" href="#">Another one</a></li>
                <li><a class="text-muted" href="#">Last time</a></li>
            </ul>
        </div>
        <div class="col-6 col-md">
            <h5>Resources</h5>
            <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="#">Resource</a></li>
                <li><a class="text-muted" href="#">Resource name</a></li>
                <li><a class="text-muted" href="#">Another resource</a></li>
                <li><a class="text-muted" href="#">Final resource</a></li>
            </ul>
        </div>
        <div class="col-6 col-md">
            <h5>Resources</h5>
            <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="#">Business</a></li>
                <li><a class="text-muted" href="#">Education</a></li>
                <li><a class="text-muted" href="#">Government</a></li>
                <li><a class="text-muted" href="#">Gaming</a></li>
            </ul>
        </div>
        <div class="col-6 col-md">
            <h5>About</h5>
            <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="#">Team</a></li>
                <li><a class="text-muted" href="#">Locations</a></li>
                <li><a class="text-muted" href="#">Privacy</a></li>
                <li><a class="text-muted" href="#">Terms</a></li>
            </ul>
        </div>
    </div>
</footer>
<!-- Bootstrap core JavaScript -->
<script src="jquery/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
