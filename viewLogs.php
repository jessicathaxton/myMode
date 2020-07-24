<?php
    require_once 'head.php';
    require_once "../../../req/connect.php";

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

    if (isset($_SESSION['uName'])) {
        $sessuName = $_SESSION['uName'];
        $sql = "SELECT * FROM finalUser WHERE uName= ?";
        $result = $pdo->prepare($sql);
        $result->execute(array($sessuName));
        $user = $result->fetch();
        $access = $user['access'];
    }

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
            echo "<div id='page'>";
            echo "<h2>Administrator Console</h2>";
            echo "<br><p><a href='manage.php'>Back to Main Admin Page</a></p>";

            $waterArr = [];
            $sleepArr = [];
            $fitArr = [];

            // GET TOTAL AVERAGES
                $sql = "SELECT * FROM logProgress";
                $result = $pdo->prepare($sql);
                $result->execute();
                $logTotal = $result->rowCount(); 

                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $water1 = $row['waterOz'];
                    $water1 = $water1*8;        //*8oz per glass
                    $sleep1 = $row['sleepHrs'];
                    $fitness1 = $row['fitnessHrs'];

                    $waterArr[] = $water1;
                    $sleepArr[] = $sleep1;
                    $fitArr[] = $fitness1;
                }

                // find average
                $watAvg = array_sum($waterArr) / count($waterArr);
                //echo "wat sum: ".array_sum($waterArr);
                //echo "wat count".count($waterArr);                // VERIFIED, MATH IS CORRECT VIA MANUAL COMPARISON
                $sleAvg = array_sum($sleepArr) / count($sleepArr);
                $fitAvg = array_sum($fitArr) / count($fitArr);
                // round to 2 dec
                $watAvgRnd = round($watAvg,2);
                $sleAvgRnd = round($sleAvg,2);
                $fitAvgRnd = round($fitAvg,2);


            // LOG ADMIN
                $page = 0;
                if (!(empty($_GET['page']))) {
                    $page = $_GET['page'];
                }
            
                $limit = 10;
                $offset = $page * $limit;

                $sql = "SELECT * FROM logProgress ORDER BY logDate DESC LIMIT $limit OFFSET $offset";
                $result = $pdo->prepare($sql);
                $result->execute();
                //$logTotal = $result->rowCount();              

                $logAdmin = "<table class='admin'>";
                $logAdmin .= "<tr><th colspan='6' class='tabTitle'>View User Logs</th></tr>";
                $logAdmin .= "<tr>
                    <th>User</th>
                    <th>Log Date</th>
                    <th>Habit Y/N?</th>
                    <th>Water (x 8oz)</th>
                    <th>Sleep Hrs</th>
                    <th>Fitness Hrs</th>
                </tr>";
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $uID = $row['userID'];
                    $date = $row['logDate'];
                    $habit = $row['habitYN'];
                    $water = $row['waterOz'];
                    $water = $water*8;      //*8oz per glass
                    $sleep = $row['sleepHrs'];
                    $fitness = $row['fitnessHrs'];

                    $logAdmin .= "<tr>
                        <td>$uID</td>
                        <td>$date</td>
                        <td>$habit</td>
                        <td>$water</td>
                        <td>$sleep</td>
                        <td>$fitness</td>
                    </tr>";
                }

                $logAdmin .= "<tr>
                    <td colspan='3' class='total'>Average Daily Progress:</td>
                    <td>$watAvgRnd oz</td>
                    <td>$sleAvgRnd hrs</td>
                    <td>$fitAvgRnd hrs</td>
                </tr>";
                $logAdmin .= "</table>";
                echo $logAdmin;
                
                $next = $page+1;
                $prev = $page-1;

                if ($page != 0) {
                    echo"<a href='viewLogs.php?page=$prev'>&#129144; Previous </a> |";
                }
                if ($logTotal-$offset > $limit) {
                    echo "| <a href='viewLogs.php?page=$next'> Next &#129154;</a>";
                }

            // END LOG ADMIN

            echo "</div>"; // end of echo div id=page
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
                        echo "<h2 style='color:#a13301;'>".$out."</h2>";
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