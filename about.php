<?php
    require_once 'head.php';
    require_once "../../../req/connect.php";



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

    <div class='about' id='about1'>
        <h1>What is MyMode?</h1>
        <div class='infoDet'>
            <div class='infoP'>
                <p>MyMode is an app for tracking your healthy habits.</p>
                <p>Eat, sleep, exercise, hydrate... and so much more!</p>
                <p>Regular tracking helps you stay on target, reinforcing new habits as you build them!</p>
                <p>Set your goals and push your limits.</p>
            </div>
        </div>
    </div> 

    <div class='about' id='about2'>
        <h1>Our Vision</h1>
        <div class='infoDet'>
            <div class='infoP'>
                <h2>Supporting you is our mission!</h2>
                <p>We all know the challenges of staying on track with basic healthy habits, and it's even harder to form new habits. At MyMode, we want to make it easy for you to create healthy goals and turn them into habits.</p>
                <p>Visit MyMode daily, log your progress, and reinforce new habits as you view all the steps you have made.</p>
                <div class='buttonDiv'>
                    <a href='index.php' id='signButton'>Ready to Start? Sign Up</a>
                </div>
            </div>
        </div>
    </div> 


</main>

<?php
    require_once 'foot.php';
?>