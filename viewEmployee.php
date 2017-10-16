<?php
	include("session.php");
	
	$username = "";
	if(isset($_POST["username"])){
		$username = $_POST["username"];
	}
	else{
		header("location: admin.php");
	}
	
	$getInfo = "select * from admin where username = '".$username."'";
	$infoQuery = mysqli_query($db, $getInfo);
	$info = mysqli_fetch_array($infoQuery);
	
	$getTimesheet = "select * from ". $username;
	$timesheetQuery = mysqli_query($db, $getTimesheet);
	
	$getFund = "select other from `". $username. "` where Row=2";
	$fundquery = mysqli_query($db, $getFund);
	$fund = mysqli_fetch_array($fundquery);
	
	$getCode = "select other from `". $username . "` where Row=3";
	$codequery = mysqli_query($db, $getCode);
	$code = mysqli_fetch_array($codequery);
	
	$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	
?>
<html>
	<head>
		<title>View Employee Timesheet</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<style>
			th{
				font-size: 8pt;
			}
			td{
				font-size: 6pt;
			}
		</style>
	</head>
	<body>
		<div class="container-fluid">
			<?php
				echo "<h3>".$info['Name']."  |  Fund & Cost Center: ".$fund["other"]."  |  Job Code: ".$code["other"]."</h3>";
				echo "<table class='table'><thead><th>Day</th><th>Date</th><th>Morning Begin</th><th>Morning End</th><th>Afternoon Begin</th><th>Afternoon End</th><th>Evening Begin</th><th>Evening End</th><th>Reason for Absence</th><th>Total</th></thead><tbody>";
				
				$w1total = 0;
				$w2total = 0;
				$total = 0;
				$startdate = "";
				$oneday = 86400;
				
				for($i = 0; $i < 14; $i ++){
					$timesheet = mysqli_fetch_array($timesheetQuery);
					if($i == 0){
						$startdate = strtotime($timesheet['other']);
					}
					echo "<tr><td>".$days[$i]."</td><td>".date("m/d/y", ($startdate+($oneday*$i)))."</td><td>".$timesheet["morningbegin"]."</td><td>".$timesheet["morningend"]."</td><td>".$timesheet["afternoonbegin"]."</td><td>".$timesheet["afternoonend"]."</td><td>".$timesheet["eveningbegin"]."</td><td>".$timesheet["eveningend"]."</td><td>".$timesheet["reason"]."</td><td>".$timesheet["Total"]."</td></tr>";
					if($i < 7){
						$w1total += $timesheet["Total"];
					}else{
						$w2total += $timesheet["Total"];
					}
					if($i == 6){
						echo "<tr><td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>Week 1:</td><td>".$w1total."</td></tr>";
					}
				}
				echo "<tr><td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>Week 2:</td><td>".$w2total."</td></tr>";
				echo "<tr><td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>Total:</td><td>".($w1total+$w2total)."</td></tr>";
				
				echo "</tbody></table>";
			?>
			<div class="row">
				<div class="col-sm-6">
					<?php
						echo "<p style='font-size: 6pt;'>Signature:</p><img src='uploads/".$username.".jpg' style='width: 10%'>";
					?>
				</div>
				<div class="col-sm-6" style="border: 1px solid black">
					<h6 style="font-size: 4pt;">APPROVAL BY: DEPARTMENT MANAGER OR SUPERVISOR</h6>
				</div>
			</div>
		</div>
	</body>
</html>