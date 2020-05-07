<?php

//check is user is logged in
include "inc/session_check.php";
if ($_SESSION['user'] != "mainadmin") {
	header('Location: adminlogin.php');
	die();
}
$title = "ADD SERVER";

include "inc/config.php";

//check request is made to save server details
if (isset($_POST["save"]))
{

	$id = $_POST["cid"];
	$name = $_POST["sname"];
	$os = $_POST["os"];
	$comment = $_POST["comment"];

	$db = db_connect();
	//save server details
	$db_query = "insert into server (name,cluster_id,os,is_bookable,comment) values('".$name."',".$id.",'".$os."',1,'".$comment."' )";
	$res = $db->query($db_query);		

	//redirect to cluster.php
	header("Location:cluster.php");
	die();
}

?>

<!--create add server page -->
<html>
<head>
    <link rel="stylesheet" type="text/css" href="inc/style.css">
</head>
<body>

	<?php
		show_menu();
	?>

	<center>
		
		<form action="addserver.php" method="POST">
			<table border=1>
			<tr><th>Cluster</th><td><select name="cid" id="cid" required>

			<?php
				$db = db_connect();

				  // get cluster data and display it in the form
				$db_query = "SELECT id,name FROM cluster WHERE is_bookable =1";
				$res = $db->query($db_query);
				while($db_row = $res->fetch())
				{
					echo "<option value=\"".$db_row['id']."\">".$db_row['name']."</option>\n";
				}
			?>							
			</td></tr>
		
				<tr><th>Server Name   </th><td><input type=text name="sname" size="50" required  value=""></td></tr>
				<tr><th>OS  </th><td><input type=text name="os"  size="50" required value=""></td></tr>				
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
