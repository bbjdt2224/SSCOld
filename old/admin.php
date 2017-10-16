<?php
	include("session.php");
	if($login_session != 'admin'){
		header("location: logout.php");
	}
	
	if(isset($_POST['clear'])){
		$clear = "update admin set submited = 'no' where submited='yes'";
		mysqli_query($db, $clear);
	}
?>

<html>
	<head>
		<title>Administrative</title>
		
	</head>
	<body>
		<?php
			 $result = mysqli_query($db,"SELECT name, submited FROM admin");
			 echo "<form mehtod='get' action='adminview.php'><table><th>Name</th><th>Submited</th>";
			 while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
				 if($row['name'] != 'admin'){
					echo "<tr><td><input type='submit' value='".$row['name']."' name='users'/></td><td>".$row['submited']."</td></tr>";
				 }
			 }
			 echo "</table></form>";
		?>
		<form action="" method="post">
			<input type='submit' name='clear' value='Clear All'>
		</form>
	<h2><a href="logout.php">Sign Out</a></h2>
	</body>
</html>