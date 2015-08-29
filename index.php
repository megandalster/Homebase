<?php
/*
 * Copyright 2015 by Allen Tucker. This program is part of RMHP-Homebase, which is free 
 * software.  It comes with absolutely no warranty. You can redistribute and/or 
 * modify it under the terms of the GNU General Public License as published by the 
 * Free Software Foundation (see <http://www.gnu.org/licenses/ for more information).
 */
session_start();
session_cache_expire(30);
?>
<html>
    <head>
        <title>
            RMH Homebase
        </title>
        <link rel="stylesheet" href="styles.css" type="text/css" />
        <style>
        	#appLink:visited {
        		color: gray; 
        	}
        </style> 
    </head>
    <body>
        <div id="container">
            <?PHP include('header.php'); ?>
            <div id="content">
                <?PHP
                include_once('database/dbPersons.php');
                include_once('domain/Person.php');
                include_once('database/dbLog.php');
                include_once('domain/Shift.php');
                include_once('database/dbShifts.php');
                date_default_timezone_set('America/New_York');
            //    fix_all_birthdays();
                if ($_SESSION['_id'] != "guest") {
                    $person = retrieve_person($_SESSION['_id']);
                    echo "<p>Welcome, " . $person->get_first_name() . ", to Homebase!";
                }
                else 
                    echo "<p>Welcome!";
                echo "   Today is " . date('l F j, Y') . ".<p>";
                ?>

                <!-- your main page data goes here. This is the place to enter content -->
                <p>
                    <?PHP
                    if ($_SESSION['access_level'] == 0)
                        echo('<p> To apply for volunteering at the Portland or Bangor Ronald McDonald House, '.
                        		'please select <b>apply</b>.');
                    if ($person) {
                        /*
                         * Check type of person, and display home page based on that.
                         * all: password check
                         * guests: show link to application form
                         * applicants: show status of application form
                         * Volunteers, subs: show upcoming schedule and log sheet
                         * Managers: show upcoming vacancies, birthdays, anniversaries, applicants
                         */

                        //APPLICANT CHECK
                        if ($person->get_first_name() != 'guest' && $person->get_status() == 'applicant') {
                            //SHOW STATUS
                            echo('<div class="infobox"><p><strong>Your application has been submitted.</strong><br><br /><table><tr><td><strong>Step</strong></td><td><strong>Completed?</strong></td></tr><tr><td>Background Check</td><td>' . $person['background_check'] . '</td></tr><tr><td>Interview</td><td>' . $person['interview'] . '</td></tr><tr><td>Shadow</td><td>' . $person['shadow'] . '</td></tr></table></p></div>');
                        }

                        //VOLUNTEER CHECK
                        if ($_SESSION['access_level'] == 1) {
                        	
                        	// display upcoming schedule
                            $shifts = selectScheduled_dbShifts($person->get_id());

                            $scheduled_shifts = array();
                            foreach ($shifts as $shift) {
                                $shift_month = get_shift_month($shift);
                                $shift_day = get_shift_day($shift);
                                $shift_year = get_shift_year($shift);

                                $shift_time_s = get_shift_start($shift);
                                $shift_time_e = get_shift_end($shift);

                                $cur_month = date("m");
                                $cur_day = date("d");
                                $cur_year = date("y");

                                if ($shift_year > $cur_year)
                                    $upcoming_shifts[] = $shift;
                                else if ($shift_year == $cur_year) {
                                    if ($cur_month < $shift_month)
                                        $upcoming_shifts[] = $shift;
                                    else if ($shift_month == $cur_month) {
                                        if ($cur_day <= $shift_day) {
                                            $upcoming_shifts[] = $shift;
                                        }
                                    }
                                }
                            }
                            if ($upcoming_shifts) {
                                echo('<div class="scheduleBox"><p><strong>Your Upcoming Schedule:</strong><br /></p><ul>');
                                foreach ($upcoming_shifts as $tableId) {
                                    echo('<li type="circle">' . get_shift_name_from_id($tableId)) . '</li>';
                                }
                                echo('</ul><p>If you need to cancel an upcoming shift, please contact the <a href="mailto:jpowers@rmhprovidence.org">Volunteer Coordinator</a>.</p></div>');
                            }
                            
                            // link to personal log sheet
                            echo('<br><div class="scheduleBox"><p><strong>View/Update your Log Sheet:</strong><br /></p><ul>');
                                
                                echo('</ul><p>Go <strong><a href="volunteerLog.php?id='.$person->get_id()
                        	   .'">here</a></strong> to view or enter your recent volunteering hours.</p></div>');
              
                        }
                        
                        if ($_SESSION['access_level'] == 2) {
                            //We have a manager authenticated
                            
                        	//active applicants box
                        	connect();
                        	$app_query = "SELECT first_name,last_name,id,start_date FROM dbPersons WHERE status LIKE '%applicant%'  AND venue='".
                        			$_SESSION['venue']."'order by start_date desc";
                        	$applicants_tab = mysql_query($app_query);
                        	$numLines = 0;
                        	//   if (mysql_num_rows($applicants_tab) > 0) {
                        	echo('<div class="applicantsBox"><p><strong>Open Applications / Dates:</strong><ul>');
                        	while ($thisRow = mysql_fetch_array($applicants_tab, MYSQL_ASSOC)) {
                        		echo('<li type="circle"><a href="' . $path . 'personEdit.php?id=' . $thisRow['id'] .'" id = "appLink">' .
                        				$thisRow['last_name'] . ', ' . $thisRow['first_name'] . '</a> / '.
                        				$thisRow['start_date'] . '</li>');
                        	}
                        	echo('</ul></p></div><br>');
                        	//    }
                        	mysql_close();
                        	
                            //log box
                            echo('<div class="logBox"><p><strong>Recent Schedule Changes:</strong><br />');
                            echo('<table class="searchResults">');
                            echo('<tr><td class="searchResults"><u>Time</u></td><td class="searchResults"><u>Message</u></td></tr>');
                            $log = get_last_log_entries(5);
                            foreach ($log as $lo) {
                                echo('<tr><td class="searchResults">' . $lo[1] . '</td>' .
                                '<td class="searchResults">' . $lo[2] . '</td></tr>');
                            }
                            echo ('</table><br><a href="' . $path . 'log.php">View full log</a></p></div><br>');

                            //beginning of vacancy box
                            //For checking time
                            $today = mktime(0, 0, 0, date('m'), date('d'), date('y'));
                            $two_weeks = $today + 14 * 86400;

                            connect();
                            $vacancy_query = "SELECT id,vacancies FROM dbShifts " .
                                    "WHERE venue='".$_SESSION['venue']."' AND vacancies > 0 ORDER BY id;";
                            $vacancy_list = mysql_query($vacancy_query);
                            if (!$vacancy_list)
                                echo mysql_error();
                            //upcoming vacancies
                            if (mysql_num_rows($vacancy_list) > 0) {
                                echo('<div class="vacancyBox">');
                                echo('<p><strong>Upcoming Vacancies:</strong><ul>');
                                while ($thisRow = mysql_fetch_array($vacancy_list, MYSQL_ASSOC)) {
                                    $shift_date = mktime(0, 0, 0, substr($thisRow['id'], 0, 2), substr($thisRow['id'], 3, 2), substr($thisRow['id'], 6, 2));
                                    if ($shift_date > $today && $shift_date < $two_weeks) {
                                        echo('<li type="circle"><a href="' . $path . 'editShift.php?shift=' . $thisRow['id'] . '">' . get_shift_name_from_id($thisRow['id']) . '</a></li>');
                                    }
                                }
                                echo('</ul></p></div><br>');
                            }
                         
                        // active volunteers who haven't worked recently
                            $everyone = getall_names("active", "volunteer",$_SESSION['venue']);
                            if ($everyone && mysql_num_rows($everyone) > 0) {
                                //active volunteers who haven't worked for the last two months
                                $two_months_ago = $today - 60 * 86400;
                                echo('<div class="inactiveBox">');
                                echo('<p><strong>Unscheduled active house volunteers who haven\'t worked during the last two months:</strong>');
                                echo('<table class="searchResults"><tr><td class="searchResults"><u>Name</u></td><td class="searchResults"><u>Date Last Worked</u></td></tr>');
                                while ($thisRow = mysql_fetch_array($everyone, MYSQL_ASSOC)) {
                                    if (!preg_match("/manager/", $thisRow['type'])) {
                                        $shifts = selectScheduled_dbShifts($thisRow['id']);
                                        $havent_worked = true;
                                        $last_worked = "";
                                        for ($i = 0; $i < count($shifts) && $havent_worked; $i++) {
                                            $date_worked = mktime(0, 0, 0, get_shift_month($shifts[$i]), get_shift_day($shifts[$i]), get_shift_year($shifts[$i]));
                                            $last_worked = substr($shifts[$i], 0, 8);
                                            if ($date_worked > $two_months_ago) 
                                                $havent_worked = false;
                                        }
                                        if ($havent_worked)
                                            echo('<tr><td class="searchResults"><a href="personEdit.php?id=' . $thisRow['id'] . '">' . $thisRow['first_name'] . ' ' . $thisRow['last_name'] . '</a></td><td class="searchResults">' . $last_worked . '</td></tr>');
                                    }
                                }
                                echo('</table></p></div><br>');
                            } 
                        }
                        //DEFAULT PASSWORD CHECK
                        if (md5($person->get_id()) == $person->get_password()) {
                            if (!isset($_POST['_rp_submitted']))
                                echo('<p><div class="warning"><form method="post"><p><strong>We recommend that you change your password, which is currently default.</strong><table class="warningTable"><tr><td class="warningTable">Old Password:</td><td class="warningTable"><input type="password" name="_rp_old"></td></tr><tr><td class="warningTable">New password</td><td class="warningTable"><input type="password" name="_rp_newa"></td></tr><tr><td class="warningTable">New password<br />(confirm)</td><td class="warningTable"><input type="password" name="_rp_newb"></td></tr><tr><td colspan="2" align="right" class="warningTable"><input type="hidden" name="_rp_submitted" value="1"><input type="submit" value="Change Password"></td></tr></table></p></form></div>');
                            else {
                                //they've submitted
                                if (($_POST['_rp_newa'] != $_POST['_rp_newb']) || (!$_POST['_rp_newa']))
                                    echo('<div class="warning"><form method="post"><p>Error with new password. Ensure passwords match.</p><br /><table class="warningTable"><tr><td class="warningTable">Old Password:</td><td class="warningTable"><input type="password" name="_rp_old"></td></tr><tr><td class="warningTable">New password</td><td class="warningTable"><input type="password" name="_rp_newa"></td></tr><tr><td class="warningTable">New password<br />(confirm)</td><td class="warningTable"><input type="password" name="_rp_newb"></td></tr><tr><td colspan="2" align="center" class="warningTable"><input type="hidden" name="_rp_submitted" value="1"><input type="submit" value="Change Password"></form></td></tr></table></div>');
                                else if (md5($_POST['_rp_old']) != $person->get_password())
                                    echo('<div class="warning"><form method="post"><p>Error with old password.</p><br /><table class="warningTable"><tr><td class="warningTable">Old Password:</td><td class="warningTable"><input type="password" name="_rp_old"></td></tr><tr><td class="warningTable">New password</td><td class="warningTable"><input type="password" name="_rp_newa"></td></tr><tr><td class="warningTable">New password<br />(confirm)</td><td class="warningTable"><input type="password" name="_rp_newb"></td></tr><tr><td colspan="2" align="center" class="warningTable"><input type="hidden" name="_rp_submitted" value="1"><input type="submit" value="Change Password"></form></td></tr></table></div>');
                                else if ((md5($_POST['_rp_old']) == $person->get_password()) && ($_POST['_rp_newa'] == $_POST['_rp_newb'])) {
                                    $newPass = md5($_POST['_rp_newa']);
                                    change_password($person->get_id(), $newPass);
                                }
                            }
                            echo('<br clear="all">');
                        }
                    }
                    ?>
                    </div>
                    <?PHP include('footer.inc'); ?>
        </div>
    </body>
</html>