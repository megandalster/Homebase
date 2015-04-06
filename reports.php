<?php
/*
 * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook, 
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan, 
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker. 
 * This program is part of RMH Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

/*
 * reports page for RMH homebase.
 * @author Jerrick Hoang
 * @version 11/5/2013
 */
session_start();
session_cache_expire(30);

include_once('header.php'); 
include_once('database/dbPersons.php');
include_once('domain/Person.php');
include_once('database/dbShifts.php');
include_once('domain/Shift.php');
?>

<html>
<head>
<title>Search for data objects</title>	
<link rel="stylesheet" href="styles.css" type="text/css" />
<link rel="stylesheet" href="lib/jquery-ui.css" />
<script src="lib/jquery-ui.js"></script>
<script>
$(function() {
	$( "#from" ).datepicker({dateFormat: 'mm-dd-y',changeMonth:true,changeYear:true});
	$( "#to" ).datepicker({dateFormat: 'mm-dd-y',changeMonth:true,changeYear:true});

	$(document).on("keyup", ".volunteer-name", function() {
		var str = $(this).val();
		var target = $(this);
		$.ajax({
			type: 'get',
			url: 'reportsCompute.php?q='+str,
			success: function (response) {
				var suggestions = $.parseJSON(response);
				console.log(target);
				target.autocomplete({
					source: suggestions
				});
			}
		});
	});

	$("input[name='date']").change(function() {
		if ($("input[name='date']:checked").val() == 'date-range') {
			$("#fromto").show();
		} else {
			$("#fromto").hide();
		}
	});

	$("#report-submit").on('click', function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: 'reportsCompute.php',
			data: $('#search-fields').serialize(),
			success: function (response) {
				$("#outputs").html(response);
			}
		});
	} );
	
});
</script>
</head>
<body>
<div id="container">

<div id = "content">
<div>
	<p id="search-fields-container">
	<form id = "search-fields" method="post">
		<input type="hidden" name="_form_submit" value="report" />
		<p class = "search-description" id="today"> <b>RMH Providence Volunteer Reports</b><br> Report date: 
		<?php date_default_timezone_set ("America/New_York"); echo Date("F d, Y");?></p>
	<table>	<tr>
		<td class = "search-description" valign="top"> &nbsp;&nbsp;&nbsp;&nbsp;Select Report Type: 
		<p>	<select multiple name="report-types[]" id = "report-type" size="5">
	  		<option value="volunteer-hours">Total Hours</option>
	  		<option value="shifts-staffed-vacant">Shifts/Vacancies</option>
	  		<option value="birthdays">Birthdays</option>
	  		<option value="history">Volunteer History</option>
			</select>
		</td>
		<td class = "search-description" valign="top">&nbsp;&nbsp; Date Range: 
			<p id="fromto"> from : <input name = "from" type="text" id="from"><br>
							&nbsp;&nbsp;&nbsp;&nbsp;to : <input name = "to" type="text" id="to"></p>
		</td>
		<td class = "search-description" valign="top"> &nbsp;&nbsp;&nbsp;&nbsp;Venue:
		    <p id="venue-input"> <select name="venue" id = "report-venue">
	  		<option value="">--any--</option>
	  		<option value="house">House</option>
	  		<option value="fam">Family Room</option></select></p>
		</td>
		<td valign="top">
	To view the report <br><br>Hit <input type="submit" value="submit" id ="report-submit" class ="btn">
	</td><td valign="top">
	<p>To save the report <p>Hit <input type="submit" value="PDF" id ="report-pdf" class ="btn">
						 or <input type="submit" value="CSV" id ="report-csv" class ="btn">
	</td>
	</tr>
	</table>
	</form>
	<p id="outputs">

	</p>
</div>
</div>
</div>

</body>