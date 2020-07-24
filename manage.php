<?php
    require_once 'head.php';
    require_once "../../../req/connect.php";
    require_once "../helpers.php";

    $out = '';

    if (isset($_POST['uName'])) {
        $submitted = TRUE;
        $uName = $_POST['uName'];
        $uName = strtolower($uName);
        $pass1 = $_POST['pass1'];

        if ($uName == '' OR $pass1 == '') {
            $out = "Please fill out all the fields.";
        }
        else {
            $sql = "SELECT * FROM finalUser WHERE uName= ?";
            $result = $pdo->prepare($sql);
            $result->execute(array($uName));
            $user = $result->fetch();

            if (!(password_verify($pass1, $user['pass']))) {
                $out = "Invalid credentials. Please try again.";
            }
            else {
                if ($user['access'] != 'admin') {
                    $out = "User is not an Admin. Please contact your Site Administrator.";
                }
                else {
                    $_SESSION['uName'] = $uName;
                    $out = "Welcome {$uName}!<br>";
                } // if not admin
            } // if password match
        } // else
    } // isset

    // get access to see if admin
    if (isset($_SESSION['uName'])) {
        $sessuName = $_SESSION['uName'];
        $sql = "SELECT * FROM finalUser WHERE uName= ?";
        $result = $pdo->prepare($sql);
        $result->execute(array($sessuName));
        $user = $result->fetch();
        $access = $user['access'];
    } // isset IS ADMIN?

    require_once 'nav.php';
?>

<main>
    <a href="index.php">
        <img id="appLogo" src="images/logo_transparent.png" alt="Mode Logo">
    </a>

    <?php
        if (isset($_SESSION['uName'])) {
            echo "<h3 class='welcomeMsg'>Welcome {$_SESSION['uName']} | <a href='logout.php'>Logout</a> | <a href='user.php?uName={$_SESSION['uName']}'>Go to Dashboard</a></h3><br>";
        }
        else {
            echo "<h3 class='welcomeMsg'><a href='index.php'>Create a Profile &nbsp;</a> | <a href='login.php'>&nbsp; Already a User? Login</a></h3>";
        }

        if (isset($_SESSION['uName']) && $access == 'admin') {
            echo "<div id='manage'>";
            echo "<h2>Administrator Console</h2>";

            $waterArr = [];
            $sleepArr = [];
            $fitArr = [];

            // LOG ADMIN
            echo "<br><h2><a href='viewLogs.php'>View All User Logs</a></h2>";
            // END LOG ADMIN

            // USER ADMIN
                $sql = "SELECT * FROM finalUser ORDER BY access ASC";
                $result = $pdo->prepare($sql);
                $result->execute();
                $userCount = 0;
                
                $userAdmin = "<table class='admin'>";
                $userAdmin .= "<tr><th colspan='4' class='tabTitle'>Manage Users</th></tr>";
                $userAdmin .= "<tr>
                    <th>User</th>
                    <th>Level</th>
                    <th>Email</th>
                    <th>Change Password</th>
                </tr>";
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $uName = $row['uName'];
                    $acc = $row['access'];
                    $email = $row['email'];
                    $ID = $row['id'];

                    $userAdmin .= "<tr>
                        <td>$uName</td>
                        <td>$acc</td>
                        <td>$email</td>
                        <td>
                            <form action='manage.php' method='POST' class='passForm'>
                                <input type='password' class='update' placeholder='new password' name='newPass' id='newPass'>
                                <input type='hidden' name='ID' value='$ID'>
                                <input type='submit' value='update' name='changePass' id='changePass'>
                            </form>
                        </td>
                    </tr>";

                    $userCount += 1;
                }
                $userAdmin .= "<tr><td colspan='4' class='total'>Total Users: $userCount</td></tr>";
                $userAdmin .= "</table>";
                echo $userAdmin;
                $outPass = '';
                $sub_try = FALSE;

                // change password
                if (isset($_POST['newPass'])) {
                    $newPass = $_POST['newPass'];
                    $ID = $_POST['ID'];

                    // ANY BLANK FIELDS?
                    if ($newPass == '') {
                        $outPass = "Please enter a Password.";
                        $submitted = FALSE;
                        $sub_try = TRUE;
                    }
                    else {
                        // MIN PASS LENGTH
                        if (strlen($newPass) < 4) {
                            $outPass = "Password must be at least 4 characters long.";
                            $submitted = FALSE;
                            $sub_try = TRUE;
                        }
                        else {
                            //https://gist.github.com/Michael-Brooks/fbbba105cd816ef5c016
                            $passEx = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';
                            
                            if (!preg_match($passEx,$newPass)) {
                                $outPass = "Password must be mixed-case with a number, 4 digit minimum.";
                                $submitted = FALSE;
                                $sub_try = TRUE;
                            }
                            else {
                                // SALT & HASH PASSWORD
                                $pass = password_hash($newPass, PASSWORD_DEFAULT);

                                $outPass = "try/catch";
                                // BIND PARAMS, THEN EXECUTE
                                try {
                                    $sql = "UPDATE finalUser
                                    SET pass = ?
                                    WHERE ID = ?";
                                    $result = $pdo->prepare($sql);          
                                    $result->execute(array($pass, $ID));
                                    $outPass = "Password Updated";
                                    $submitted = TRUE;
                                    $sub_try = TRUE;
                                }
                                catch (PDOException $e) {
                                    $outPass = $e->getMessage();
                                    $submitted = FALSE;
                                    $sub_try = TRUE;
                                } //catch
                            } // if mixed-case
                        } // if pass length
                    } // if blank fields
                } // isset NEWPASS

                if ($sub_try == TRUE) {
                    echo "<br><h2>$outPass</h2>";
                }
            // END USER ADMIN

            // GOALS ADMIN
                $sql = "SELECT * FROM userGoals ORDER BY userID ASC";
                $result = $pdo->prepare($sql);
                $result->execute();

                $goalAdmin = "<table class='admin'>";
                $goalAdmin .= "<tr><th colspan='5' class='tabTitle'>View Goals</th></tr>";
                $goalAdmin .= "<tr>
                    <th>User</th>
                    <th>Habit</th>
                    <th>Water (per 8oz)</th>
                    <th>Sleep Hrs</th>
                    <th>Fitness Hrs</th>
                </tr>";
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $uID = $row['userID'];
                    $habit = $row['habit'];
                    $water = $row['waterOz'];
                    $sleep = $row['sleepHrs'];
                    $fitness = $row['fitnessHrs'];

                    $goalAdmin .= "<tr>
                        <td>$uID</td>
                        <td>$habit</td>
                        <td>$water</td>
                        <td>$sleep</td>
                        <td>$fitness</td>
                    </tr>";

                    $waterArr[] = $water;
                    $sleepArr[] = $sleep;
                    $fitArr[] = $fitness;
                }
                // find average
                $watAvg = array_sum($waterArr) / count($waterArr);
                $sleAvg = array_sum($sleepArr) / count($sleepArr);
                $fitAvg = array_sum($fitArr) / count($fitArr);
                // round to 2 dec
                $watAvgRnd = round($watAvg,2);
                $sleAvgRnd = round($sleAvg,2);
                $fitAvgRnd = round($fitAvg,2);

                $goalAdmin .= "<tr>
                    <td colspan='2' class='total'>Average Goals:</td>
                    <td>$watAvgRnd</td>
                    <td>$sleAvgRnd</td>
                    <td>$fitAvgRnd</td>
                </tr>";
                $goalAdmin .= "</table>";
                echo $goalAdmin;
            // END GOALS ADMIN

            echo "</div>"; // end of echo div id=manage
        }
        else {
    ?>
            <div id='logIn'>
                <form id='loginForm' method='POST' action='<?php echo $_SERVER['PHP_SELF'] ?>'>
                    <h1>Dashboard</h1>
                    <div class='formDiv'>
                        <label for='uName'>User Name</label>
                        <input type='text' name='uName' id='uName'>
                    </div>
                    <div class='formDiv'>
                        <label for='pass1'>Password</label>
                        <input type='password' name='pass1' id='pass1'>
                    </div>

                    <?php
                        echo "<h2 style='color:#00b0c0;'>".$out."</h2>";
                    ?>

                    <div class='buttonDiv'>
                        <input type='submit' value='Log In' id='subButton'>
                    </div>
                </form>
            </div> <!-- end of logIn -->
    <?php
        }   // end of show form else
    ?>

</main>

<?php
    require_once 'foot.php';
?>