<?php

// connect.php
// To connect between php scripting and database.
define("DATABASE_HOST", "localhost");         // or "127.0.0.1"
define("DATABASE_USER", "root");              // default for XAMPP/WAMP
define("DATABASE_PASSWORD", "");              // default is empty in XAMPP
define("DATABASE_NAME", "quickbite");                  // replace with your DB name

// Create connection (port 3306 is default, no need to specify)
$conn = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);

// If connection failed then dsplay mysql error
if (mysqli_connect_errno())
	{
	 echo "Failed to connect to MySQL: " . mysqli_connect_error ();
	}

// To select one particular database to be used
mysqli_select_db($conn,"quickbite") or die( "Could not open products database");

//set the default time zone to use in Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');
?>