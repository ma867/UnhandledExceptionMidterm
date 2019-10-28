<?php
session_start();
?>
<html lang="en">
<head>
  <title>Unsucessful</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
  <style>

	p, h1, h2, h3, h4, h5 {
    margin: 0;
}

body{
    margin: 0;
    padding: 0;

    font-weight: 400;
    background: #f3f3f3;
}

.error-wrapper {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.error-wrapper .title {
    font-size: 32px;
    font-weight: 700;
    color: #000;
}

.error-wrapper .info {

}

.home-btn,
.home-btn:focus,
.home-btn:hover,
.home-btn:visited {
    text-decoration: none;
    color: #a03728;;
    padding: 17px 77px;
    border: 1px solid #a03728;;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    -o-border-radius: 3px;
    border-radius: 3px;
    display: block;
    margin: 20px 0;
    width: max-content;
    background-color: transparent;
    outline: 0;
}

.man-icon {
    background: url('images/error.png') center center no-repeat;
    display: inline-block;
    height: 200px;
    width: 200px;
    margin-bottom: 16px;
}
#button{
	color:#a03728;
  border-color: #a03728;
  background-color: transparent;
  }
  </style>
</head>
<body>
	<div class="container">
		<div class="error-wrapper">
			<div class="man-icon"></div>
			<h3 class="display-4">Whoops!</h3>
			<p class="h3 mb-3 font-weight-normal">You already added this meal. Redirecting....</p>
		</div>
	</div>
  <script src="jquery/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
	header ("refresh: 2; url=home.php");
	exit;
?>
