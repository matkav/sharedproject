<?php
	session_start();
	include 'dbConnect.php';
	//error_reporting(E_ERROR | E_PARSE);
	if($_SESSION['loggedIn'] == false)
	{
		header('Location: main.php');
	}
	else if($_SESSION['filledInfo'])
	{
		if($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$state = $con->prepare("UPDATE users SET hasInfo=0,studentID=null,phone=null,email=null,dob=null,medCondition=null,docName=null,docPhone=null,nokName=null,nokPhone=null,photoName=null WHERE userName=?");
			$state->bind_param("s",$_SESSION['name']);
			$state->execute();
			$_SESSION['filledInfo'] = false;
			header('Location: main.php');
		}
		echo" 
			<h1>ARE YOU SURE YOU WANT TO DELETE YOUR PERSONAL INFO?</h1><br>
			<form action='". htmlspecialchars($_SERVER['PHP_SELF']). "' method='post'>
				<input type='submit' value='Yes'>
			</form>
			<a href='main.php'><button type=button>No, return to Main Page</button></a>";
	}
	else
	{
		echo "You have no personal info saved";
	}
?>
<html>
	<head>
		<title>Delete Personal Info</title>
	</head>
	<body style="background-color:gray">
	</body>
</html>