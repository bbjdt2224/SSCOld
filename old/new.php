<?php
	include("config.php");
	$msg = "";
	if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["name"]) && isset($_POST["cpassword"]) && isset($_POST["security"]) && isset($_POST["facc"]) && isset($_POST["code"])){
		if($_POST["username"] != null || $_POST["password"] != null || $_POST["name"] != null || $_POST["cpassword"] != null || $_POST["security"] != null || $_POST["facc"] != null || $_POST["code"] != null){
			if($_POST["password"] == $_POST["cpassword"]){
				$un = "SELECT username FROM admin WHERE username='".$_POST["username"]."'";
				$r = $db->query($un);
				$ro = mysqli_num_rows($r);
				if($ro == 0){
					$new = "CREATE TABLE ".$_POST["username"]." SELECT * FROM blank";
					$db->query($new);
					$update = "UPDATE admin SET name = '".$_POST["name"]."' WHERE username='".$_POST["username"]."'";
					$db->query($update);
					$num = "SELECT * FROM admin";
					$result = $db->query($num);
					$rows = mysqli_num_rows($result);
					$insert = "INSERT INTO `admin` (`id`, `username`, `password`, `security`, `submited`, `name`) VALUES (".($rows+1).", '".$_POST["username"]."', '".hash("md2",$_POST["password"])."', '".$_POST["security"]."', 'no', '".$_POST["name"]."')";
					$db->query($insert);
					$updatefacc = "update ".$_POST['username']." set other='".$_POST['facc']."' where Row='2'";
					mysqli_query($db, $updatefacc);
					$updatecode = "update ".$_POST["username"]." set other='".$_POST['code']."' where Row='3'";
					mysqli_query($db, $updatecode);
					$_SESSION["login_user"] = $_POST["username"];
					header("location: welcome.php");
				}
				else{
					$msg = "This username is already taken";
				}
			}
			else{
				$msg = "Password and Confirm Password do not match";
			}
		}
		else{
			$msg = "Fill all areas";
		}
	}
	
?>
<html>
	<head>
		<title>New Employee</title>
	</head>
	<body>
		<form action="" method="post">
			<table>
				<tr><td>Name: </td><td><input type="text" name="name"/></td></tr>
				<tr><td>Bronco Net ID: </td><td><input type="text" name="username"/></td></tr>
				<tr><td>Security Word: </td><td><input type="text" name="security"/></td></tr>
				<tr><td>Fund and Cost Center: </td><td><input type="text" name="facc"/></td></tr>
				<tr><td>Job Code: </td><td><input type="text" name="code"/></td></tr>
				<tr><td>Password: </td><td><input type="password" name="password"/></td></tr>
				<tr><td>Confirm Password: </td><td><input type="password" name="cpassword"/></td></tr>
				<tr><td clospan='2'><button type="submit" name="submit">Submit</button></td></tr>
			</table>
		</form>
		<h4><?php echo $msg;?></h4>
	</body>
<html>