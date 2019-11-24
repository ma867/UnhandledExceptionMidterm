<?php
session_start();
?>
<html lang="en">

<head>
    <title>Home</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">

    <!--style-->
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
</head>

<!--Navigation Bar-->

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


<!--Welcome Message-->
<h4 style="text-align:center;"> Welcome!</h4>

<!--Cards-->
<?php
include('testRabbitMQServer.php');
    $username= $_SESSION["username"];
    $meals = getIndividualMealInformationAndDisplay($username);
    echo $meals;
?>


<!--About Section-->
<section class="page-section bg-danger" id="about">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h2 class="text-white mt-4">About</h2>
                <hr class="divider light my-4">
                <p class="text-white-50 mb-4">At Healthy Meal Plans, we understand that ‘healthy’ actually means something
                    different to everyone so we’ve worked tirelessly to ensure that this site offers options no matter what your taste, cooking experience, or dietary preferences are. </p>
                <a class="btn btn-light btn-xl js-scroll-trigger" href="#services">Get Started!</a>
            </div>
        </div>
    </div>
</section>





<!--Feauture Section-->
<section class="page-section " id="services">
    <div class="container">
        <h2 class="text-center mt-4">Services</h2>
        <hr class="divider light my-4">
        <div class="row">
            <div class="col-lg-4">
                <div class="img-with-text">
                    <img src="images/calories.jpg" alt="sometext" />
                    <h4>BMI Calories</h4>
                    <p>User can set up profile with calculated BMI, Calorie diet</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="img-with-text">
                    <img src="images/mealplan.png" alt="sometext" />
                    <h4>Meal Plan</h4>
                    <p>Suggested healthy meal plans featuring several delicious recipes for every taste and dietary preference.</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="img-with-text">
                    <img src="images/browse.png" alt="sometext" />
                    <h4> Browse search Recipes </h4>
                    <p>Search for your own recipes using the search and filter tool.</p>
                </div>
            </div>

        </div>
    </div>

</section><br>






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
