<?php
    require_once 'head.php';
    require_once "../../../req/connect.php";
    require_once "../helpers.php";

    $sql = "CREATE TABLE IF NOT EXISTS finalUser (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        uName VARCHAR(64) UNIQUE,
        access VARCHAR(24),
        email VARCHAR(128) UNIQUE,
        pass VARCHAR(512)
    )";

    $result = $pdo->query($sql);
    $submitted = FALSE;
    $sub_try = FALSE;
    $out = "";

    $uName = '';
    $email = '';

    if (isset($_POST['email'])) {
        $sub_try = TRUE;
        $uName = $_POST['uName'];
        $uName = strtolower($uName);
        $email = $_POST['email'];
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];
        $access = 'user';

        // ANY BLANK FIELDS?
        if ($uName == '' OR $email == '' OR $pass1 == '' OR $pass2 == '') {
            $out = "Please complete all the form fields.";
            $submitted = FALSE;
            $sub_try = TRUE;
        }
        else {
            // VERIFY PASSWORDS MATCH
            if ($pass1 != $pass2) {
                $out = "Passwords don't match. Please try again.";
                $submitted = FALSE;
                $sub_try = TRUE;
            }
            else {
                // MIN PASS LENGTH
                if (strlen($pass1) < 4) {
                    $out = "Password must be at least 4 characters long.";
                    $submitted = FALSE;
                    $sub_try = TRUE;
                }
                else {
                    //https://gist.github.com/Michael-Brooks/fbbba105cd816ef5c016
                    $passEx = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';
                    
                    if (!preg_match($passEx,$pass1)) {
                        $out = "Password must be mixed-case with a number, 4 digit minimum.";
                        $submitted = FALSE;
                        $sub_try = TRUE;
                    }
                    else {
                        // BASIC VALID EMAIL FORMAT?
                        if (validEmail($email) == FALSE) {
                            $out = "Invalid email format. Please try again.";
                            $submitted = FALSE;
                            $sub_try = TRUE;
                        }
                        else {
                            // SALT & HASH PASSWORD
                            $pass = password_hash($pass1, PASSWORD_DEFAULT);

                            // BIND PARAMS, THEN EXECUTE
                            try {
                                $sql = "INSERT INTO finalUser (uName, access, email, pass) VALUES (?, ?, ?, ?)";
                                $result = $pdo->prepare($sql);          
                                $result->execute(array($uName, $access, $email, $pass));
                                $thanks = "Thank you for registering, $uName. <br> Check your email for a confirmation!<br>
                                <p><a href='login.php' style='font-size: 2em;padding:30px;color:rgb(255, 69, 69);'>Log In</a>";
                                mail($email, "Thank You!", "Hello $uName! Thank you for registering on MyMode. We are happy to be part of your healthy habit adventure.");
                                $submitted = TRUE;
                            }
                            catch (PDOException $e) {
                                //echo $e->getMessage();
                                if ($e->getCode() == 23000) {
                                    $out = "Email or username is already in use. Please try another.";
                                    $submitted = FALSE;
                                    $sub_try = TRUE;
                                }
                                else {
                                    $out = $e->getMessage();
                                    $submitted = FALSE;
                                    $sub_try = TRUE;
                                }
                            } //catch
                        } //if email validation
                    } // if mixed-case
                } // if pass length
            } //if pass match
        } // if blank fields
    } //isset


    require_once 'nav.php';
?>

<main>
    <a href="index.php">
        <img id="appLogo" src="images/logo_transparent.png" alt="myMode Logo">
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
                <p>MyMode is an app for tracking your healthy habits.</p>
                <p>Eat, sleep, exercise, hydrate... and so much more!</p>
                <p>Sign up to see your progress and reinforce your healthy lifestyle!</p>
            </div>
            <div class='buttonDiv'>
                <a href='about.php' id='aboutButton'>Learn More</a>
            </div>
        </div>
    </div> 

    <!-- SIGN UP (or LOGIN, button on side or under) -->
    <div id='signUp'>
        <?php
            if (isset($_SESSION['uName'])) {
                echo "<h2>Welcome, {$_SESSION['uName']}!</h2><br>
                    <a href='logout.php'>Logout</a><br>";
                echo "<a href='user.php?uName={$_SESSION['uName']}' id='subButton'>Go to Dashboard</a>";
            }
            else {
                if ($submitted == FALSE) {
        ?>
            <form id='signUpForm' method='POST' action='<?php echo $_SERVER['PHP_SELF']?>'>
                <h1>Join MyMode</h1>
                <div class='formDiv'>
                    <label for='uName'>Enter a Username</label>
                        <input type='text' name='uName' id='uName' value='<?php echo $uName ?>'>
                </div>

                <div class='formDiv'>
                    <label for='email'>What's Your Email?</label>
                        <input type='text' name='email' id='email' value='<?php echo $email ?>'>
                </div>

                <div class='formDiv'>
                    <label for='pass1'>Create A Password</label>
                        <input type='password' name='pass1' id='pass1'>
                </div>

                <div class='formDiv'>
                    <label for='pass2'>Check Password!</label>
                        <input type='password' name='pass2' id='pass2'>
                </div>

        <?php
            if ($sub_try) {
                echo "<h2>".$out."</h2>";
            } 
        ?>
                <div class='buttonDiv'>
                    <input type='submit' value='Create Profile' id='subButton'>
                    <a href='login.php' id='loginButton'>Already a User? Login</a>
                </div>
            </form>
        <?php
            }
            else {
                // if form submits successfully, display $thanks
                echo "<h2>".$thanks."</h2>";
            }
        }  
        ?>
    </div> <!-- end of div signUp-->
        
</main>

<?php
    require_once 'foot.php';
?>

<script>

    function validUname(uName) {
        var uName = document.getElementById('uName').value;

        // CHECK IF UNAME EXISTS (JQUERY)
        // CAN ALSO SET REGEX FOR NBSP in username

        $("#unHint").remove();

        if (uName == '' || uName.length < 2) {
            $('#uName').css("border","2px solid #ff4545");
            $('#uName').after("<span id='unHint' class='hint'>Enter a Unique Username, 2 characters or more!</span>");
            return false;
        }
        else {
            $('#uName').css("border","2px solid #00b0c0");
            $('#uName').after("<span id='unHint' class='hint'>Username looks good!</span>");
            return true;
        }
    } // validUname

    function validEmail(email) {
        var email = document.getElementById('email').value;

        var emRegEx = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        
        var retVal = emRegEx.test(email);
        $("#emHint").remove();

        if (!retVal) {
            $('#email').css("border","2px solid #ff4545");
            $('#email').after("<span id='emHint' class='hint'>Email Example: jdoe@test.com</span>");
            return false;
        }
        else {
            $('#email').css("border","2px solid #00b0c0");
            $('#email').after("<span id='emHint' class='hint'>Email looks good!</span>");
            return true;
        }
    } // validEmail

    function validPass1(pass1) {
        var pass1 = document.getElementById('pass1').value;
        var regexPass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{3,50}$/;
        $("#p1Hint").remove();

        if (regexPass.test(pass1) && pass1 != '') {
            $('#pass1').css("border","2px solid #00b0c0");
            $('#pass1').after("<span id='p1Hint' class='hint'>Password meets requirements!</span>");
            return true;
        }
        else {
            $('#pass1').css("border","2px solid #ff4545");
            $('#pass1').after("<span id='p1Hint' class='hint'>4 character minimum, mixed case alphanumeric.</span>");
            return false;
        }
    } // validPass1

    function validPass2(pass2) {
        $("#p2Hint").remove();
        
        if ($("#pass2").val() != $("#pass1").val()) {
            $("#pass2").css("border","2px solid #ff4545");
            $("#pass2").after("<span id='p2Hint' class='hint'>Password does not match...</span>");
            return false;
        }
        else {
            $("#pass2").css("border","2px solid #00b0c0");
            $("#pass2").after("<span id='p2Hint' class='hint'>Password matches!</span>");
            return true;
        }
    } // validPass2



    $(document).ready(function() {

        $("input").blur(function() {
            $(this).each(function() {
                if ($(this).val() == "") {
                    $(this).css("border","2px dashed #ff4545");
                }
            });
        });

        // email validation
        $('#email').keyup(function() {
            validEmail($('#email'));
        });

        // uName validation
        $('#uName').keyup(function() {
            validUname($('#uName'));
        });

        // pass1 mixed case alphanumeric validation
        $("#pass1").keyup(function() {
            validPass1($('#pass1'));
        });

        // pass2 feedback, realtime
        $("#pass2").keyup(function() {
            validPass2($('#pass2'));
        });


        $("#signUpForm").submit(function() {
            console.log('trying to submit');
            if (validUname() && validEmail() && validPass1() && validPass2()) {
                console.log('Everything looks good!');
                return true;
            }
            else {
                console.log('Something looks wrong...');
                return false;
            }
            //return false; // troubleshooting; remove later
	    }); //submit

    }); // document.ready
</script>