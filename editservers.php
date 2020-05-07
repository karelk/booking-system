<?php

//check is user is logged in
include "inc/session_check.php";
if ($_SESSION['user'] != "mainadmin") {
	header('Location: adminlogin.php');
	die();
}
$title = "EDIT SERVER";
include "inc/config.php";
	 show_header_start();

?>
<script>
function checkmode()
{
	if(document.getElementById('mode').checked)
	{
		document.getElementById('os').disabled  = true;
		document.getElementById('sname').disabled  = true;
		document.getElementById('comment').disabled  = true;

	}else
	{
		document.getElementById('sname').disabled  = false;
		document.getElementById('os').disabled  = false;		
		document.getElementById('comment').disabled  = false;
	}	

}
</script>
<?php

	show_header_end();


//check if server is updated
if (isset($_POST["save"]))
{
	if(isset($_POST["mode"]))
	{
		$option = 1;
	}else
	{
		$option = 0;
	}
	$id = $_POST["id"];

	$db = db_connect();
	
	
		//check if server is set for maintainance or cluster details are updated.
	if($option == 1)
	{
		//set server to maintainance mode.
		$db_query = "SELECT is_bookable FROM server WHERE is_bookable !=3 and id = ".$id;
		$res = $db->query($db_query);
		$db_row = $res->fetch();
		$isbk = $db_row['is_bookable']; 

		if($isbk == 1)
		{
			$db_query = "update server set is_bookable = 0  where id = ".$id;
		}else
		{
			$db_query = "update server set is_bookable = 1  where id = ".$id;
		}
		$res = $db->query($db_query);

	}else
	{
		//update server details.
		$name = $_POST["sname"];
		$os = $_POST["os"];
		$comment = $_POST["comment"];
		$db_query = "update server set name = '".$name."',os='".$os."',comment='".$comment."',is_bookable = 1 where id = ".$id;	
		$res = $db->query($db_query);					
	}
	//redirect to cluster.php
	header("Location:cluster.php");
	die();

}else
{
  // if server id is provided display details of that server
  if (isset($_GET["sid"]))
  {
		$tbloption = 0;
		$sid = $_GET["sid"];
	    $option = $_GET["opt"];
		  
	  $db = db_connect();

	  // get server data and show in form
	  $db_query = "SELECT * FROM server WHERE is_bookable !=3 and id = ".$sid;
	  $res = $db->query($db_query);
	  $db_row = $res->fetch();

	  if($db_row['is_bookable'] == 1)
	  {
  		 $option  = 1;
	  }else
	  {
  		 $option  = 0;
	  }
	  echo "<body>";
	  show_menu();
	  echo "<center>";
	  echo "<form action=\"editservers.php\" method=\"POST\">";
	  echo "<table border=1>\n";
	  echo "<tr><th>id</th><td>".$db_row['id']."</td></tr>\n";
	  echo "<tr><th>Server Name   </th><td><input type=text id=\"sname\" name=\"sname\" size=\"50\""; if($option == 0) { echo " disabled ";}  echo "value=\"".$db_row['name']."\"></td></tr>\n";
	  echo "<tr><th>OS  </th><td><input type=text id=\"os\" name=\"os\"  size=\"50\""; if($option == 0) { echo " disabled ";} echo "value=\"".$db_row['os']."\">          </td></tr>\n";
	  echo "<tr><th>Status    </th><td>";
	  if($db_row['is_bookable'] == 1)
	  {
		   echo "Active";
		  echo"&nbsp;<input type=\"checkbox\" id=\"mode\" name=\"mode\" value =\"1\" onclick=\"checkmode()\"/>";	  
	  }else
	  {		  
		  echo "Under Maintainance";
		  echo"&nbsp;<input type=\"checkbox\" id=\"mode\" name=\"mode\" value =\"1\" checked onclick=\"checkmode()\"/>";  		 
	  }

	  echo "</td></tr>\n";	
	  echo "<tr><th>Comment</th><td><textarea id=\"comment\" name=\"comment\""; if($option == 0) { echo " disabled ";}  echo " rows=5 cols=50 >".$db_row['comment']."</textarea></td></tr>\n";
		// submit lines
	  echo "<tr><th></th><td>\n";
	  echo "<input type=\"submit\" name = \"save\" value=\"Save\">\n";
	  echo "<input type=\"hidden\" name=\"id\" value=\"".$db_row['id']."\">\n";

	  echo "</td></tr>\n";
	  echo "</table>\n";
  	  echo "</form>\n";
	  echo "</center>";
  }else
	{
	  	//redirect to cluster.php if server id is not given
	  	header("Location:cluster.php");
		die();
	}
}
 echo "</body>";

  show_footer();

?>
