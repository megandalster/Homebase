<?php
/*
 * Copyright 2015 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook, 
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan, 
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker. 
 * This program is part of RMH Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

	session_start();
	session_cache_expire(30);
?>
<html>
	<head>
		<title>
			About
		</title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
	</head>
	<body>
		<div id="container">
			<?PHP include('header.php');?>
			<div id="content">
				<p><strong>Background</strong><br /><br />
				<i>Homebase</i> is a web-based volunteer management and scheduling system developed over the years by 
				college students and an instructor in collaboration with staff members at the Ronald McDonald Houses in 
				Portland, ME and Providence, RI. 
				
				<p>The original version of <i>Homebase</i></a> was developed in 2008 by <a href="http://www.bowdoin.edu/computer-science" target="_blank">
	            Bowdoin College</a> students and an instructor for the <a href="http://www.rmhportland.org/" target="_blank">Ronald McDonald House in 
	            Portland, Maine</a>.  It was later revised and enhanced in 2011, 2013, and 2015 by other groups of students and RMH staff members.  
	            More information about the original <i>Homebase</i> project can be found 
	            in <a href="http://www.bowdoin.edu/news/archives/1bowdoincampus/005118.shtml" target="_BLANK"> this article</a>.      
 
				<p>This project is supported by <a href="http://npfi.org" target="_blank">
				The Non-Profit FOSS Institute (NPFI)</a>, which "aims to build communities that develop and support customized 
				free and open source software (FOSS) applications that directly benefit the missions of humanitarian 
				non-profit organizations."  NPFI is inspired by the <a href="http://www.hfoss.org" target="_blank">Humanitarian 
				Free and Open Source (HFOSS) Project</a>, which has more global humanitarian goals.
				<p>
				
 				<p><b>System Access and Reuse</b><br /><br />
				Because <i>Homebase</i> must protect the privacy of individual RMH volunteers and staff, outside access to the system is
				restricted.  If you are an RMH staff member or volunteer and have forgotten your Username or Password, please contact the <a href="mailto:jpowers@rmhprovidence.org">Volunteer Coordinator</a>.
                </p>
				<p> <i>Homebase</i> is free and open source software (see <a href="http://code.google.com/p/rmhp-homebase/" target="_blank">http://code.google.com/p/rmhp-homebase/</a>).  
				From this site, its source code can be freely downloaded and adapted
				 to fit the volunteer scheduling needs of other non-profits.  
				 For more information about the capabilities or adaptability of <i>Homebase</i></i> to other settings, please 
				 contact either <a href="mailto:allen@bowdoin.edu">Allen Tucker</a> or visit the website 
				 <a href="http://npfi.org" target="_blank">http://npfi.org</a>.
				</p> 
				
			</div>
		<?PHP include('footer.inc');?>
		</div>
	</body>
</html>
