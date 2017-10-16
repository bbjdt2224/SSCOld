<?php
   include("config.php");
   session_start();
   	   $error = "";
   if($_SERVER["REQUEST_METHOD"] == "POST"){
	     
	   $myusername = mysqli_real_escape_string($db,$_POST["username"]);
	   $mypassword = hash("md2",mysqli_real_escape_string($db, $_POST["password"]));
	   $sql = "select id from admin where username = '$myusername' and password = '$mypassword'";
	   $result = mysqli_query($db,$sql);
	   $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	   
	   $count = mysqli_num_rows($result);
	   
	   
	   setcookie("loginname", $_POST['username'], time() + (86400 * 120), "/");
	   
	   if($count == 1){
			$_SESSION["login_user"] = $myusername;
			if($myusername != "admin"){
				$_SESSION['admin'] = 0;
				header("location: EditTimesheet.php");
			}
			elseif($myusername == "admin"){
				$_SESSION['admin'] = 1;
				header("location: admin.php");
			}
			
	   }
	   else{
		   $error = "Your pin was incorrect";
	   }
   }
?>
<html>
	<head>
		<title>Login</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class='container'>
			<br>
			<h1>Login</h1>
			<form method="post">
				<div class="form-group">
					<label for="username">Username:</label>
					<input type="username" class="form-control" name="username" id="username"></input>
				</div>
				<div class="form-group">
					<label for="password">Password:</label>
					<input type="password" class="form-control" name="password" id="password"></input>
				</div>
				<button type="submit" class="btn btn-success">Submit</button>
				<br>
				<br>
				<a href="NewEmployee.php" class="btn btn-info">New Employee</a>
				<a href="Forgot.php" class="btn btn-warning">Forget Password</a>
			</form>
		</div>
	</body>
</html>