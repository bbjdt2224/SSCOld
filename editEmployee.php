<?php
	include("session.php");
	
	$username = "";
	if(isset($_POST["username"])){
		$username = $_POST["username"];
	}
	
	$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	
	$getPrevious = "select previous from ". $username;
	$previousQuery = mysqli_query($db, $getPrevious);
	
	if(isset($_POST["Monday"])){
		for($i = 0; $i < 7; $i ++){
			$updatePrevious = "update ". $username ." set previous = ". $_POST[$days[$i]]. " where row = ". ($i+1);
			mysqli_query($db, $updatePrevious);
		}
		for($i = 0; $i < 7; $i ++){
			$updatePrevious = "update ". $username ." set previous = ". $_POST[$days[$i]]. " where row = ". ($i+8);
			mysqli_query($db, $updatePrevious);
			header("location: admin.php");
		}
	}
?>

<html>
	<head>
		<title>Edit Employee Hours</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<form method="post">
				<table class="table">
					<thead>
						<th>Monday</th>
						<th>Tuesday</th>
						<th>Wednesday</th>
						<th>Thursday</th>
						<th>Friday</th>
						<th>Saturday</th>
						<th>Sunday</th>
					</thead>
					<tbody>
						<tr>
							<?php
								for($i = 0; $i < 7; $i ++){
									$row = mysqli_fetch_array($previousQuery);
									echo "<td><input class='form-control' type='number' name='".$days[$i]."' value='".$row['previous']."'></td>";
								}
								echo "<input type='hidden' value='".$username."' name='username'>";
							?>
						</tr>
					</tbody>
				</table>
				<button type="sumbit" class="btn btn-success">Submit</button>
			</form>
		</div>
	</body>
</html>