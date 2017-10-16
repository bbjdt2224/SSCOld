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
	   
	   if($count == 1){
			$_SESSION["login_user"] = $myusername;
			
			header("location: welcome.php");
	   }
	   else{
		   $error = "You Username or Password was incorrect";
	   }
   }
?>
<html>
	<head>
		<title>Login</title>
		<link rel="stylesheet" type="text/css" href="loginStyle.css"/>
	</head>
	<body>
		<h1>Enter Username and Password</h1>

		
		<form action="" method="post">
			<label>Username: </label><input type="text" name="username"/>
			</br>
			<label>Password: </label><input type="password" name="password"/>
			<button type="submit" name="login">Login</button>
			<h4><?php echo $error; ?></h4>
		</form>
		<a href="new.php">New Employee</a>
		</br>
		<a href="forgot.php">Forgot Password</a>
	</body>

</html>