<?php
    // if SESSION UNAME EMPTY redirect away
    $uName = $_GET['uName'];
    if ($uName == '') {
        header("refresh:0; url=index.php");
    }

    require_once 'head.php';
    require_once "../../../req/connect.php";
    require_once "boredAPI.php";

    //error_reporting(E_ALL);
    //ini_set("display_errors","1");

    $submitted = FALSE;
    $sub_try = FALSE;
    $out = '';
    $outLog = '';

    $sql = "CREATE TABLE IF NOT EXISTS userGoals (
        goalID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        userID INT,
        habit VARCHAR(256),
        waterOz INT,
        sleepHrs INT,
        fitnessHrs INT,
        FOREIGN KEY (userID)
            REFERENCES finalUser(id)
    )";

    $result = $pdo->query($sql);

 // GET TODAY'S DATE
    $today = getdate();
    /*echo "<pre>";
    print_r($today);
    echo "</pre>";*/
    $d = $today['mday'];
    $m = $today['mon'];
    $y = $today['year'];

    if (strlen($m) < 2) {
        $m = '0'.$today['mon'];
    }   // FIX M FOR SQL
    if (strlen($d) < 2) {
        $d = '0'.$today['mday'];
    }   // FIX D FOR SQL
    $todaySQL = "{$y}-{$m}-{$d}";

    // TO CREATE OLD LOGS, CHANGE DATE MANUALLY
    //$todaySQL = "2020-05-27";

 // get the userID
    $sql = "SELECT id FROM finalUser WHERE uName = ?";
    $result = $pdo->prepare($sql);
    $result->execute(array($uName));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $userID = $row['id'];
        //echo "userID assigned: {$userID}";
    } // get user ID
    

 // CREATING HABITS -----------------------------------------------------
    if (isset($_POST['habit'])) {
        $habit = $_POST['habit'];
        $water = $_POST['water'];
        $sleep = $_POST['sleep'];
        $fitness = $_POST['fitness'];
        $sub_try = TRUE;
        //echo "{$habit}, <br> {$water} oz, <br> {$sleep} hrs nightly, <br> {$fitness} hrs weekly";

        // ANY BLANK FIELDS
        if ($habit == '' OR $water == 0 OR $sleep == 0 OR $fitness == 0) {
            $out = "Complete all fields";
            $submitted = FALSE;
        }
        else {
            $out = "All fields complete";
            $length = strlen($habit);
            if ($length > 256) {
                $length = strlen($habit);
                $out = "Habit too long, 256 max: length is {$length}";
                $submitted = FALSE;
            }
            else {
                $out = "Habit length okay: length is {$length} ";

                $goalsExist = FALSE;
                $sql = "SELECT * FROM userGoals WHERE userID = ?";
                $result = $pdo->prepare($sql);
                $result->execute(array($userID));
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $setWater = $row['waterOz'];
                    if ($setWater > 0) {
                        $goalsExist = TRUE;
                    }
                    else {
                        $goalsExist = FALSE;
                    }
                } // get habits
                // do goals exist already?
                if ($goalsExist) {
                    //echo "goals exist; insert stopped.";
                    $submitted = FALSE;
                }
                else {
                    //echo "no goals found; trying insert.";
                    try {
                    $sql = "INSERT INTO userGoals (userID, habit, waterOz, sleepHrs, fitnessHrs) VALUES (?, ?, ?, ?, ?)";
                        $result = $pdo->prepare($sql);
                        $result->execute(array($userID, $habit, $water, $sleep, $fitness));
                        $out = "New goals created!";
                        $submitted = TRUE;
                    }
                    catch (PDOException $e) {
                        $out = $e->getMessage();
                        $submitted = FALSE;
                    } //catch 
                } // do goals exist?
            } // habit length <= 256
        } // blank fields
    } // isset CREATE HABITS

 // GET HABITS -------------------------------------------------------
    $setHabit = '';
    $setWater = '';
    $setSleep = '';
    $setFitness = '';

    $sql = "SELECT * FROM userGoals WHERE userID = ?";
    $result = $pdo->prepare($sql);
    $result->execute(array($userID));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $setHabit = $row['habit'];
        $setWater = $row['waterOz'];
        $setSleep = $row['sleepHrs'];
        $setFitness = $row['fitnessHrs'];
    } // get habits

 // GET EXISTING LOGS -------------------------------------------------------
    // logDate MUST BE yyyy-mm-dd format
    $sql = "CREATE TABLE IF NOT EXISTS logProgress (
    logID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT,
    logDate DATE,       
    habitYN VARCHAR(10),
    waterOz INT,
    sleepHrs INT,
    fitnessHrs INT,
    FOREIGN KEY (userID)
        REFERENCES finalUser(id)
    )";
    $result = $pdo->query($sql);

    $todayWat = '';
    $todaySle = '';
    $todayExc = '';
    $logsExist = FALSE;
    $logsToday = FALSE;
    $setLogWat = '';

    $sql = "SELECT * FROM logProgress WHERE userID = ? ORDER BY logDate ASC";
    $result = $pdo->prepare($sql);
    $result->execute(array($userID));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $setLogDate = $row['logDate'];

        if ($setLogDate === $todaySQL) {
            $todayWat = $row['waterOz'];
            $todaySle = $row['sleepHrs'];
            $todayExc = $row['fitnessHrs'];
            $logsExist = TRUE;
            $logsToday = TRUE;
        }

        if ($setLogDate > 0) {
            $logsExist = TRUE;
        }
        else {
            $logsExist = FALSE;
        }

    } // get habits if logged today

 // LOG PROGRESS -----------------------------------------------------
    $logWat = '';
    $logSle = '';
    $logExc = '';
    $subLog = FALSE;

    if (isset($_POST['logWat'])) {
        $logWat = $_POST['logWat'];
        $logSle = $_POST['logSle'];
        $logExc = $_POST['logExc'];

        if ((empty($_POST['habitQues'])) OR $logWat == '' OR $logSle == '' OR $logExc == '') {
            $outLog = "Complete all fields.<br> You can update your answers later.";
        }
        else {
            $habQues = $_POST['habitQues'];
            if (!(($logWat >= 0) && (filter_var($logWat, FILTER_VALIDATE_INT) || filter_var($logWat, FILTER_VALIDATE_INT) === 0)
                && ($logSle >= 0) && (filter_var($logSle, FILTER_VALIDATE_INT) || filter_var($logSle, FILTER_VALIDATE_INT) === 0)
                && ($logExc >= 0) &&(filter_var($logExc, FILTER_VALIDATE_INT) || filter_var($logExc, FILTER_VALIDATE_INT) === 0))) {
                $outLog =  "Please type a positive integer, <br>your total for today";
            }
            else {
                if ($logsToday) {
                    if ($setLogDate === $todaySQL) {
                        try {
                            $sql = "UPDATE logProgress
                                SET habitYN = ?,
                                waterOz = ?,
                                sleepHrs = ?,
                                fitnessHrs = ?
                                WHERE userID = ? AND logDate = ?
                            ";
                            $result = $pdo->prepare($sql);
                            $result->execute(array($habQues, $logWat, $logSle, $logExc, $userID, $todaySQL));
                            $outLog = "Progress Updated!";
                            $subLog = TRUE;
                        }
                        catch (PDOException $e) {
                            $outLog = $e->getMessage();
                            $subLog = FALSE;
                        } //catch
                    }
                    else {
                        $out = "Oops! An error occurred. Please try again later.";
                    } // IF LOG IS TODAY'S DATE
                }
                else {
                    try {
                        $sql = "INSERT INTO logProgress (userID, logDate, habitYN, waterOz, sleepHrs, fitnessHrs) VALUES (?, ?, ?, ?, ?, ?)";
                        $result = $pdo->prepare($sql);
                        $result->execute(array($userID, $todaySQL, $habQues, $logWat, $logSle, $logExc));
                        $outLog = "Progress Logged!";
                        $subLog = TRUE;
                    }
                    catch (PDOException $e) {
                        $outLog = $e->getMessage();
                        $subLog = FALSE;
                    } //catch 
                } // are there logs today or not?
            } // validate form is correct
        } // if any field empty
    } // isset logForm

    require_once 'nav.php';
?>




<main id="userMain">
    <a href="index.php">    
        <img id="appLogo" src="images/logo_transparent.png" alt="Mode Logo">
    </a>

    <div id='inspDiv'>
        <?php
        echo "<h2>{$_SESSION['uName']}'s Dashboard | <a href='logout.php'>Logout</a></h2><br>";
        ?>
        <h2>Need ideas on things to do? We've got you covered!</h2>
        <h2><?php echo $activity ?>.</h2><!-- pulled from https://www.boredapi.com/ -->
    </div>


    <div id='userDash'>
        <div id='userGoals'>

            <?php
                // show habits if set
                if ($setHabit != '') {
                    $goals = "<h2>Your Goals</h2>";
                    $goals .= "<table id='goalTable'>";
                    $goals .= "<tr>
                        <th>New Habit:</th>
                        <td>{$setHabit}</td>
                    </tr>";
                    $goals .= "<tr>
                        <th>Daily Water Goal:</th>
                        <td>Drink {$setWater} 8oz glasses of water daily.</td>
                    </tr>";
                    $goals .= "<tr>
                        <th>Nightly Rest Goal:</th>
                        <td>Sleep {$setSleep} hours nightly.</td>
                    </tr>";
                    $goals .= "<tr>
                        <th>Weekly Fitness Goal:</th>
                        <td>Exercise {$setFitness} total hours weekly.</td>
                    </tr>";
                    $goals .= "</table>";     
                    
                    echo $goals;
                    // EDIT GOALS
                    //echo "<input type='submit' value='Edit Goals' id='editButton'>";
                } // if goals are set or $submitted = TRUE
                else {
            ?>

            <form id='goalForm' method='POST' action=<?php echo "user.php?uName={$_SESSION['uName']}" ?>>
                <h2>Create Your Goals</h2>

                <div class='formDiv'>
                    <label for='habit'>Create a New Habit to Reach!</label>
                    <input type='text' list='habits' name='habit' id='habit' placeholder='Click for Ideas'>
                    <datalist id='habits'>
                        <option>Exercise 3 hours per week.</option>
                        <option>Create a new meal-prep recipe every week.</option>
                        <option>Try 2 new types of exercise each week.</option>
                        <option>Drink no more than 1 soda per week.</option>
                        <option>Purge unused items and keep from accumulating more stuff!</option>
                    </datalist>
                </div>

                <div class='formDiv'>
                    <label for='water'>Daily Water Goal</label>
                    <!-- Tried 'datalist', but not fully supported in Firefox -->
                    <select name='water' id='water'>
                        <option value=0>Drink more water!</option>
                        <option value=2 style='color:red;'>2 - 8oz glasses</option>
                        <option value=4 style='color:darkorange;'>4 - 8oz glasses</option>
                        <option value=6 style='color:yellowgreen;'>6 - 8oz glasses</option>
                        <option value=8 style='font-weight:bold;color:green;'>8 - 8oz glasses (recommended)</option>
                        <option value=10 style='color:green;'>10 - 8oz glasses</option>
                        <option value=12 style='color:green;'>12 - 8oz glasses</option>
                        <option value=14 style='color:green;'>14 - 8oz glasses</option>
                        <option value=16 style='color:green;'>16 - 8oz glasses</option>
                    </select>
                </div>

                <div class='formDiv'>
                    <label for='sleep'>Nightly Rest Goal</label>
                    <select name='sleep' id='sleep'>
                        <option value=0>Get a good night's rest.</option>
                        <option value=2 style='color:red;'>2 hours</option>
                        <option value=4 style='color:darkorange;'>4 hours</option>
                        <option value=6 style='color:yellowgreen;'>6 hours</option>
                        <option value=8 style='font-weight:bold;color:green;'>8 hours (recommended)</option>
                        <option value=10 style='color:yellowgreen;'>10 hours</option>
                        <option value=12 style='color:darkorange;'>12 hours</option>
                    </select>
                </div>

                <div class='formDiv'>
                    <label for='fitness'>Weekly Fitness Goal</label>
                    <select name='fitness' id='fitness'>
                        <option value=0>You can do this!</option>
                        <option value=1>1 hour</option>
                        <option value=2>2 hours</option>
                        <option value=3>3 hours</option>
                        <option value=4>4 hours</option>
                        <option value=5>5 hours</option>
                        <option value=6>6 hours</option>
                        <option value=7>7 hours</option>
                        <option value=8>8 hours</option>
                        <option value=9>9 hours</option>
                        <option value=10>10 hours</option>
                        <option value=11>11 hours</option>
                        <option value=12>12 hours</option>
                    </select>
                </div>

                <?php
                    if ($sub_try) {
                        echo "<h2 style='color:rgb(255, 69, 69);'>".$out."</h2>";
                    } 
                ?>

                <div class='buttonDiv'>
                    <input type='submit' value='Create Goals' id='subButton'>
                </div>
            </form>

            <?php
                } // if habits exist, show them; else, show form
            ?>


            <div id='history'>
                <h2>Track Your Progress</h2>
                <?php
                    // TRACK PROGRESS -----------------------------------------------------
                    // get user progress
                    if ($logsExist) {
                        $sql = "SELECT * FROM logProgress WHERE userID = ?";
                        $result = $pdo->prepare($sql);
                        $result->execute(array($userID));

                        $track = "<table id='trackTable'>";
                        $track .= "<tr>
                            <th>Date</th>
                            <th>New Habit Effort</th>
                            <th>Water (per 8oz glass)</th>
                            <th>Sleep</th>
                            <th>Exercise</th>
                        </tr>";
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $rdate = $row['logDate'];
                            $rhabit = $row['habitYN'];
                            $rwater = $row['waterOz'];
                            $rsleep = $row['sleepHrs'];
                            $rfitness = $row['fitnessHrs'];

                            //echo "<br>{$rdate} - Worked on habit: {$rhabit} - {$rwater} glasses - {$rsleep} hours - {$rfitness} hours";
                            $track .= "<tr>
                                <td>$rdate</td>
                                <td>$rhabit</td>
                                <td>($rwater) 8 oz glasses</td>
                                <td>$rsleep hr</td>
                                <td>$rfitness hr</td>
                            </tr>";
                        } //while
                        $track .= "</table>";

                        echo $track;
                    }
                    else {
                        echo "<h3>Log Your Progress Daily and See it Here!</h3>";
                    }
                ?>
            </div> <!-- history -->
        </div> <!-- userGoals -->



        <div id='userHistory'>

            <form id='logForm' method='POST' action=<?php echo "user.php?uName={$_SESSION['uName']}" ?>>
                <h2>Log Your Progress</h2>

                <div class='formDiv' id='radioLog'>
                    <!-- Did you work on habit today? (7 days = celebrate) <br> -->
                    <h3>Did you work towards your new habit today?</h3>
                    <div id='radioSection'>
                        <label for='logYes'>Yes</label>
                            <input type='radio' id='logYes' name='habitQues' value='yes'>
                        <label for='logNo'>No</label>
                            <input type='radio' id='logNo' name='habitQues' value='no'>
                    </div>
                </div>

                <div class='formDiv'>
                    <label for='logWat'>How many total 8 ounce glasses of water have you had today?</label>
                    <input type='text' id='logWat' name='logWat' value='<?php echo $todayWat ?>'>
                </div>

                <div class='formDiv'>
                    <label for='logSle'>How many total hours of sleep did you get last night?</label>
                        <input type='text' id='logSle' name='logSle' value='<?php echo $todaySle ?>'>
                </div>

                <div class='formDiv'>
                    <label for='logExc'>How many total hours have you exercised today?</label>
                        <input type='text' id='logExc' name='logExc' value='<?php echo $todayExc ?>'>
                </div>

                <?php
                    echo "<h2 style='color:rgb(255, 69, 69);'>".$outLog."</h2>";
                ?>

                <div class='buttonDiv'>
                    <input type='submit' value='Log Progress' id='trackButton'>
                    <!-- TRY ADDING "EDIT GOALS" BUTTON -->
                </div>
            </form>
        </div> <!-- userHistory -->
    </div> <!-- end of userDash -->

</main>

<?php
    require_once 'foot.php';
?>
