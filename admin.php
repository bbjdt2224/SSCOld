<?php
	include("session.php");
	if(isset($_POST['yes'])){
		$getnames = "select * from admin";
		$query = mysqli_query($db, $getnames);
		$numNames = mysqli_num_rows($query);
		for($i = 0; $i < $numNames; $i ++){
			$updatesubmited = "update admin set submitted = 0 where id= ". $i;
			mysqli_query($db, $updatesubmited);
		}
	}
	

	$getAll = "select * from admin";
	$all = mysqli_query($db, $getAll);
	$numAll = mysqli_num_rows($all);
	for($i = 0; $i < $numAll; $i ++){
		$row = mysqli_fetch_array($all);
		if(isset($_POST[$row["username"]])){
			$getid = "select id from admin where username = '".$row["username"]."'";
			$id = mysqli_query($db, $getid);
			$idrow = mysqli_fetch_array($id);
			$id = $idrow['id'];
			$removeName = "delete from admin where username = '". $row["username"]."'";
			mysqli_query($db, $removeName);
			$dropTable = "drop table ". $row["username"];
			mysqli_query($db, $dropTable);
			for($i = $id; $i < $numAll; $i ++){
				$changeNumber = "update admin set id = ". $i." where id = ". ($i+1);
				mysqli_query($db, $changeNumber);
			}
		}
	}
?>

<html>
	<head>
		<title>Admin List</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<table class="table">
				<thead>
					<th>Name</th>
					<th>Submited</th>
					<th></th>
					<th></th>
					<th>Hour Diffrence</th>
					<th>Remove Employee</th>
				</thead>
				<tbody>
					<?php
						$getNames = "select * from admin";
						$rows = mysqli_query($db, $getNames);
						$num = mysqli_num_rows($rows);
						
						for($i = 0; $i < $num; $i ++){
							$row = mysqli_fetch_array($rows);
							if($row['Name'] != "admin"){
								echo "<tr><td>". $row["Name"]. "</td><td>";
								if($row['submitted'] == 1){
									echo "Yes";
								}
								else{
									echo "No";
								}
								
								$total = 0;
								$previous = 0;
								
								$getHours = "select Total, previous from ". $row["username"];
								$hours = mysqli_query($db, $getHours);
								for($j = 0; $j < 14; $j++){
									$hrow = mysqli_fetch_array($hours);
									$total += $hrow["Total"];
									$previous += $hrow["previous"];
								}
								
								$diffrence = $total - $previous;
								
								echo "</td><td><form method='post' action='viewEmployee.php' target='_blank'><input type='hidden' name='username' value='".$row["username"]."'><button type='submit' class='btn btn-info'>View</button></td><td></form><form method='post' action='editEmployee.php'><input type='hidden' name='username' value='".$row["username"]."'><button type='submit' class='btn btn-info'>Edit</button></form></td>
								<td>".$diffrence."</td>
								<td><button type='button' class='btn btn-danger' data-toggle='modal' data-target='#".$row["username"]."'>Delete</button></td></tr>";
								deleteModal($row["username"]);
							}	
						}
						
						function deleteModal($username){
							echo "<div id='".$username."' class='modal fade' role='dialog'>
									  <div class='modal-dialog'>

										<!-- Modal content-->
										<div class='modal-content'>
										  <div class='modal-header'>
											<button type='button' class='close' data-dismiss='modal'>&times;</button>
											<h4 class='modal-title'>Are You Sure?</h4>
										  </div>
										  <div class='modal-body'>
											<form method='post'>
												<input type='submit' class='btn btn-success' name='".$username."' value='Delete'>
											</form>
										  </div>
										  <div class='modal-footer'>
											<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
										  </div>
										</div>

									  </div>
									</div>";
						}
					?>
				</tbody>
			</table>
			<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#areyousure">Clear Submitted</button>
			<a href="logout.php" class="btn btn-danger">Log Off</a>
			<div id="areyousure" class="modal fade" role="dialog">
			  <div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Are You Sure?</h4>
				  </div>
				  <div class="modal-body">
					<form method="post">
						<input type="submit" class="btn btn-success" name="yes" value="Clear">
					</form>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  </div>
				</div>

			  </div>
			</div>
		</div>
	</body>
</html>