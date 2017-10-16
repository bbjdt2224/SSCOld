<?php
	include("config.php");
	$msg = "";
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$sel = "SELECT username, security FROM admin WHERE username='".$_POST["username"]."'";
			$result = mysqli_query($db,$sel);
			$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
			$count = mysqli_num_rows($result);
			if($count == 1 && $row["security"] == $_POST["security"]){
				$up = "UPDATE admin SET password='".hash("md2", $_POST["npassword"])."'";
				$db->query($up);
				$_SESSION["login_user"] = $_POST["username"];
				header("location: EditTimesheet.php");
			}
			else{
				$msg = "Information is incorrect";
			}
	}
?>

<html>
	<head>
		<title>Forgot Password</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="page-header">
				<h1>Forgot Password</h1>
			</div>
			<form method="post">
				<div class="form-group">
					<label for="username">Username</label>
					<input type="username" class="form-control" name="username" id="username" required></input>
				</div>
				<div class="form-group">
					<label for="security">Security Word</label>
					<input type="text" class="form-control" name="security" id="security" required></input>
				</div>
				<div class="form-group">
					<label for="npassword">New Password</label>
					<input type="password" class="form-control" name="npassword" id="npassword" required></input>
				</div>
				<button type="submit" class="btn btn-success">Submit</button>
			</form>
		</div>
	</body>
</html>