<?php
	include("session.php");
	if($login_session != 'admin'){
		header("location: logout.php");
	}
	$viewuser = $_SESSION["currentuser"];
	
	$sel = "SELECT name FROM admin WHERE username='".$viewuser."'";
	$r = mysqli_query($db,$sel);
	$row = mysqli_fetch_array($r,MYSQLI_ASSOC);
	$name = $row["name"];
	
	$file = $name.".xls";
	
	$selecttime = "select other from ".$viewuser." where Row='1'";
	if(($result = mysqli_query($db, $selecttime)) != null){
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$startTime = strtotime($row['other']);
	}
	else{
		$startTime = time();
	}
	
	$endTime = $startTime + 1209600;
	$oneday = 86400;
	
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
	
	$colnames = array("morningbegin", "morningend", "afternoonbegin", "afternoonend", "eveningbegin", "eveningend");
	
	$text = array("-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-");
	
	for($i = 1; $i < 15; $i++){
		$selectreason = "select reason from ".$viewuser." where Row=".$i;
		$result = mysqli_query($db,$selectreason);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$text[$i-1] = $row['reason'];
	}
	
	for($i = 1; $i < 15; $i++){
		for($j = 0; $j < 6; $j++){
			$sel = "SELECT ".$colnames[$j]." FROM ".$viewuser." WHERE Row='".$i."'";
				$r = mysqli_query($db,$sel);
				$row = mysqli_fetch_array($r,MYSQLI_ASSOC);
				$sch[$i-1][$j] = $row[$colnames[$j]];
		}
	}
	
	$timesheet = "<html><head><title>Export</title></head><body>";
	$timesheet .= "<table id='schedule'>";
	$timesheet .= "<colgroup>";
	for($i = 0; $i < 16; $i++){
		$timesheet .= "<col width='120px'></col>";
	}
	$timesheet .= "</colgroup>";
	$timesheet .= "<tr><td class='center' colspan='5' rowspan='4'>STEP Program</td><td class='center' colspan='5'>Western Michigan University</td><td colspan='6' rowspan='4'>This form is to be completed on a daily basis and it must be received by the department with the appropriate signature prior to authorization for payment of hours on the regular Payroll Time Sheet. The employee must account for all of the time she/he is scheduled to work during each pay period on this Time Report. Do not include unpaid lunch periods.</td></tr>";
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
	$timesheet .= "</body></html>";
	
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
	
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$file");
	echo $timesheet;
	
	
?>