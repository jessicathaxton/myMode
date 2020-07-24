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

            if (password_verify($pass1, $user['pass'])) {
                // SESSION INFO GOES HERE
                $_SESSION['uName'] = $uName;
                echo "<script>window.location.assign('user.php?uName=".$_SESSION['uName']."')</script>";
                $out = "Welcome {$uName}!<br>";
            }
            else {
                $out = "Invalid credentials. Please try again.";
            } 
        } // else
    } // isset

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
    ?>

    <div id='info'>
        <h1>Meet MyMode</h1>
        <div class='infoDet'>
            <div class='infoP'>
                <p>Your myMode account helps track your healthy habits.</p>
                <p>Eat, sleep, exercise, meditate... and so much more!</p>
                <p><b>Sign in</b> to set personalized goals and track your progress.</p>
            </div>
        </div>
    </div> 

    <div id='logIn'>
        <?php
            if(isset($_SESSION['uName'])) {		// if exists, they're logged in
                echo "<h2>Hello {$_SESSION['uName']}. <a href='logout.php'>Logout</a></h2><br>";
                echo "<a href='user.php?uName={$_SESSION['uName']}'>Go to Dashboard</a>";
                // COOKIES - check if box is checked
                if(isset($_POST['remember'])) {
                    setcookie('uName', $uName, time()+3600);	// name, value, expiration
                }
            }
            else {			// else show form, if not logged in
        ?>	
            <form id='loginForm' method='POST' action='<?php echo $_SERVER['PHP_SELF'] ?>'>
                <div class='formDiv'>
                    <label for='uName'>User Name</label>
                    <input type='text' name='uName' id='uName'>
                </div>
                <div class='formDiv'>
                    <label for='pass1'>Password</label>
                    <input type='password' name='pass1' id='pass1'>
                </div>
                <div class='formDiv'>
                    <label for='remember'>Remember Me:</label>
                    <input type='checkbox' name='remember'>
                </div>

                <?php
                    echo "<h2 style='color:#a13301;'>".$out."</h2>";
                ?>

                <div class='buttonDiv'>
                    <input type='submit' value='Log In' id='subButton'>
                    <a href='index.php'; id='loginButton'>No Profile? Create One</a>
                </div>
            </form>
        <?php
            }	// end of else from isset
        ?>
    </div> <!-- end of logIn -->

</main>

<?php
    require_once 'foot.php';
?>