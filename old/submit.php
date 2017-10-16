<?php
	include("session.php");
	$submitsheet = "update admin set submited='yes' where username='".$login_session."'";
	mysqli_query($db, $submitsheet);
	echo "Timesheet Submitted";
	echo "</br><h2><a href='logout.php'>Done</a></h2>";
?>