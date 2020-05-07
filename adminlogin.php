<?php

 error_reporting( -1 );
 ini_set( 'display_errors', 1 );



session_start();

//check if password is entered
$errormsg = "";
if(isset($_POST['userPassword']))
{

	$Passwd = $_POST['userPassword'];

	//check the password entered is correct
	if($Passwd == "ADMIN-PASSWORD-GOES-HERE") 
	{
		//create admin session
		$_SESSION["user"] = "mainadmin";
		$_SESSION["mailid"] = "admin@your-server.your-domain.org";

		//redirect to index page
		header('Location: index.php');
		die();
	}
	else 
	{
		$errormsg =  "Invalid Password";
	}
} 


	//Create Admin login page

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
<form action="adminlogin.php" method="post">
<div>&nbsp;</div>
<div>
	<div style="display: inline;">Admin Password:</div><div style="display: inline;"> <input type="password" name="userPassword" required/></div>
	<div>&nbsp;</div>
	<div><input type="submit" name="submit" value="Submit" /></div>
</div>
</form>
</div>
<?php
	echo "</center>";
	echo "</body>";
	echo "</html>";
?>