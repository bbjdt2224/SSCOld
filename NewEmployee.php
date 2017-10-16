<?php
	include("config.php");
	session_start();
	$msg = "";
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		if($_POST["password"] == $_POST["cpassword"]){
				$username = "SELECT username FROM admin WHERE username='".$_POST["username"]."'";
				$query = mysqli_query($db, $username);
				$numRows = mysqli_num_rows($query);
				if($numRows == 0){
					$new = "CREATE TABLE ".$_POST["username"]." SELECT * FROM blank";
					mysqli_query($db, $new);
					$update = "UPDATE admin SET name = '".$_POST["name"]."' WHERE username='".$_POST["username"]."'";
					mysqli_query($db, $update);
					$num = "SELECT * FROM admin";
					$result = mysqli_query($db, $num);
					$rows = mysqli_num_rows($result);
					$insert = "INSERT INTO `admin` (`id`, `username`, `password`, `admin`, `Name`, `security`, `submitted`) VALUES (".($rows+1).", '".$_POST["username"]."', '".hash("md2",$_POST["password"])."', '0', '".$_POST["name"]."', '".$_POST["security"]."', '0')";
					mysqli_query($db, $insert);
					$updatefacc = "update ".$_POST['username']." set other='".$_POST['facc']."' where Row='2'";
					mysqli_query($db, $updatefacc);
					$updatecode = "update ".$_POST["username"]." set other='".$_POST['code']."' where Row='3'";
					mysqli_query($db, $updatecode);
					for($i = 1; $i < 8; $i ++){
						$updatehours = "update ".$_POST['username']." set previous = '".$_POST[$i]."' where Row = ".$i;
						mysqli_query($db, $updatehours);
					}
					for($i = 1; $i < 8; $i ++){
						$updatehours = "update ".$_POST['username']." set previous = '".$_POST[$i]."' where Row = ".($i+7);
						mysqli_query($db, $updatehours);
					}
					$_SESSION["login_user"] = $_POST["username"];
					$_SESSION['admin'] = 0;
					header("location: EditTimesheet.php");
				}
				else{
					$msg = "This username is already taken";
				}
			}
			else{
				$msg = "Password and Confirm Password do not match";
			}
	}
?>

<html>
	<head>
		<title>New Employee</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="page-header">
				<h1 style="color: red;"><?php echo $msg; ?></h1>
				<h1>New Employee</h1>
			</div>
			<form method="post">
				<div class="form-group">
					<label for="name">First and Last Name</label>
					<input type="text" class="form-control" name="name" id="name" required></input>
				</div>
				<div class="form-group">
					<label for="username">Username(Bronco Net ID)</label>
					<input type="text" class="form-control" name="username" id="username" required></input>
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control" name="password" id="password" required></input>
				</div>
				<div class="form-group">
					<label for="cpassword">Confirm Password</label>
					<input type="password" class="form-control" name="cpassword" id="cpassword" required></input>
				</div>
				<div class="form-group">
					<label for="security">Security Word</label>
					<input type="text" class="form-control" name="security" id="security" required></input>
				</div>
				<div class="form-group">
					<label for="facc">Fund & Cost Center</label>
					<input type="text" class="form-control" name="facc" id="facc" required></input>
				</div>
				<div class="form-group">
					<label for="code">Job Code</label>
					<input type="text" class="form-control" name="code" id="code" required></input>
				</div>
				
				<h1>Hours</h1>
				
				<table class="table">
					<thead>
						<th>Sunday</th>
						<th>Monday</th>
						<th>Tuesday</th>
						<th>Wednesday</th>
						<th>Thursday</th>
						<th>Friday</th>
						<th>Saturday</th>
					</thead>
					<tbody>
						<tr>
							<td>
								<div class="form-group">
									<input type="number" class="form-control" name="1" value="0"></input>
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="number" class="form-control" name="2" value="0"></input>
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="number" class="form-control" name="3" value="0"></input>
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="number" class="form-control" name="4" value="0"></input>
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="number" class="form-control" name="5" value="0"></input>
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="number" class="form-control" name="6" value="0"></input>
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="number" class="form-control" name="7" value="0"></input>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				
				<button type="submit" class="btn btn-success">Submit</button>
				
			</form>
		</div>
	</body>
</html>