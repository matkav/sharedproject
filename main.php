<?php
	session_start();
	error_reporting(E_ERROR | E_PARSE);
	if($_SESSION['loggedIn'] == true)
	{
		echo 'You are logged in as ' . $_SESSION['name'] . '<br>';
		echo '<a href="logOut.php"><button type=button>Logout</button></a><br>';
		echo '<a href="joinClub.php"><button type=button>Join/View a club</button></a>';
		echo '<a href="addInfo.php"><button type=button>Add/Modify Personal Info</button></a>';
		echo '<a href="deleteInfo.php"><button type=button>Delete Personal Info</button></a>';
	}
	else
	{
		echo 'You are not logged in<br>';
		echo '<a href="login.php"><button type=button>Login</button></a><br>';
		echo '<a href="accReg.php"><button type=button>Register</button></a>';
	}
?>
<html>
	<head>
		<title>IT Carlow Clubs & Societies Main Page</title>
	</head>
	<body style="background-color:gray">
	</body>
</html>