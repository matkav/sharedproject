<?php
	session_start();
	error_reporting(E_ERROR | E_PARSE);
	include 'dbConnect.php';
	$err = "";
	if($_SESSION['loggedIn'] == false)
	{
		header('Location: main.php');
	}
	if($_SESSION['filledInfo'])
	{
		if($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$clubJoin = $_POST["selectClub"];
			if(!($clubJoin == "sample"))
			{
				$state = $con->prepare("INSERT INTO " . $clubJoin . " (userID) VALUES (?)");
				$state->bind_param("s",$_SESSION['userID']);
				$state->execute();
			}
		}
		$clubsList['Boxing'] = 0;
		$clubsList['Soccer'] = 0;
		$clubsList['Rugby'] = 0;
		$firstClub = true;
		foreach($clubsList as $curClub=>$memID)
		{
			$state = $con->prepare("SELECT memberID FROM " . $curClub . " WHERE userID = ?");
			$state->bind_param("s",$_SESSION['userID']);
			$state->execute();
			$result = $state->get_result();
			$member = $result->fetch_assoc();
			if(mysqli_num_rows($result) > 0)
			{
				$clubsList[$curClub] = $member['memberID'];
				if($firstClub)
				{
					echo "You are a member of the following clubs and societies: " . $curClub;
					$firstClub = false;
				}
				else
				{
					echo ", " . $curClub;
				}
			}
			else
			{
				$clubsList[$curClub] = 0;
			}
		}
		if($firstClub)
		{
			echo "You are not a member of any clubs or societies";
		}
		echo "<br>Join a club by select one from the dropdown menu and pressing Join: ";
		
		echo "<br><form action='" . htmlspecialchars($_SERVER['PHP_SELF']). "' method='post'><select name ='selectClub'><option value='sample'>Pick a Club</option>";
		foreach($clubsList as $curClub=>$memID)
		{
			echo $curClub . "<br>";
			if($memID<=0)
			{
				echo "<option value ='" . $curClub . "' >" . $curClub . "</option>";
			}
		}
		echo "<br></select><input type='submit' value = 'Join'></form>";
	}
	else
	{
		$err .= "You have to fill in your personal info before you can join a club/society<br><a href='addInfo.php'><button type=button>Add/Modify Personal Info</button></a>";
	}
	echo $err;
	echo '<br><a href="main.php"><button type=button>Return to Main Page</button></a>';
	$state->close();
	$con->close();
?>
<html>
	<head>
		<title>Join a club/society</title>
	</head>
	<body style="background-color:gray">
	</body>
</html>