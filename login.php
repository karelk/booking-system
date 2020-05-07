<?php

 error_reporting( -1 );
 ini_set( 'display_errors', 1 );
// check to see if user is logging out

session_start();

$errormsg = "";
//check is user name/id is enetered
if(isset($_POST['userLogin']))
{
	$usrid = $_POST['userLogin'];
	$Passwd = $_POST['userPassword'];

	//include file for ldap authentication
	include('inc/nethzauth.php');

    //create authentication class object
	$auth = new NethzAuthModel();
	if($auth->test($usrid, $Passwd)) {
		//create session of user and admin
		$_SESSION["user"] = $usrid;		
		$_SESSION["mailid"] = $auth->getUsermail();		
		header('Location: index.php');	
		die();
	}
	else {
		$errormsg =  $auth->getMessage();
	}
} 


// output logout success
if(isset($_GET['out']))
{
	//distroy session of user and admin
	session_unset();	
	session_destroy();
}


	//create login page
	$title = "Login";
	include "inc/config.php";
	echo "<html>";
	echo "<head>";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"inc/style.css\">";
    echo "</head>";
	echo "<body>";
	if(!empty($errormsg))
	{
		echo "<center><p>".$errormsg."</b></center>";
	}
	echo "<center><p><b>".strtoupper($title)."</b><p></center>";
	echo "<center>";

?>
<div>
<form action="login.php" method="post">
<div>
	<div style="display: inline;">User:</div><div style="display: inline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="userLogin" required/></div>
</div>
<div>&nbsp;</div>
<div>
	<div style="display: inline;">Password:</div><div style="display: inline;"> <input type="password" name="userPassword" required/></div>
	<div>&nbsp;</div>
	<div><input type="submit" name="submit" value="Submit" /></div> 
</div>
</form>
</div>
<div><a href="adminlogin.php">Go to Admin Login</a></div>
<?php
	echo "</center>";
	echo "</body>";
	echo "</html>";
?>