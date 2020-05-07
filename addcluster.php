<?php

//check is user is logged in
include "inc/session_check.php";
if ($_SESSION['user'] != "mainadmin") {
	header('Location: adminlogin.php');
	die();
}
$title = "Add Cluster";
include "inc/config.php";

//check request is made to save cluster details
if (isset($_POST["save"]))
{
			  
	$name = $_POST["cname"];
	$cpu = $_POST["cpu"];
	$cores = $_POST["cores"];
	$ram = $_POST["ram"];
	$comment = $_POST["comment"];
	$db = db_connect();

	//save cluster details
	$db_query = "insert into cluster (name,cpu,cores,ram,is_bookable,comment) values('".$name."','".$cpu."','".$cores."','".$ram."',1,'".$comment."' )";
	$res = $db->query($db_query);
	
	//redirect to cluster.php
	header("Location:cluster.php");
	die();
}
?>

<!--create add cluster page -->
<html>
<head>
    <link rel="stylesheet" type="text/css" href="inc/style.css">
</head>
<body>
	<!--div  style="display:inline;"><a href="newbooking.php">New Booking</a> | 
	<a href="index.php">Existing Bookings</a> | 
	<a href="cluster.php">Cluster</a></div>
	<div style="display:inline;margin: 0px 0px 0px 45%;">&nbsp;</div>
	<div style="display:inline;">Logged in as : <?php echo $_SESSION['user']; ?>
	&nbsp;&nbsp;&nbsp;<a href="login.php?out=o">Logout</a></div-->
	<?php
		show_menu();
	?>
	<center>
		
		<form action="addcluster.php" method="POST">
			<table border=1>
				<tr><th>Cluster Name   </th><td><input type=text name="cname" size="50" required  value=""></td></tr>
				<tr><th>Cpu  </th><td><input type=text name="cpu"  size="50" required value=""></td></tr>
				<tr><th>Cores    </th><td><input type=text name="cores" size="50" required value=""></td></tr>	
				<tr><th>Ram    </th><td><input type=text name="ram" size="50" required value=""></td></tr>	
				<tr><th>Comment</th><td><textarea name="comment" rows=5 cols=50 ></textarea></td></tr>
				<tr><th></th><td>

				<input type="submit" value="save">
				<input type="hidden" name="save" value="1">

				</td></tr>
			</table>
		</form>
	</center>

</body>
</html>

