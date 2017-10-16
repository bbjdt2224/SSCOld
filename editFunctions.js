//inserts time into table
function insertTime(abv, date, row){
	$("#"+abv.replace(/\s+/g, '')+date.replace(/\s+/g, '')+"t").text($("#"+abv.replace(/\s+/g, '')+date.replace(/\s+/g, '')+"m").find(":selected").text());
	calHours(date, row);
	updateTotal(row, date);
}

//insert reason of absence into table
function insertReason(date, row){
	$("#reason"+date.replace(/\s+/g, '')+"t").text($("#reason"+date.replace(/\s+/g, '')+"m").val());
	
	updateReason(row, "reason", date);
}

//adds all the hours together and displays it in the table
function calHours(date, row){
	var mb = document.getElementById("mb"+date.replace(/\s+/g, '')+"t").innerHTML.split(':');
	var me = document.getElementById("me"+date.replace(/\s+/g, '')+"t").innerHTML.split(':');
	var ab = document.getElementById("ab"+date.replace(/\s+/g, '')+"t").innerHTML.split(':');
	var ae = document.getElementById("ae"+date.replace(/\s+/g, '')+"t").innerHTML.split(':');
	var eb = document.getElementById("eb"+date.replace(/\s+/g, '')+"t").innerHTML.split(':');
	var ee = document.getElementById("ee"+date.replace(/\s+/g, '')+"t").innerHTML.split(':');
	
	if(ab[0] == 12){
		ab[0] = 0;
	}
	
	if(ee[0] == 1){
		ee[0] = 13;
	}
	
	var m = me[0]-mb[0];
	m = m || 0;
	var a = ae[0]-ab[0];
	a = a || 0;
	var e = ee[0]-eb[0];
	e = e || 0;
	var tot = m+a+e;

	document.getElementById("tot"+date.replace(/\s+/g, '')).innerHTML = tot;
	if(row < 7){
		week1[Number(row)] = tot;
	}
	else{
		week2[Number(row)-7] = tot;
	}
	calTotal();
}

//calculates the total hours worked
function calTotal(){
	var w1 = 0;
	var w2 = 0;
	for(var i = 0; i < 7; i ++){
		w1 += Number(week1[i]);
	}
	for(var i = 0; i < 7; i ++){
		w2 += Number(week2[i]);
	}
	document.getElementById("week1total").innerHTML = w1;
	document.getElementById("week2total").innerHTML = w2;
	document.getElementById("total").innerHTML = (w1+w2);
}

//inserts values into the database
function updateDB(row, col, abv, date){
	var data = document.getElementById(abv.replace(/\s+/g, '')+date.replace(/\s+/g, '')+"m").options[document.getElementById(abv.replace(/\s+/g, '')+date.replace(/\s+/g, '')+"m").selectedIndex].text;
	col = col.replace(/\s+/g, '').toLowerCase();
	row = row.replace(/\s+/g, '');
	row = Number(row)+1;
	var xmlhttp = new XMLHttpRequest();
		var response = "";
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				response = this.responseText;
				console.log(response);
			}
		}
		xmlhttp.open("GET", "updateDB.php?r="+row+"&c="+col+"&d="+data, true);
		xmlhttp.send();
		return;
}

//updates the total number of hours in the database
function updateTotal(row, date){
	var data = document.getElementById("tot"+date.replace(/\s+/g, '')).innerHTML;
	row = row.replace(/\s+/g, '');
	row = Number(row)+1;
	var xmlhttp = new XMLHttpRequest();
		var response = "";
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				response = this.responseText;
				console.log(response);
			}
		}
		xmlhttp.open("GET", "updateDB.php?r="+row+"&c=total&d="+data, true);
		xmlhttp.send();
		return;
}

//update the reason of absence in the database
function updateReason(row, col, date){
	var data = document.getElementById("reason"+date.replace(/\s+/g, '')+"m").value;
	var xmlhttp = new XMLHttpRequest();
	//row = row.replace(/\s+/g, '');
	row = Number(row)+1;
		var response = "";
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				response = this.responseText;
				console.log(response);
			}
		}
		xmlhttp.open("GET", "updateDB.php?r="+row+"&c="+col+"&d="+data, true);
		xmlhttp.send();
		return;
}

//changes the dates in the table
function changeDate(){
	var data = document.getElementById("date").value;
	console.log(data);
	var xmlhttp = new XMLHttpRequest();
		var response = "";
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				response = this.responseText;
				console.log(response);
			}
		}
		xmlhttp.open("GET", "updateDB.php?r=1 &c=other &d="+data, true);
		xmlhttp.send();
		return;
}

calTotal();