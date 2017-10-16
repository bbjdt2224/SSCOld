<?php
	include("session.php");
	
	$morningbegin = array();
	$morningend = array();
	$afternoonbegin = array();
	$afternoonend = array();
	$eveningbegin = array();
	$eveningend = array();
	$reason = array();
	$total = array();
	$startdate = "";
	
	$getinfo = "select * from ".$_SESSION["login_user"];
	$infoquery = mysqli_query($db, $getinfo);
	for($i = 0 ; $i < 15; $i ++){
		$row = mysqli_fetch_array($infoquery);
		$morningbegin[] = $row["morningbegin"];
		$morningend[] = $row["morningend"];
		$afternoonbegin[] = $row["afternoonbegin"];
		$afternoonend[] = $row["afternoonend"];
		$eveningbegin[] = $row["eveningbegin"];
		$eveningend[] = $row["eveningend"];
		$reason[] = $row["reason"];
		$total[] = $row["Total"];
		if($i == 0){
			$startdate = $row["other"];
		}
	}
	
	$startdate = strtotime($startdate);
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$target_dir = "uploads/";
		$target_file = $target_dir . $_SESSION["login_user"].".jpg";//basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"]) || isset($_POST['save'])) {
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				echo "<script>alert('File is an image - " . $check["mime"] . ".')</script>";
				$uploadOk = 1;
			} else {
				echo "<script>alert('File is not an image.')</script>";
				$uploadOk = 0;
			}
		}
		if(isset($_POST['submit'])){
			$setSubmit = "update admin set submitted = 1 where username = '".$_SESSION['login_user']."'";
			mysqli_query($db, $setSubmit);
		}
		// Check if file already exists
		if (file_exists($target_file)) {
			echo "<script>alert('Sorry, file already exists.')</script>";
			$uploadOk = 0;
		}
		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 10000000) {
			echo "<script>alert('Sorry, your file is too large.')</script>";
			$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<script>alert('Sorry, your file was not uploaded.')</script>";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				echo "<script>alert('The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.)</script>";
				header("location: logout.php");
			} else {
				echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
			}
		}
		
	}
?>

<html>
	<head>
		<title>Timesheet</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<style>
			.time{
				width: 100%;
			}
		</style>
	</head>
	<body>
		<div class="container-fluid">
			<div class="page-header">
				<h1>Western Michigan University</h1>
				<h2>STEP Program</h2>
				<h4>The employee must account for all of the time she/he is scheduled to work during each pay period on this Time Report. Do not include unpaid lunch periods.</h4>
				<form class="form-inline">
					<input type="date" id="date"></input>
					<button class="btn btn-success" type="button" onclick="changeDate(); window.location.reload()">Change</button>
				</form>
			</div>
			<table class="table">
				<thead>
					<th>Day</th>
					<th>Date</th>
					<th>Morning Begin</th>
					<th>Morning End</th>
					<th>Afternoon Begin</th>
					<th>Afternoon End</th>
					<th>Evening Begin</th>
					<th>Evening End</th>
					<th>Reason for Absence</th>
					<th>Total</th>
				</thead>
				<tbody>
					<?php
						//the number of seconds in a day
						$oneday = 86400;
						//array containing days of the week
						$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
						//loop creating the first seven days in the table
						for($i = 0; $i < 7; $i++){
							echo "<tr><td>";
							echo $days[$i]."</td>";
							//date
							echo "<td>". date("m/d/y", ($startdate+($oneday*$i)))."</td>";
							//morning begin time
							echo "<td><button type='button' class='btn btn-info time'  data-toggle='modal' data-target='#mb". date("mdy", ($startdate+($oneday*$i)))."' id='mb".date("mdy", ($startdate+($oneday*$i)))."t'>".$morningbegin[$i]."</button></td>";
							//edit morning begin window
							morningmodal(date("mdy", ($startdate+($oneday*$i))), "Morning Begin", "mb", date("m/d/y", ($startdate+($oneday*$i))), $i);
							//morning end time
							echo "<td><button type='button' class='btn btn-info time'  data-toggle='modal' data-target='#me". date("mdy", ($startdate+($oneday*$i)))."' id='me".date("mdy", ($startdate+($oneday*$i)))."t'>".$morningend[$i]."</button></td>";
							//edit morning end 
							morningmodal(date("mdy", ($startdate+($oneday*$i))), "Morning End", "me", date("m/d/y", ($startdate+($oneday*$i))), $i);
							// afternoon begin time
							echo "<td><button type='button' class='btn btn-primary time'  data-toggle='modal' data-target='#ab". date("mdy", ($startdate+($oneday*$i)))."' id='ab".date("mdy", ($startdate+($oneday*$i)))."t'>".$afternoonbegin[$i]."</button></td>";
							//edit afternoon begin
							afternoonmodal(date("mdy", ($startdate+($oneday*$i))), "Afternoon Begin", "ab", date("m/d/y", ($startdate+($oneday*$i))), $i);
							//afternoon end time
							echo "<td><button type='button' class='btn btn-primary time'  data-toggle='modal' data-target='#ae". date("mdy", ($startdate+($oneday*$i)))."' id='ae".date("mdy", ($startdate+($oneday*$i)))."t'>".$afternoonend[$i]."</button></td>";
							//edit afternoon end
							afternoonmodal(date("mdy", ($startdate+($oneday*$i))), "Afternoon End", "ae", date("m/d/y", ($startdate+($oneday*$i))), $i);
							//evening begin time
							echo "<td><button type='button' class='btn btn-info time'  data-toggle='modal' data-target='#eb". date("mdy", ($startdate+($oneday*$i)))."' id='eb".date("mdy", ($startdate+($oneday*$i)))."t'>".$eveningbegin[$i]."</button></td>";
							//edit eveing begin
							eveningmodal(date("mdy", ($startdate+($oneday*$i))), "Evening Begin", "eb", date("m/d/y", ($startdate+($oneday*$i))), $i);
							//evening end time
							echo "<td><button type='button' class='btn btn-info time'  data-toggle='modal' data-target='#ee". date("mdy", ($startdate+($oneday*$i)))."' id='ee".date("mdy", ($startdate+($oneday*$i)))."t'>".$eveningend[$i]."</button></td>";
							//edit evenin end
							eveningmodal(date("mdy", ($startdate+($oneday*$i))), "Evening End", "ee", date("m/d/y", ($startdate+($oneday*$i))), $i);
							//reason for absence
							echo "<td><button type='button' class='btn btn-warning time'data-toggle='modal' data-target='#reason". date("mdy", ($startdate+($oneday*$i)))."' id='reason".date("mdy", ($startdate+($oneday*$i)))."t'>".$reason[$i]."</button></td>";
							// edit reason for absence
							reasonmodal(date("mdy", ($startdate+($oneday*$i))), date("m/d/y", ($startdate+($oneday*$i))), $i);
							//total number of hours that day
							echo "<td id='tot".date("mdy", ($startdate+($oneday*$i)))."'>". $total[$i]. "</td>";
							echo "</tr>";
						}
						//total hours that week
						echo "<tr><td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>Week 1:</td> <td id='week1total'>0</td></tr>";
						
						//second set of days
						for($i = 0; $i < 7; $i++){
							echo "<tr><td>";
							echo $days[$i]."</td>";
							echo "<td>". date("m/d/y", ($startdate+($oneday*$i)+($oneday*7)))."</td>";
							//morning begin time
							echo "<td><button type='button' class='btn btn-info time'  data-toggle='modal' data-target='#mb". date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."' id='mb".date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."t'>".$morningbegin[$i+7]."</button></td>";
							//edit morning begin
							morningmodal(date("mdy", ($startdate+($oneday*$i)+($oneday*7))), "Morning Begin", "mb", date("m/d/y", ($startdate+($oneday*$i)+($oneday*7))), $i+7);
							//morning end time
							echo "<td><button type='button' class='btn btn-info time'  data-toggle='modal' data-target='#me". date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."' id='me".date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."t'>".$morningend[$i+7]."</button></td>";
							//edit morning end
							morningmodal(date("mdy", ($startdate+($oneday*$i)+($oneday*7))), "Morning End", "me", date("m/d/y", ($startdate+($oneday*$i)+($oneday*7))), $i+7);
							//afternoon begin time
							echo "<td><button type='button' class='btn btn-primary time'  data-toggle='modal' data-target='#ab". date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."' id='ab".date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."t'>".$afternoonbegin[$i+7]."</button></td>";
							//edit afternoon begin
							afternoonmodal(date("mdy", ($startdate+($oneday*$i)+($oneday*7))), "Afternoon Begin", "ab", date("m/d/y", ($startdate+($oneday*$i)+($oneday*7))), $i+7);
							//afternoon end time
							echo "<td><button type='button' class='btn btn-primary time'  data-toggle='modal' data-target='#ae". date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."' id='ae".date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."t'>".$afternoonend[$i+7]."</button></td>";
							//edit afternoon end
							afternoonmodal(date("mdy", ($startdate+($oneday*$i)+($oneday*7))), "Afternoon End", "ae", date("m/d/y", ($startdate+($oneday*$i)+($oneday*7))), $i+7);
							//evening begin time
							echo "<td><button type='button' class='btn btn-info time'  data-toggle='modal' data-target='#eb". date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."' id='eb".date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."t'>".$eveningbegin[$i+7]."</button></td>";
							//edit evening begin
							eveningmodal(date("mdy", ($startdate+($oneday*$i)+($oneday*7))), "Evening Begin", "eb", date("m/d/y", ($startdate+($oneday*$i)+($oneday*7))), $i+7);
							//evening end time
							echo "<td><button type='button' class='btn btn-info time'  data-toggle='modal' data-target='#ee". date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."' id='ee".date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."t'>".$eveningend[$i+7]."</button></td>";
							//edit evening end
							eveningmodal(date("mdy", ($startdate+($oneday*$i)+($oneday*7))), "Evening End", "ee", date("m/d/y", ($startdate+($oneday*$i)+($oneday*7))), $i+7);
							//reason for absence
							echo "<td><button type='button' class='btn btn-warning time'data-toggle='modal' data-target='#reason". date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."' id='reason".date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."t'>".$reason[$i]."</button></td>";
							//edit reason fro absence
							reasonmodal(date("mdy", ($startdate+($oneday*$i)+($oneday*7))), date("m/d/y", ($startdate+($oneday*$i)+($oneday*7))), $i+7);
							//total hours for that day
							echo "<td id='tot".date("mdy", ($startdate+($oneday*$i)+($oneday*7)))."'>". $total[$i+7]. "</td>";
							echo "</tr>";
						}
						
						echo "<tr><td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>Week 2:</td> <td id='week2total'>0</td></tr>";
						echo "<tr><td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td>Total:</td> <td id='total'>0</td></tr>";
					
						//function that creates the window for editing the morning times
						function morningmodal($date, $time, $abv, $slashdate, $row){
							echo
								'<div id="'.$abv.$date.'" class="modal fade" role="dialog">
								  <div class="modal-dialog">

									<div class="modal-content">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">'.$time.' '.$slashdate.'</h4>
									  </div>
									  <div class="modal-body">
										<select id="'.$abv.$date.'m" class="form-control">
											<option>-</option>
											<option>7:00 AM</option>
											<option>8:00 AM</option>
											<option>9:00 AM</option>
											<option>10:00 AM</option>
											<option>11:00 AM</option>
											<option>12:00 AM</option>
										</select>
										<button class="btn btn-success" onclick="insertTime(\' '.$abv.' \', \' '.$date.' \', \' '.$row.' \'); updateDB(\' '.$row.' \',\' '.$time.' \', \' '.$abv.' \', \' '.$date.' \')" data-dismiss="modal">Enter</button>
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									  </div>
									</div>

								  </div>
								</div>'
								;
						}
						
						//function that creates the window for editing the afternoon times
						function afternoonmodal($date, $time, $abv, $slashdate, $row){
							echo
								'<div id="'.$abv.$date.'" class="modal fade" role="dialog">
								  <div class="modal-dialog">

									<div class="modal-content">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">'.$time.' '.$slashdate.'</h4>
									  </div>
									  <div class="modal-body">
										<select id="'.$abv.$date.'m" class="form-control">
											<option>-</option>
											<option>12:00 AM</option>
											<option>1:00 PM</option>
											<option>2:00 PM</option>
											<option>3:00 PM</option>
											<option>4:00 PM</option>
											<option>5:00 PM</option>
										</select>
										<button class="btn btn-success" onclick="insertTime(\' '.$abv.' \', \' '.$date.' \', \' '.$row.' \'); updateDB(\' '.$row.' \',\' '.$time.' \', \' '.$abv.' \', \' '.$date.' \')" data-dismiss="modal">Enter</button>
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									  </div>
									</div>

								  </div>
								</div>'
								;
						}
						//function that creates the window for editing tha evening times
						function eveningmodal($date, $time, $abv, $slashdate, $row){
							echo
								'<div id="'.$abv.$date.'" class="modal fade" role="dialog">
								  <div class="modal-dialog">

									<div class="modal-content">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">'.$time.' '.$slashdate.'</h4>
									  </div>
									  <div class="modal-body">
										<select id="'.$abv.$date.'m" class="form-control">
											<option>-</option>
											<option>5:00 PM</option>
											<option>6:00 PM</option>
											<option>7:00 PM</option>
											<option>8:00 PM</option>
											<option>9:00 PM</option>
											<option>10:00 PM</option>
											<option>11:00 PM</option>
											<option>12:00 PM</option>
											<option>1:00 AM</option>
										</select>
										<button class="btn btn-success" onclick="insertTime(\' '.$abv.' \', \' '.$date.' \', \' '.$row.' \'); updateDB(\' '.$row.' \',\' '.$time.' \', \' '.$abv.' \', \' '.$date.' \')" data-dismiss="modal">Enter</button>
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									  </div>
									</div>

								  </div>
								</div>'
								;
						}
						//function that creates the window for editing the reason for absences
						function reasonmodal($date, $slashdate, $row){
							echo
								'<div id="reason'.$date.'" class="modal fade" role="dialog">
								  <div class="modal-dialog">

									<div class="modal-content">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Reason '.$slashdate.'</h4>
									  </div>
									  <div class="modal-body">
										<input type="text" id="reason'.$date.'m" class="form-control">
										<button class="btn btn-success" onclick="insertReason( \' '.$date.' \', '.$row.')" data-dismiss="modal">Enter</button>
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									  </div>
									</div>

								  </div>
								</div>'
								;
						}
						
						echo "<script>
							var week1 = ['".$total[0]."', '".$total[1]."', '".$total[2]."', '".$total[3]."', '".$total[4]."', '".$total[5]."', '".$total[6]."'];
							var week2 = ['".$total[7]."', '".$total[8]."', '".$total[9]."', '".$total[10]."', '".$total[11]."', '".$total[12]."', '".$total[13]."'];
							</script>
						"
					?>
				</tbody>
			</table>
			<h5>I certify that this Time Report is an accurate and complete record of time actually worked during this period. Reasons for any absence are correctly stated by me and I hereby request any applicable pay for the absences as prescribed by any specific policies and regulations involved in accordance with applicable University policies.</h5>
			<form method="post" enctype="multipart/form-data">
				<label for="fileToUpload">Signature</label>
				<input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" class="form-control">
				<br>
				<input type="submit" name="save" value="Save" class="btn btn-primary">
				<input type="submit" name="submit" value="Submit" class="btn btn-success">
				<a href="logout.php" class="btn btn-danger">Log Off</a>
			</form>
		</div>
		<script src="editFunctions.js"></script>
	</body>
</html>