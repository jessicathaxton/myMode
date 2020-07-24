<?php
    header("refresh:4; url=index.php");
    require_once 'head.php';
    require_once "../../../req/connect.php";
    require_once "../helpers.php";
    require_once 'nav.php';
?>

<main>
    <a href="index.php">
        <img id="appLogo" src="images/logo_transparent.png" alt="myMode Logo">
    </a>

    <div class='out'>
        <?php 
            if(isset($_SESSION['uName'])) {		// if exists, they're logged in
                echo "<h1>Goodbye {$_SESSION['uName']}. Come back soon!</h1>";

                $_SESSION = array();
                SESSION_DESTROY();
            }
            else {
                echo "<h3>You are not signed in.<br>
                    <a href='login.php'>Sign In</a> or <a href='index.php'>Create an Account</a></h3>
                ";
            }
        ?>

        <div class='infoImg'>
            <img src='images/logo_transparent.png' alt="myMode logo" id='outImg'>
        </div>
    </div>
        
</main>

<?php
    require_once 'foot.php';
?>
