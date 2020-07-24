<?php
    require_once 'head.php';
    require_once "../../../req/connect.php";



    require_once 'nav.php';
?>

<main>
    <a href="index.php">
        <img id="appLogo" src="images/logo_transparent.png" alt="myMode logo">
    </a>

    <?php
        if (isset($_SESSION['uName'])) {
            echo "<h3 class='welcomeMsg'>Welcome {$_SESSION['uName']} | <a href='logout.php'>Logout</a> | <a href='user.php?uName={$_SESSION['uName']}'>Go to Dashboard</a></h3><br>";
        }
        else {
            echo "<h3 class='welcomeMsg'><a href='index.php'>Create a Profile &nbsp;</a> | <a href='login.php'>&nbsp; Already a User? Login</a></h3>";
        }
    ?>

    <div class='about' id='about3'>
        <h1>Questions? <br> Find Answers Here</h1>
        <div class='infoDet'>
            <div class='faqP'>
                <div class='qa'>
                    <p class='ques'>What is myMode?</p>
                        <p class='ans'>myMode is an application designed to help users set new healthy goals and track their progress. The idea is that regular logging of progress will help to reinforce healthy habits.</p>
                </div>
                <div class='qa'>
                    <p class='ques'>How do I get started?</p>
                        <p class='ans'>Getting started is easy! Go to our <a href='index.php'>Home Page</a>, create a unique username and pasword, enter your email, and click on "Create Profile". After your profile is successfully created, you will see a welcome message with a link to go to our <a href='login.php'>Log In</a> page. Click that link, then enter your username and password. Once your sign in has been verified, you will be taken to your dashboard, where you can create goals, log daily progress, and view past logs.</p>
                </div>
                <div class='qa'>
                    <p class='ques'>I already entered logs once today. How do I update them?</p>
                        <p class='ans'>myMode is set up so you can enter logs multiple times per day. If myMode detects that you already entered logs for today's date, it will show those previous entries in your log form. You can then update today's entries, and your log will update rather than create a new log.</p>
                </div>
                <div class='qa'>
                    <p class='ques'>How do I change my goals?</p>
                        <p class='ans'>myMode is currently in development, and we are working to add new features and functionality daily. For now, there is not an option to edit existing goals, but that option will be here soon.
                            <br>
                        Here is a small list of features we plan to add soon:
                            <ul>
                                <li>&#1995; Edit Goals</li>
                                <li>&#1995; Sort logs</li>
                                <li>&#1995; Add real-time user name availability on profile creation</li>
                                <li>&#1995; View log Graphs (instead of a plain chart!)</li>
                            </ul>
                        </p>
                </div>
                <div class='buttonDiv'>
                    <a href='index.php' id='signButton'>Ready to Start? Sign Up</a>
                </div>
                <br>
                <p>Can't find your question here? <br>Send us an message at <a href='https://www.jessicathaxton.com/contact/'>jessicathaxton.com/contact</a>.</p>
            </div>
        </div>
    </div> 

</main>

<?php
    require_once 'foot.php';
?>