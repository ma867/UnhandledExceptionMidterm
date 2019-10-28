<?php
    $logindb = new mysqli("192.168.2.4","testUser","12345","testdb");
	if(mysqli_connect_errno())
	{
		echo "failed to connect to MYSQL:" . mysqli_connect_error();
		exit();
	}
	print "connected";

?>
