<?php
	session_start();
	error_reporting(E_ERROR | E_PARSE);
	if($_SESSION['loggedIn'] == true)
	{
		header('Location: main.php');
	}
?>
<html>
	<head>
		<title>IT Carlow Clubs and Societies Account Registration Page</title>
	</head>
	<body style="background-color:gray">
	<h1>Registration</h1>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<label for="userName">Username:</label>
			<input type="text" id="userName" name="userName" pattern="[A-Za-z0-9]{5,50}" title="Must be letters or numbers and at least 5 characters" required><br>
			<label for="passWord">Password:</label>
			<input type="password" id="passWord" name="passWord" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,50}" title="Must have one number, one uppercase, one lowercase letter and be at least 6 characters long" required><br>
			<input type="submit" value="Submit">
		</form>
	</body>
</html> 
<?php
	include 'dbConnect.php';
	function filterInput($input) 
	{
	  return htmlspecialchars(stripslashes(trim($input)));
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") 
	{
	  $userName = filterInput($_POST["userName"]);
	  $passWord = $_POST["passWord"];
	  $error = "";
	  if(empty($userName))
	  {
		  $error .= "Invalid Username ";
	  }
	  else if(empty($passWord))
	  {
		  $error .= "Invalid Password ";
	  }
	  else if(empty($error))
	  {
		  $state = $con->prepare("SELECT * FROM users where userName = ?");
		  $state->bind_param("s",$userName);
		  $state->execute();
		  if($state->fetch())
		  {
			  $error .= "User already exists ";
		  }
		  else
		  {
			  $passWord = password_hash($passWord, PASSWORD_DEFAULT);
			  $state = $con->prepare("INSERT INTO users (userName,pass) VALUES (?,?)");
			  $state->bind_param("ss",$userName,$passWord);
			  $state->execute();
			  echo "User '" . $userName . "' Created";
			  header('Location: login.php');
		  }
	  }
	  else
	  {
		  $error .= "Error ";
	  }
	  echo $error;
	  $state->close();
	  $con->close();
	}
	echo '<br><a href="main.php"><button type=button>Return to Main Page</button></a>';
?>