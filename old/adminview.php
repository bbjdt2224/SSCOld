<?php
	include("session.php");
	if($login_session != 'admin'){
		header("location: logout.php");
	}
	$viewuser = "";
	 $result = mysqli_query($db,"SELECT name, username FROM admin");
	 while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
		 if(isset($_GET['users']) && $_GET['users'] == $row["name"]){
			 $viewuser = $row['username'];
			 $_SESSION["currentuser"] = $viewuser;
		 }
	 }
?>

<script type="text/javascript" src='export.js'></script>
<html>
	<head>
		<title>Admin View</title>
		<link rel="stylesheet" type="text/css" href="timesheetStyle.css"/>
		<script>
			function tableToJson(table) {
				var data = [];

				// first row needs to be headers
				var headers = [];
				for (var i=0; i<table.rows[0].cells.length; i++) {
					headers[i] = table.rows[0].cells[i].innerHTML.toLowerCase().replace(/ /gi,'');
				}
				data.push(headers);
				// go through cells
				for (var i=1; i<table.rows.length; i++) {

					var tableRow = table.rows[i];
					var rowData = {};

					for (var j=0; j<tableRow.cells.length; j++) {

						rowData[ headers[j] ] = tableRow.cells[j].innerHTML;

					}

					data.push(rowData);
				}       

				return data;
			}

			function callme(){
				var table = tableToJson($('#schedule').get(0));
				var doc = new jsPDF('l','pt','letter',true);
				doc.setFontSize(8);


				$.each(table, function(i, row){
					$.each(row, function(j,cell){
					 doc.cell(10,10,50,10,cell,i);	
					
					});
				});

				doc.save('SSC.pdf');
			}
		</script>
	</head>
	<body>
		<?php
		
			$sel = "SELECT name FROM admin WHERE username='".$viewuser."'";
			$r = mysqli_query($db,$sel);
			$row = mysqli_fetch_array($r,MYSQLI_ASSOC);
			$name = $row["name"];
		
			$sch = array(
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", ""),
				array("","", "", "", "", "")
			);
			$tablearray = array();
			$total = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
	
			$colnames = array("morningbegin", "morningend", "afternoonbegin", "afternoonend", "eveningbegin", "eveningend");
			
			$text = array("-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-");
			
			for($i = 1; $i < 15; $i++){
				for($j = 0; $j < 6; $j++){
					if(isset($_POST['t'.($i-1).$j])){
						$sch[$i][$j] = $_POST["t".($i-1).$j];
						$update = "update ".$viewuser." set ".$colnames[$j]."='".$_POST['t'.($i-1).$j]."' where Row='".$i."'";
						$db->query($update);
					}
				}
			}
			
			for($i = 1; $i < 15; $i++){
				for($j = 0; $j < 6; $j++){
					$sel = "SELECT ".$colnames[$j]." FROM ".$viewuser." WHERE Row='".$i."'";
						$r = mysqli_query($db,$sel);
						$row = mysqli_fetch_array($r,MYSQLI_ASSOC);
						$sch[$i-1][$j] = $row[$colnames[$j]];
				}
			}
			
			for($i = 1; $i < 15; $i++){
				$selectreason = "select reason from ".$viewuser." where Row=".$i;
				$result = mysqli_query($db,$selectreason);
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$text[$i-1] = $row['reason'];
			}
		
			$selecttime = "select other from ".$viewuser." where Row='1'";
			if(($result = mysqli_query($db, $selecttime)) != null){
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$startTime = strtotime($row['other']);
			}
			else{
				$startTime = time();
			}
			
			for($j = 1; $j< 15; $j++){
				if(isset($_POST["t".$j])){
					$updateprevious = "update ".$viewuser." set previous='".$_POST['t'.$j]."' where Row='".$j."'";
					mysqli_query($db, $updateprevious);
				}
			}
		
			
			$endTime = $startTime + 1209600;
			$oneday = 86400;
			$timesheet = "<table id='schedule'>";
			$timesheet .= "<colgroup>";
			for($i = 0; $i < 16; $i++){
				$timesheet .= "<col width='120px'></col>";
			}
			$timesheet .= "</colgroup><tr>";
			for($i = 0; $i < 16; $i ++){
				$timesheet .= "<td></td>";
			}
			$timesheet .= "</tr><tr><td class='center' colspan='5' rowspan='4'>STEP Program</td><td class='center' colspan='5'>Western Michigan University</td><td colspan='6' rowspan='4'>This form is to be completed on a daily basis and it must be received by the department with the appropriate signature prior to authorization for payment of hours on the regular Payroll Time Sheet. The employee must account for all of the time she/he is scheduled to work during each pay period on this Time Report. Do not include unpaid lunch periods.</td></tr>";
			$timesheet .= "<tr><td class='center' colspan='5'>STEP Program</td></tr>";
			$timesheet .= "<tr><td colspan='5'></td></tr>";
			$timesheet .= "<tr><td colspan='5'></td></tr>";
			$timesheet .= "<tr><td colspan='2'>Name (Last, First, Mid)</td><td class='b' colspan='4'>$name</td><td class='nb' colspan='2'></td><td class='nb right' colspan='3'>Fund & Cost Center: </td><td class='b' colspan='5'>25-70148900  - JOB CODE: 060084</td></tr>";
			$timesheet .= "<tr><td colspan='2'>Empolye ID Number: </td><td class='b' colspan='4' bgcolor='black'></td><td colspan='2'></td><td class='right' colspan='3'>Pay Period Begin: </td><td class='b' colspan='2'><a href='javascript:void(0)' onclick=\"document.getElementById('time').style.display='block'\">".date("m/d/y", $startTime)."</a></td><td class='nb'>End: </td><td class='b' colspan='2'>".date("m/d/y", $endTime)."</td></tr>";
			$timesheet .= "<tr><td colspan='16' height='10px'></td></tr>";
			$timesheet .= "<tr><td class='b center' rowspan='2'>Day</td><td class='b center' rowspan='2'>Date</td><td class='b center' colspan='2'>Morning</td><td class='b center' colspan='2'>Afternoon</td><td class='b center' colspan='2'>Evening</td><td class='b center' rowspan='2'>Regular Hours</td><td class='b center' rowspan='2'>Overtime Hours</td><td class='b center' rowspan='2' colspan='5'>Reasons for any absence</td><td class='b center' rowspan='2'>Daily Total Hours</td></tr>";
			$timesheet .= "<tr><td class='b center'>Time Began</td><td class='b center'>Time Ended</td><td class='b center'>Time Began</td><td class='b center'>Time Ended</td><td class='b center'>Time Began</td><td class='b center'>Time Ended</td>";
			for($i = 2; $i < 9; $i++){
				$timesheet .= "<tr><td class='b'>".date("l", mktime(0,0,0,1,$i,17))."</td><td class='b right'>".date("m/d/y",($startTime+(($i-2)*$oneday)))."</td>";
				for($j = 0; $j < 6; $j++){
					$timesheet .= "<td class='b right'>".$sch[$i-2][$j]."</td>";
				}
				$total[$i-2] = (calhours($sch[$i-2][0], $sch[$i-2][1], $sch[$i-2][2], $sch[$i-2][3], $sch[$i-2][4], $sch[$i-2][5])/3600);
				$timesheet .= "<td class='b right'>".$total[$i-2]."</td><td class='b'></td><td class='b center' colspan='5'>".$text[$i-2]."</td><td class='b right'>".$total[$i-2]."</td></tr>";
				$updatetotal = "update ".$viewuser." set Total='".$total[$i-2]."' where Row='".($i-1)."'";
				$db->query($updatetotal);
				
			}
			$temptot = $total[0] + $total[1] + $total[2] + $total[3] + $total[4] + $total[5] + $total[6];
			$timesheet .= "<tr><td class='b' colspan='8'></td><td class='b right'>$temptot</td><td class='b'></td><td class='b'></td><td class='b right' colspan='4'>Total Hours - First Week-> </td><td class='b right'>$temptot</td></tr>";
			for($i = 2; $i < 9; $i++){
				$timesheet .= "<tr><td class='b'>".date("l", mktime(0,0,0,1,$i,17))."</td><td class='b right'>".date("m/d/y",($startTime+(($i+5)*$oneday)))."</td>";
				for($j = 0; $j < 6; $j++){
					$timesheet .= "<td class='b right'>".$sch[$i+5][$j]."</td>";
				}
				$total[$i+5] = (calhours($sch[$i+5][0], $sch[$i+5][1], $sch[$i+5][2], $sch[$i+5][3], $sch[$i+5][4], $sch[$i+5][5])/3600);
				$timesheet .= "<td class='b right'>".$total[$i+5]."</td><td class='b'></td><td class='b center' colspan='5'>".$text[$i+5]."</td><td class='b right'>".$total[$i+5]."</td></tr>";
				$updatetotal = "update ".$viewuser." set Total='".$total[$i+5]."' where Row='".($i+6)."'";
				$db->query($updatetotal);
				
			}
			$temptot = $total[7] + $total[8] + $total[9] + $total[10] + $total[11] + $total[12] + $total[13];
			$timesheet .= "<tr><td class='b' colspan='8'></td><td class='b right'>$temptot</td><td class='b'></td><td class='b'></td><td class='b right' colspan='4'>Total Hours - Second Week-> </td><td class='b right'>$temptot</td></tr>";
			$temptot = $total[0] + $total[1] + $total[2] + $total[3] + $total[4] + $total[5] + $total[6] + $total[7] + $total[8] + $total[9] + $total[10] + $total[11] + $total[12] + $total[13];
			$timesheet .= "<tr><td class='nb' colspan='8' rowspan='3'>I certify that this Time Report is an accurate and complete record of time actually worked during this period. Reasons for any absence are correctly stated by me and I hereby request any applicable pay for the absences as prescribed by any specific policies and regulations involved in accordance with applicable University policies.</td><td class='b right'>$temptot</td><td class='b'></td><td class='b right' colspan='5'>Total Hours For Pay Period-> </td><td class='b right'>$temptot</td></tr>";
			$timesheet .= "<tr><td rowspan='2' colspan='8'></td></tr>";
			$timesheet .= "<tr></tr>";
			$timesheet .= "<tr><td class='b' rowspan='2' colspan='8' height='40px'></td><td rowspan='3'></td><td class='b' rowspan='2' colspan='7'></td></tr>";
			$timesheet .= "<tr></tr>";
			$timesheet .= "<tr><td class='right' colspan='8'>Signature</td><td class='center' colspan='7'>Appproval by: Department Manager or Supervisor</td></tr>";
			$timesheet .= "</table>";
			
			echo $timesheet;
			
			echo "<div id='editor'></div>";
			
			echo "</br></br>";
			
			$selectprevious = "select Row, Total, previous from ".$viewuser;
			$result = mysqli_query($db, $selectprevious);
			
			$timediffrence = "<table>";
			$timediffrence .= "<colgroup>";
			$timediffrence .= str_repeat("<col width='100px'></col>", 15);
			$timediffrence .= "</colgroup>";
			$timediffrence .= "<th></th><th class='b'>Monday 1</th><th class='b'>Tuseday 1</th><th class='b'>Wednesday 1</th><th class='b'>Thursday 1</th><th class='b'>Friday 1</th><th class='b'>Saturday 1</th><th class='b'>Sunday 1</th><th class='b'>Monday 2</th><th class='b'>Tusedy 2</th><th class='b'>Wednesday 2</th><th class='b'>Thursday 2</th><th class='b'>Friday 2</th><th class='b'>Saturday 2</th><th class='b'>Sunday 2</th>";
			$timediffrence .= "<tr><td>Changed</td>";
			while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
				 $timediffrence .= "<td class='b center'>".($row['Total']-$row['previous'])."</td>";
			 }
			 $selectprevious = "select Row, previous from ".$viewuser;
			 $result = mysqli_query($db, $selectprevious);
			 $timediffrence .= "</tr><tr><td>Schedule</td>";
			 while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
				 $timediffrence .= "<td class='b center'><a href='javascript:void(0)' onclick=\"document.getElementById('".$row['Row']."').style.display = 'block'\">".$row['previous']."</a></td>";
			 }
			 $timediffrence .= "</tr></table>";
			 echo $timediffrence;
			
			function calhours($mb, $me, $ab, $ae, $eb, $ee){
				$mb = strtotime($mb);
				$me = strtotime($me);
				$ab = strtotime($ab);
				$ae = strtotime($ae);
				$eb = strtotime($eb);
				$ee = strtotime($ee);
				if(($ee - $eb) < 0){
					$ee += 86400;
				}
				if(($ae - $ab) < 0){
					$ae += 86400;
				}
				if(($me - $mb) < 0){
					$me += 86400;
				}
				$e = $ee - $eb;
				$a = $ae - $ab;
				$m = $me - $mb;
				return $e+$a+$m;
			}
			
			for($i = 1; $i < 15; $i++){
				echo "<div class='timechange' id='".$i."' style='display: none'><form action='' method='post'><select name='t".$i."'><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option></select></br><input type='submit' name='time'></form></div>";
			}
		?>
		
		<h2><a href='admin.php'>Go Back</a></h2>
		<a href="javascript:callme()">PDF</a>
		<script type="text/javascript" src="js/jquery-2.1.3.js"></script>
		<script type="text/javascript" src="js/jspdf.js"></script>
		
		
		<form action='pdf.php' method='post'><input id='exports' type='submit' value='Export'></form>
	</body>
</html>