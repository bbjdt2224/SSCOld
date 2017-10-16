<?php
	include("session.php");
	if($login_session == 'admin'){
		header("location: admin.php");
	}
?>
<html>
	<head>
		<title>Timesheet</title>
		<link rel="stylesheet" type="text/css" href="timesheetStyle.css"/>
	</head>
	<body>
		<?php
		
			$sel = "SELECT name FROM admin WHERE username='".$login_session."'";
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
			$total = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$text = array("-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-");
	
			$colnames = array("morningbegin", "morningend", "afternoonbegin", "afternoonend", "eveningbegin", "eveningend");
			
			for($i = 1; $i < 15; $i++){
				for($j = 0; $j < 6; $j++){
					if(isset($_POST['t'.($i-1).$j])){
						$sch[$i][$j] = $_POST["t".($i-1).$j];
						$update = "update ".$login_session." set ".$colnames[$j]."='".$_POST['t'.($i-1).$j]."' where Row='".$i."'";
						$db->query($update);
					}
				}
			}
			
			for($i = 1; $i < 15; $i++){
				for($j = 0; $j < 6; $j++){
					$sel = "SELECT ".$colnames[$j]." FROM ".$login_session." WHERE Row='".$i."'";
						$r = mysqli_query($db,$sel);
						$row = mysqli_fetch_array($r,MYSQLI_ASSOC);
						$sch[$i-1][$j] = $row[$colnames[$j]];
				}
			}
			
			for($i = 0; $i < 15; $i ++){
				if(isset($_POST['text'.$i])){
					$text[$i] = $_POST['text'.$i];
					$updatereason = "update ".$login_session." set reason='".$_POST['text'.$i]."' where Row='".($i+1)."'";
					mysqli_query($db,$updatereason);
				}
			}
			
			for($i = 1; $i < 15; $i++){
				$selectreason = "select reason from ".$login_session." where Row=".$i;
				$result = mysqli_query($db,$selectreason);
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$text[$i-1] = $row['reason'];
			}
		
			if(isset($_POST["month"]) && isset($_POST["day"]) && isset($_POST["changedate"])){
				$updatetime = "update ".$login_session." set other='".$_POST["month"].$_POST['day']."' where Row='1'";
				mysqli_query($db, $updatetime);
			}
			$selecttime = "select other from ".$login_session." where Row='1'";
			if(($result = mysqli_query($db, $selecttime)) != null){
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$startTime = strtotime($row['other']);
			}
			else{
				$startTime = time();
			}
		
			
			$endTime = $startTime + 1209600;
			$oneday = 86400;
		
			$timesheet = "<table>";
			$timesheet .= "<colgroup>";
			for($i = 0; $i < 16; $i++){
				$timesheet .= "<col width='120px'></col>";
			}
			$selectfund = "select other from $login_session where Row='2'";
			$result = mysqli_query($db, $selectfund);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$fund = $row['other'];
			$selectcode = "select other from $login_session where Row='3'";
			$result = mysqli_query($db, $selectcode);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$code = $row['other'];
			
			$timesheet .= "</colgroup>";
			$timesheet .= "<tr><td class='center' colspan='5' rowspan='4'>STEP Program</td><td class='center' colspan='5'>Western Michigan University</td><td colspan='6' rowspan='4'>This form is to be completed on a daily basis and it must be received by the department with the appropriate signature prior to authorization for payment of hours on the regular Payroll Time Sheet. The employee must account for all of the time she/he is scheduled to work during each pay period on this Time Report. Do not include unpaid lunch periods.</td></tr>";
			$timesheet .= "<tr><td class='center' colspan='5'>STEP Program</td></tr>";
			$timesheet .= "<tr><td class='nb' colspan='5'></td></tr>";
			$timesheet .= "<tr><td class='nb' colspan='5'></td></tr>";
			$timesheet .= "<tr><td class='nb' colspan='2'>Name (Last, First, Mid)</td><td class='b' colspan='4'>$name</td><td class='nb' colspan='2'></td><td class='nb right' colspan='3'>Fund & Cost Center: </td><td class='b' colspan='5'>$fund  - JOB CODE: $code</td></tr>";
			$timesheet .= "<tr><td class='nb' colspan='2'>Empolye ID Number: </td><td class='b' colspan='4' bgcolor='black'></td><td colspan='2'></td><td class='right' colspan='3'>Pay Period Begin: </td><td class='b' colspan='2'><a href='javascript:void(0)' onclick=\"document.getElementById('time').style.display='block'\">".date("m/d/y", $startTime)."</a></td><td class='nb'>End: </td><td class='b' colspan='2'>".date("m/d/y", $endTime)."</td></tr>";
			$timesheet .= "<tr><td class='nb' colspan='16' height='10px'></td></tr>";
			$timesheet .= "<tr><td class='b center' rowspan='2'>Day</td><td class='b center' rowspan='2'>Date</td><td class='b center' colspan='2'>Morning</td><td class='b center' colspan='2'>Afternoon</td><td class='b center' colspan='2'>Evening</td><td class='b center' rowspan='2'>Regular Hours</td><td class='b center' rowspan='2'>Overtime Hours</td><td class='b center' rowspan='2' colspan='5'>Reasons for any absence</td><td class='b center' rowspan='2'>Daily Total Hours</td></tr>";
			$timesheet .= "<tr><td class='b center'>Time Began</td><td class='b center'>Time Ended</td><td class='b center'>Time Began</td><td class='b center'>Time Ended</td><td class='b center'>Time Began</td><td class='b center'>Time Ended</td>";
			for($i = 2; $i < 9; $i++){
				$timesheet .= "<tr><td class='b'>".date("l", mktime(0,0,0,1,$i,17))."</td><td class='b right'>".date("m/d/y",($startTime+(($i-2)*$oneday)))."</td>";
				for($j = 0; $j < 6; $j++){
					$timesheet .= "<td class='b right'><a href='javascript:void(0)' onclick=\"document.getElementById('".($i-2).$j."').style.display='block'\">".$sch[$i-2][$j]."</a></td>";
				}
				$total[$i-2] = (calhours($sch[$i-2][0], $sch[$i-2][1], $sch[$i-2][2], $sch[$i-2][3], $sch[$i-2][4], $sch[$i-2][5])/3600);
				if($total[$i-2] < 1){
					$total[$i-2] = '';
				}
				$timesheet .= "<td class='b right'>".$total[$i-2]."</td><td class='b'></td><td class='b center' colspan='5'><a href='javascript:void(0)' onclick=\"document.getElementById('text".($i-2)."').style.display = 'block'\">".$text[$i-2]."</a></td><td class='b right'>".$total[$i-2]."</td></tr>";
				$updatetotal = "update ".$login_session." set Total='".$total[$i-2]."' where Row='".($i-1)."'";
				$db->query($updatetotal);
				
			}
			$temptot = $total[0] + $total[1] + $total[2] + $total[3] + $total[4] + $total[5] + $total[6];
			$timesheet .= "<tr><td class='b' colspan='8'></td><td class='b right'>$temptot</td><td class='b'></td><td class='b'></td><td class='b right' colspan='4'>Total Hours - First Week-> </td><td class='b right'>$temptot</td></tr>";
			for($i = 2; $i < 9; $i++){
				$timesheet .= "<tr><td class='b'>".date("l", mktime(0,0,0,1,$i,17))."</td><td class='b right'>".date("m/d/y",($startTime+(($i+5)*$oneday)))."</td>";
				for($j = 0; $j < 6; $j++){
					$timesheet .= "<td class='b right'><a href='javascript:void(0)' onclick=\"document.getElementById('".($i+5).$j."').style.display='block'\">".$sch[$i+5][$j]."</a></td>";
				}
				$total[$i+5] = (calhours($sch[$i+5][0], $sch[$i+5][1], $sch[$i+5][2], $sch[$i+5][3], $sch[$i+5][4], $sch[$i+5][5])/3600);
				if($total[$i+5] < 1){
					$total[$i+5] = '';
				}
				$timesheet .= "<td class='b right'>".$total[$i+5]."</td><td class='b'></td><td class='b center' colspan='5'><a href='javascript:void(0)' onclick=\"document.getElementById('text".($i+5)."').style.display = 'block'\">".$text[$i+5]."</a></td><td class='b right'>".$total[$i+5]."</td></tr>";
				$updatetotal = "update ".$login_session." set Total='".$total[$i+5]."' where Row='".($i+6)."'";
				$db->query($updatetotal);
				
			}
			$temptot = $total[7] + $total[8] + $total[9] + $total[10] + $total[11] + $total[12] + $total[13];
			$timesheet .= "<tr><td class='b' colspan='8'></td><td class='b right'>$temptot</td><td class='b'></td><td class='b'></td><td class='b right' colspan='4'>Total Hours - Second Week-> </td><td class='b right'>$temptot</td></tr>";
			$temptot = $total[0] + $total[1] + $total[2] + $total[3] + $total[4] + $total[5] + $total[6] + $total[7] + $total[8] + $total[9] + $total[10] + $total[11] + $total[12] + $total[13];
			$timesheet .= "<tr><td colspan='8' rowspan='3'>I certify that this Time Report is an accurate and complete record of time actually worked during this period. Reasons for any absence are correctly stated by me and I hereby request any applicable pay for the absences as prescribed by any specific policies and regulations involved in accordance with applicable University policies.</td><td class='b right'>$temptot</td><td class='b'></td><td class='b right' colspan='5'>Total Hours For Pay Period-> </td><td class='b right'>$temptot</td></tr>";
			$timesheet .= "<tr><td class='nb' rowspan='2' colspan='8'></td></tr>";
			$timesheet .= "<tr></tr>";
			$timesheet .= "<tr><td class='b' rowspan='2' colspan='8' height='40px'></td><td rowspan='3'></td><td class='b' rowspan='2' colspan='7'></td></tr>";
			$timesheet .= "<tr></tr>";
			$timesheet .= "<tr><td class='right' colspan='8'>Signature</td><td class='center' colspan='7'>Appproval by: Department Manager or Supervisor</td></tr>";
			$timesheet .= "</table>";
			
			echo $timesheet;
			
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
		?>
		<div class='timechange' id="time" style="display: none">
			<form action="" method="post">
				<a href="javascript:void(0)" onclick="document.getElementById('time').style.display='none'" bgcolor="red">X</a>
				<?php
					$month = "<select name='month'>";
					for($i = 1; $i < 13; $i++){
						$month .= "<option>". date("F", mktime(0,0,0,$i,1,2017))."</option>";
					}
					$month .= "</select>";
					$day = "<select name='day'>";
					for($i = 1; $i < 32; $i++){
						$day .= "<option>$i</option>";
					}
					$day .= "</select>";
					echo $month;
					echo $day;
					echo "<button type='submit' name='changedate'>Submit</button>";
				?>
			</form>
		</div>
			<?php
				$morning = "<option> - </option><option>7:00 AM</option><option>8:00AM</option><option>9:00 AM</option><option>10:00 AM</option><option>11:00 AM</option><option>12:00 PM</option>";
				$afternoon = "<option> - </option><option>12:00 PM</option><option>1:00 PM</option><option>2:00 PM</option><option>3:00 PM</option><option>4:00 PM</option><option>5:00 PM</option>";
				$evening = "<option> - </option><option>5:00 PM</option><option>6:00 PM</option><option>7:00 PM</option><option>8:00 PM</option><option>9:00 PM</option><option>10:00 PM</option><option>11:00 PM</option><option>12:00 AM</option><option>1:00 AM</option>";
				for($i = 0; $i < 15; $i++){
					for($j = 0; $j < 6; $j++){
						if($j == 0 || $j == 1){
							echo "<div class='timechange' id='".$i.$j."' style='display: none'><form action='' method='post'><select name='t".$i.$j."'>$morning</select><button type='submit' name='$i$j'>Submit</button></form></div>";
						}
						elseif($j == 2 || $j == 3){
							echo "<div class='timechange' id='".$i.$j."' style='display: none'><form action='' method='post'><select name='t".$i.$j."'>$afternoon</select><button type='submit' name='$i$j'>Submit</button></form></div>";
						}
						elseif($j == 4 || $j == 5){
							echo "<div class='timechange' id='".$i.$j."' style='display: none'><form action='' method='post'><select name='t".$i.$j."'>$evening</select><button type='submit' name='$i$j'>Submit</button></form></div>";
						}
						
					}
				}
				for($i = 0; $i < 15; $i++){
						echo "<div class='timechange' id='text".$i."' style='display: none'><form action='' method='post'><input type='text' name='text".$i."' value='-'><button type='submit' name='$i$j'>Submit</button></form></div>";
				}
			?>
			
			<form action='submit.php' method='post'>
				<input type='submit' name='submit' value='Submit'/>
			</form>
		
		<h2><a href="logout.php">Sign Out</a></h2>
	</body>
</html>