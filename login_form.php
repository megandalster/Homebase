<?php
/*
 * Copyright 2013 by Allen Tucker. 
 * This program is part of RMHC-Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 * Created on Mar 28, 2008
 * @author Oliver Radwan <oradwan@bowdoin.edu>, Sam Roberts, Allen Tucker
 * @version revised 3/31/2019
 */
?>

<div id="content">
    <?PHP
    include_once('database/dbPersons.php');
    include_once('domain/Person.php');
    if (($_SERVER['PHP_SELF']) == "/logout.php") {
        //prevents infinite loop of logging in to the page which logs you out...
        echo "<script type=\"text/javascript\">window.location = \"index.php\";</script>";
    }
    if (!array_key_exists('_submit_check', $_POST)) {
        echo('<div align="left"><p>Access to Homebase requires a Username and a Password. ' .
        '<ul>'
        );
        echo('<li>If you are a volunteer logging in for the first time, please change your password after logging in.  ');
        echo('<li>If you are having difficulty logging in or have forgotten your Password, please contact either the 
        		<a href="mailto:hmportland@rmhcmaine.org"><i>Portland House Manager</i></a>
        		or the <a href="mailto:hmbangor@rmhbangor.org"><i>Bangor House Manager</i></a>. ');
        echo '</ul>';
        echo('<p><table><form method="post"><input type="hidden" name="_submit_check" value="true"><tr><td>Username:</td>
        		<td><input type="text" name="user" tabindex="1"></td></tr>
        		<tr><td>Password:</td><td><input type="password" name="pass" tabindex="2"></td></tr>
                <tr><td colspan="2" align="center"><input type="submit" name="Login" value="Login"></td></tr></table>');
    } else {
            $db_pass = md5($_POST['pass']);
            $db_id = $_POST['user'];
            $person = retrieve_person($db_id);
            if ($person) { //avoids null results
                if ($person->get_password() == $db_pass) { //if the passwords match, login
                    $_SESSION['logged_in'] = 1;
                    date_default_timezone_set ("America/New_York");
                    if ($person->get_status() == "applicant")
                        $_SESSION['access_level'] = 0;
                    else if (in_array('manager', $person->get_type()))
                        $_SESSION['access_level'] = 2;
                    else
                        $_SESSION['access_level'] = 1;
                    $_SESSION['f_name'] = $person->get_first_name();
                    $_SESSION['l_name'] = $person->get_last_name();
                    $_SESSION['venue'] = $person->get_venue();
                    $_SESSION['type'] = $person->get_type();
                    $_SESSION['_id'] = $_POST['user'];
                    echo "<script type=\"text/javascript\">window.location = \"index.php\";</script>";
                }
                else {
                    //At this point, they failed to authenticate
                    echo('<div align="left"><p class="error">Error: invalid username/password<br />if you cannot remember your password, ask the
        		      <a href="mailto:hmportland@rmhcmaine.org"><i>Portland House Manager</i></a>
        		      or the <a href="mailto:hmbangor@rmhbangor.org"><i>Bangor House Manager</i></a> to reset it for you.</p>');
                    echo('<p><table><form method="post"><input type="hidden" name="_submit_check" value="true"><tr><td>Username:</td>
                        <td><input type="text" name="user" tabindex="1"></td></tr><tr><td>Password:</td>
                        <td><input type="password" name="pass" tabindex="2"></td></tr><tr><td colspan="2" align="center"><input type="submit" name="Login" value="Login"></td>
                        </tr></table></div>');
                }
            } else {
                //At this point, they failed to authenticate
                echo('<div align="left"><p class="error">Error: invalid username/password<br />if you cannot remember your password, ask the 
        		<a href="mailto:hmportland@rmhcmaine.org"><i>Portland House Manager</i></a>
        		or the <a href="mailto:hmbangor@rmhbangor.org"><i>Bangor House Manager</i></a> to reset it for you.</p>');
                echo('<p><table><form method="post"><input type="hidden" name="_submit_check" value="true"><tr><td>Username:</td>
                    <td><input type="text" name="user" tabindex="1"></td></tr><tr><td>Password:</td>
                    <td><input type="password" name="pass" tabindex="2"></td></tr><tr><td colspan="2" align="center"><input type="submit" name="Login" value="Login"></td>
                    </tr></table></div>');
            }
    }
    ?>
</div>
</body>
</html>
