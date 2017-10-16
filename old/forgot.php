<?php
	include("config.php");
	$msg = "";
	if(isset($_POST["username"]) && isset($_POST["security"]) && isset($_POST["npassword"])){
		if($_POST["username"] != null || $_POST["security"] != null || $_POST["npassword"] != null){
			$sel = "SELECT username, security FROM admin WHERE username='".$_POST["username"]."'";
			$result = mysqli_query($db,$sel);
			$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
			$count = mysqli_num_rows($result);
			if($count == 1 && $row["security"] == $_POST["security"]){
				$up = "UPDATE admin SET password='".hash("md2", $_POST["npassword"])."'";
				$db->query($up);
				$_SESSION["login_user"] = $_POST["username"];
				header("location: welcome.php");
			}
			else{
				$msg = "Information is incorrect";
			}
		}
		else{
			$msg = "Fill all boxes";
		}
	}
?>

<html>
	<head>
		<title>Change Password</title>
	</head>
	<body>
		<form action="" method="post">
			<table>
				<tr><td>Bronco Net ID: </td><td><input type='text' name='username'></td></tr>
				<tr><td>Security Code: </td><td><input type='text' name='security'></td></tr>
				<tr><td>New Password: </td><td><input type='text' name='npassword'></td></tr>
				<tr><td colspan='2'><button type='submit' name='submit'>Submit</button></td></tr>
			</table>
		</form>
		<h2><?php echo $msg; ?></h2>
	</body>
</html>