<?php
	include('config.php');
	session_start();
	$row = $_GET['r'];
	$col = $_GET['c'];
	$data = $_GET['d'];
	$sql = "update `".$_SESSION['login_user']."` set ".$col."= '".$data."' where Row='".$row."'";
	echo $sql;
	$result = mysqli_query($db, $sql);
	?>