<?php
	$server = 'localhost';
	$user = 'root';
	$pass = 'Hl6@.[!SGYLAF8RY';
	$db = 'clubs';
	
	$con = new mysqli($server,$user,$pass,$db);
	if($con->connect_error)
	{
		die("Database connection failed");
	}
?>