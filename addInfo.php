<?php
	session_start();
	error_reporting(E_ERROR | E_PARSE);
	function filterInput($input) 
	{
	  return htmlspecialchars(stripslashes(trim($input)));
	}
	if($_SESSION['loggedIn'] == false)
	{
		header('Location: main.php');
	}
	else
	{
		
		include 'dbConnect.php';
		$err = "";
		$key = "A[ST0WY.24me5nEg";
		if($_SERVER["REQUEST_METHOD"] == "POST") 
		{
			$id = filterInput($_POST["studentID"]);
			$phone = filterInput($_POST["phoneNum"]);
			$email = filterInput($_POST["email"]);
			$dob = $_POST["dob"];
			$medCondition = $_POST["medCon"];
			$docName = filterInput($_POST["docName"]);
			$docPhone = filterInput($_POST["docPhone"]);
			$nokName = filterInput($_POST["nokName"]);
			$nokPhone = filterInput($_POST["nokPhone"]);
			$fileExtension = "";
			$alreadyPhoto = false;
			
			if(empty($id))
			{
				$err .= "Invalid Student ID ";
			}
			if(empty($phone))
			{
				$err .= "Invalid Phone Number ";
			}
			if(empty($email))
			{
				$err .= "Invalid Email ";
			}
			if(empty($dob))
			{
				$err .= "Invalid Date of Birth ";
			}
			if(empty($docName))
			{
				$err .= "Invalid Doctor Name ";
			}
			if(empty($docPhone))
			{
				$err .= "Invalid Doctor Phone ";
			}
			if(empty($nokName))
			{
				$err .= "Invalid Next of Kin name ";
			}
			if(empty($nokPhone))
			{
				$err .= "Invalid Next of Kin phone";
			}
			if(is_uploaded_file($_FILES['photoUp']['tmp_name']))
			{
				$fileName = $_FILES['photoUp']['name'];
				$fileExtension = strrchr($fileName,".");
				if(empty($fileName))
				{
					$err .= "Empty Photo File Name ";
				}
				else if(strlen($fileName) > 255)
				{
					$err .= "Photo File Name is too long ";
				}
				else if($_FILES['photoUp']['size'] > 10000000)
				{
					$err .= "Photo can't be bigger than 10MB";
				}
				else if(empty($fileExtension))
				{
					$err .= "No file extension for photo ";
				}
				else if(!($fileExtension == ".png") && !($fileExtension == ".jpeg") && !($fileExtension == ".jpg") && !($fileExtension == ".gif") && !($fileExtension == ".webp"))
				{
					$err .= "Photo file extension must .png,.jpeg,.jpg,.gif, or .webp";
				}
			}
			else
			{
				$state = $con->prepare("SELECT photoName FROM users WHERE userName = ?");
				$state->bind_param("s",$_SESSION['name']);
				$state->execute();
				$state->bind_result($result);
				if($state->fetch())
				{
					if(empty($result))
					{
						$err .= "Error uploading photo ";
					}
					else
					{
						$alreadyPhoto = true;
					}
					while($state->fetch());
				}
				else
				{
					$err .= "Error uploading photo ";
				}
			}
			
			if(empty($err))
			{
				$time = time();
				$photoName = $time . $fileExtension;
				$state = $con->prepare("UPDATE users SET hasInfo=1,studentID=(AES_ENCRYPT(?,'$key')),phone=(AES_ENCRYPT(?,'$key')),email=(AES_ENCRYPT(?,'$key')),dob=(AES_ENCRYPT(?,'$key')),medCondition=(AES_ENCRYPT(?,'$key')),docName=(AES_ENCRYPT(?,'$key')),docPhone=(AES_ENCRYPT(?,'$key')),nokName=(AES_ENCRYPT(?,'$key')),nokPhone=(AES_ENCRYPT(?,'$key')) WHERE userName=?");
				$state->bind_param("ssssssssss",$id,$phone,$email,$dob,$medCondition,$docName,$docPhone,$nokName,$nokPhone,$_SESSION['name']);
				$state->execute();
				if(!$alreadyPhoto && move_uploaded_file($_FILES['photoUp']['tmp_name'], "photos/" . $photoName))
				{
					$state = $con->prepare("UPDATE users SET photoName=? WHERE userName=?");
					$state->bind_param("ss",$photoName,$_SESSION['name']);
					$state->execute();
				}
				else if(!$alreadyPhoto)
				{
					$err .= "Error moving photo ";
				}
				$_SESSION['filledInfo'] = true;
			}
			
		}
		$state = $con->prepare("SELECT hasInfo FROM users WHERE userName = ?");
		$state->bind_param("s",$_SESSION['name']);
		$state->execute();
		$state->bind_result($result);
		if($state->fetch())
		{
			if($result > 0)
			{
				while($state->fetch());
				$state = $con->prepare("SELECT aes_decrypt(studentID,'$key'),aes_decrypt(phone,'$key'),aes_decrypt(email,'$key'),aes_decrypt(dob,'$key'),aes_decrypt(medCondition,'$key'),aes_decrypt(docName,'$key'),aes_decrypt(docPhone,'$key'),aes_decrypt(nokName,'$key'),aes_decrypt(nokPhone,'$key'),photoName FROM users WHERE userName = ?");
				$state->bind_param("s",$_SESSION['name']);
				$state->execute();
				$state->bind_result($id,$phone,$email,$dob,$medCondition,$docName,$docPhone,$nokName,$nokPhone,$photo);
				if($state->fetch())
				{
					echo"
					<form action='" . htmlspecialchars($_SERVER['PHP_SELF']). "' method='post' enctype='multipart/form-data'>
						<label for='studentID'>Student ID:</label>
						<input type='text' id='studentID' name='studentID' pattern='[A-Za-z0-9]{5,}' title='Must be letters or numbers and at least 5 characters' value ='" . $id . "' required><br>
						<label for='phoneNum'>Phone Number:</label>
						<input type='text' id='phoneNum' name='phoneNum' pattern='[0-9]{9,}' title='Must be only numbers and at least 9 characters' value ='" . $phone . "' required><br>
						<label for='email'>Email:</label>
						<input type='email' id='email' name='email' pattern='.{,256}' value ='" . $email . "' required><br>
						<label for='dob'>Date of Birth:</label>
						<input type='date' id='dob' name='dob' value ='" . $dob . "' required><br>
						<label for='medCon'>Medical Conditions:</label>
						<input type='text' id='medCon' name='medCon' pattern=.{0,255} value ='" . $medCondition . "'><br>
						<label for='docName'>Doctor Name:</label>
						<input type='text' id='docName' name='docName' pattern=[A-Za-z\s]{5,256} value ='" . $docName . "' required><br>
						<label for='docPhone'>Doctor Phone Number:</label>
						<input type='text' id='docPhone' name='docPhone' pattern=[0-9]{9,256} value ='" . $docPhone . "' required><br>
						<label for='nokName'>Next of Kin Name:</label>
						<input type='text' id='nokName' name='nokName' pattern=[A-Za-z\s]{5,256} value ='" . $nokName . "' required><br>
						<label for='nokPhone'>Next of Kin Phone Number:</label>
						<input type='text' id='nokPhone' name='nokPhone' pattern=[0-9]{9,256} value ='" . $nokPhone . "' required><br>
						<label for='photo'>Photo:</label>
						<input type='file' id='photoUp' name='photoUp'><br>
						Current photo:<br>
						<img src='photos/". $photo . "' width='250' height='250'><br>
						<input type='submit' value='Submit'>
					</form>";
				}
				else
				{
					$err .= "Error retriving your information from database ";
				}
			}
			else
			{
				echo"
				<form action='" . htmlspecialchars($_SERVER['PHP_SELF']). "' method='post' enctype='multipart/form-data'>
					<label for='studentID'>Student ID:</label>
					<input type='text' id='studentID' name='studentID' pattern='[A-Za-z0-9]{5,}' title='Must be letters or numbers and at least 5 characters' required><br>
					<label for='phoneNum'>Phone Number:</label>
					<input type='text' id='phoneNum' name='phoneNum' pattern='[0-9]{9,}' title='Must be only numbers and at least 9 characters' required><br>
					<label for='email'>Email:</label>
					<input type='email' id='email' name='email' pattern='.{,256}' required><br>
					<label for='dob'>Date of Birth:</label>
					<input type='date' id='dob' name='dob'><br>
					<label for='medCon'>Medical Conditions:</label>
					<input type='text' id='medCon' name='medCon' pattern=.{0,256}><br>
					<label for='docName'>Doctor Name:</label>
					<input type='text' id='docName' name='docName' pattern=[A-Za-z\s]{5,256} required><br>
					<label for='docPhone'>Doctor Phone Number:</label>
					<input type='text' id='docPhone' name='docPhone' pattern=[0-9]{9,256} required><br>
					<label for='nokName'>Next of Kin Name:</label>
					<input type='text' id='nokName' name='nokName' pattern=[A-Za-z\s]{5,256} required><br>
					<label for='nokPhone'>Next of Kin Phone Number:</label>
					<input type='text' id='nokPhone' name='nokPhone' pattern=[0-9]{9,256} required><br>
					<label for='photo'>Photo:</label>
					<input type='file' id='photoUp' name='photoUp' required><br>
					<input type='submit' value='Submit'>
				</form>";
			}
		}
		else
		{
			$err .= "Error getting info from database ";
		}
		echo $err;
		echo '<br><a href="main.php"><button type=button>Return to Main Page</button></a>';
		$state->close();
		$con->close();
	}
?>
<html>
	<head>
		<title>Add/modify Personal Info</title>
	</head>
	<body style="background-color:gray">
	</body>
</html>