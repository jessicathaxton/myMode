MYMODE
	MyMode is a simple healthy-habit goal tracking web application. 

MYMODE'S GOALS
	MyMode was created as a pet project, a motivator for the developer to not only test their coding skills but to inspire them to use their own application on a healthy habit journey. 
	MyMode is the first fully functional site built by the developer. 

BUILD STATUS
	FUNCTIONAL, still IN DEVELOPMENT
	Wish List Includes:
 	- add more engaging graphics and charts populated using the mySQL data
 	- test jQuery animations in lieu of CSS animations, creating an interactive interface to better engage end users
	- add filters and custom reporting options in the admin console
	- add sort options to the user.php logs to allow for customized viewing
	- allow users to edit or change their habit goals
	- allow users to choose their own local timezone

LANGUAGES USED
	HTML5
	CSS3
	JavaScript
	jQuery
	PHP
	MySQL

SITE COLORS
	BACKGROUND
		rgb(32, 42, 64);
	LINKS & BUTTONS
		NORMAL: 	rgb(255, 69, 69);	RED
		HOVER/VISITED: 	rgb(235, 44, 44);	RED
		NORMAL: 	rgb(0, 176, 192);	BLUE
		HOVER/VISITED: 	rgb(0, 128, 139);	BLUE
	HEADERS
		rgb(0, 176, 192);
	BORDER/ACCENT
		rgb(50, 49, 80);
	
TECH / FRAMEWORK
	Built with:
	- Visual Studio Code 	- https://code.visualstudio.com/
	- FileZilla 		- https://filezilla-project.org/
	- XAMPP	(Apache, MySQL)	- https://www.apachefriends.org/index.html

FEATURES
	MyMode provides tracking and accountability of several basic healthy habits: drinking water, getting enough sleep, and exercising. 
	Additionally, users can create a custom habit which they work towards. Progress is logged daily by the user.

FILES INCLUDED
	MAIN SITE	USER		ADMIN		MISCELLANEOUS		FOLDERS
	 index.php	 login.php	 manage.php	 privacy.php		 images - contains images & logo
	 about.php	 user.php	 viewLogs.php	 boredAPI.php		 styles - contains mainstyle.css
	 faq.php	 logout.php			 README.txt

	* all pages are framed with requires: 	head.php 		
						nav.php 	
						foot.php

INSTALLATION
	- Download and unzip the 'final' folder, which contains the above indicated files
		- My file structure: 	C:\xampp\htdocs\233p\final
	- Place the files in a subfolder inside your 'htdocs' folder (or equivalent; wherever your live website files live).
	- Inside PHPmyAdmin, create a mySQL database. In the following step, you will need to create a connect script to this database.
	- Inside your main folder (xampp for me), create a folder called 'req'. This is where your connect script needs to be.
		- My file structure: 	C:\xampp\req
		- Connect script should be named "connect.php"
		- Connect script should use PDO, which is used throughout the project. Using MySQLi may cause conflicts.

API Reference
	There is currently one API called - boredAPI.com is a license- and token-free API available to the public. 
	The API is employed in this project through CURL/PHP.
	myMode has two files controlling the API: boredAPI.php and user.php, wherein activity and habit suggestions are made to each user when their dashboard loads.

HOW TO USE?
--- USER ---
	Create an account following the requirements indicated on index.php. 
	After account creation, go to login.php. 
	Sign into your account. 
		- You should be redirected to your dashboard, provided you do not have any extension stopping redirects.
	Fill out your personal goals.
	Daily, log progress toward your goals. Progress can be updated throughout the day; only the most recent log for that day will remain in the database.
	Logs are viewable after data has been logged to the mySQL database. The more days you track, the more interesting your logs are!

--- ADMIN ---
	Go to the admin portal, manage.php. 
	Sign in with a user who has admin priviledges (currently granted through PHPmyAdmin).
	Once signed in, you can view a variety of logs, change user passwords, and a few other things.

CREDITS
	BoredAPI (https://www.boredapi.com/) for use of their activity suggestion API.
	Ken S. (my instructor) for useful feedback and constructive criticism through the development stages.
	@kalvisuals - for the background image - https://unsplash.com/photos/fMIsAL72P3U

CONTRIBUTE
	MyMode currently does not have an official feedback forum. If interest is generated, the developer will pursue a forum creation. 
	For now, please feel free to send C&C or suggestions for code improvement to the developer directly at support@jthaxton.cocc-cis.com.

LICENSE
	No licenses are granted for this project. 
	Please do not reproduce or redistribute, in whole or part, without prior written permission from the developer.
	That said, feel free to look at the code for ideas and inspiration!

COPYRIGHT
	2020 © Jessica Thaxton 

CONTACT
	For questions not addressed in the README or FAQ, please contact the developer at support@jthaxton.cocc-cis.com