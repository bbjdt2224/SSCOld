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
	
	$file = $name.".pdf";
	
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
	
	$timesheet = "<html><head><title>Export</title><script>
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
					 doc.cell(10,10,48,10,cell,i);	
					
					});
				});

				doc.save('".$file."');
			}
		</script></head><body>";
	$timesheet .= "<table id='schedule'>";
	$timesheet .= "<colgroup>";
	for($i = 0; $i < 16; $i++){
		$timesheet .= "<col width='120px'></col>";
	}
	$timesheet .= "</colgroup><tr>";
	for($i = 0; $i < 16; $i ++){
				$timesheet .= "<td>".$i."</td>";
			}
	$timesheet .= "</tr><tr><td>Name</td><td class='b' colspan='4'>".$name."</td><td></td><td class='nb'></td><td></td><td class='nb right'></td><td></td><td></td><td class='b'>25-70148900 </td><td>JOB CODE: </td><td>060084</td><td></td><td></td></tr>";
	$timesheet .= "<tr><td></td><td></td><td class='b' bgcolor='black'></td><td></td><td></td><td></td><td></td><td></td><td class='right'>Pay Period</td><td>Begin: </td><td class='b'>".date("m/d/y", $startTime)."</td><td></td><td></td><td class='nb'>End: </td><td class='b'>".date("m/d/y", $endTime)."</td><td></td></tr>";
	$timesheet .= "<tr><td class='b center'>Day</td><td class='b center'>Date</td><td class='b center'>Morning</td><td></td><td class='b center'>Afternoon</td><td></td><td class='b center'>Evening</td><td></td><td class='b center'>Regular Hours</td><td class='b center'>Overtime Hours</td><td class='b center'>Reasons for</td><td>any absence</td><td></td><td></td><td></td><td class='b center'>Daily Total Hours</td></tr>";
	$timesheet .= "<tr><td></td><td></td><td class='b center'>Time Began</td><td class='b center'>Time Ended</td><td class='b center'>Time Began</td><td class='b center'>Time Ended</td><td class='b center'>Time Began</td><td class='b center'>Time Ended</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
	for($i = 2; $i < 9; $i++){
		$timesheet .= "<tr><td class='b'>".date("l", mktime(0,0,0,1,$i,17))."</td><td class='b right'>".date("m/d/y",($startTime+(($i-2)*$oneday)))."</td>";
		for($j = 0; $j < 6; $j++){
			$timesheet .= "<td class='b right'>".$sch[$i-2][$j]."</td>";
		}
		$total[$i-2] = (calhours($sch[$i-2][0], $sch[$i-2][1], $sch[$i-2][2], $sch[$i-2][3], $sch[$i-2][4], $sch[$i-2][5])/3600);
		$timesheet .= "<td class='b right'>".$total[$i-2]."</td><td class='b'></td><td class='b center'>".$text[$i-2]."</td><td></td><td></td><td></td><td></td><td class='b right'>".$total[$i-2]."</td></tr>";
		$updatetotal = "update ".$viewuser." set Total='".$total[$i-2]."' where Row='".($i-1)."'";
		$db->query($updatetotal);
		
	}
	$temptot = $total[0] + $total[1] + $total[2] + $total[3] + $total[4] + $total[5] + $total[6];
	$timesheet .= "<tr><td class='b'></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class='b right'>$temptot</td><td class='b'></td><td class='b'></td><td></td><td></td><td></td><td class='b right'>First Week </td><td class='b right'>$temptot</td></tr>";
	for($i = 2; $i < 9; $i++){
		$timesheet .= "<tr><td class='b'>".date("l", mktime(0,0,0,1,$i,17))."</td><td class='b right'>".date("m/d/y",($startTime+(($i+5)*$oneday)))."</td>";
		for($j = 0; $j < 6; $j++){
			$timesheet .= "<td class='b right'>".$sch[$i+5][$j]."</td>";
		}
		$total[$i+5] = (calhours($sch[$i+5][0], $sch[$i+5][1], $sch[$i+5][2], $sch[$i+5][3], $sch[$i+5][4], $sch[$i+5][5])/3600);
		$timesheet .= "<td class='b right'>".$total[$i+5]."</td><td class='b'></td><td class='b center'".$text[$i+5]."</td><td></td><td></td><td></td><td></td><td class='b right'>".$total[$i+5]."</td></tr>";
		$updatetotal = "update ".$viewuser." set Total='".$total[$i+5]."' where Row='".($i+6)."'";
		$db->query($updatetotal);
		
	}
	$temptot = $total[7] + $total[8] + $total[9] + $total[10] + $total[11] + $total[12] + $total[13];
	$timesheet .= "<tr><td class='b'></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class='b right'>$temptot</td><td class='b'></td><td class='b'></td><td></td><td></td><td></td><td class='b right'>Second Week </td><td class='b right'>$temptot</td></tr>";
	$temptot = $total[0] + $total[1] + $total[2] + $total[3] + $total[4] + $total[5] + $total[6] + $total[7] + $total[8] + $total[9] + $total[10] + $total[11] + $total[12] + $total[13];
	$timesheet .= "<tr><td class='nb' rowspan='3'></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class='b right'>$temptot</td><td class='b'></td><td></td><td></td><td></td><td></td><td class='b right'>Total Hours </td><td class='b right'>$temptot</td></tr>";
	$timesheet .= "<tr><td class='right'>Signature</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class='center' colspan='7'>Appproval by: Department Manager or Supervisor</td></tr>";
	$timesheet .= "</table>";
	$timesheet .= "<a href='javascript:callme()'>PDF</a>
		<script type='text/javascript' src='js/jquery-2.1.3.js'></script>
		<script type='text/javascript' src='js/jspdf.js'></script></body></html>";
	
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
	echo $timesheet;
	
	
?>