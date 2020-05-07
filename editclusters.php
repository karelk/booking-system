<?php

//check is user is logged in
include "inc/session_check.php";
if ($_SESSION['user'] != "mainadmin") {
	header('Location: adminlogin.php');
	die();
}
$title = "Edit Cluster";
include "inc/config.php";
$title ="EDIT CLUSTER";

//check if cluster is updated
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
		
	//check if cluster is set for maintainance or cluster details are updated.
	if($option == 1)
	{
		//set cluster to maintainance mode.
		$db_query = "SELECT is_bookable FROM cluster WHERE is_bookable !=3 and id = ".$id;
		$res = $db->query($db_query);
		$db_row = $res->fetch();
		$isbk = $db_row['is_bookable']; 
		
		if($isbk == 1)
		{
			$db_query = "update cluster set is_bookable = 0  where id = ".$id;
		}else
		{
			$db_query = "update cluster  set is_bookable = 1  where id = ".$id;
		}			
		$res = $db->query($db_query);
	}else
	{
		$name = $_POST["cname"];
		$cpu = $_POST["cpu"];
		$cores = $_POST["cores"];
		$ram = $_POST["ram"];
		$comment = $_POST["comment"];
		//update cluster details.
		$db_query = "update cluster set name = '".$name."',cpu='".$cpu."',cores='".$cores."',ram='".$ram."',comment='".$comment."',is_bookable = 1  where id = ".$id;
		
		$res = $db->query($db_query);
	}
	
	//redirect to cluster.php
	header("Location:cluster.php");
	die();

}else
{
	
 
  // if cluster id is provided display details of that cluster
  if (isset($_GET["cid"]))
  {
	  show_header_start();
		?>
		<script>

		function checkmode()
		{
			if(document.getElementById('mode').checked)
			{
				document.getElementById('cname').disabled  = true;
				document.getElementById('cores').disabled  = true;
				document.getElementById('cpu').disabled  = true;
				document.getElementById('ram').disabled  = true;
				document.getElementById('comment').disabled  = true;

			}else
			{
				document.getElementById('cname').disabled  = false;
				document.getElementById('cpu').disabled  = false;
				document.getElementById('cores').disabled  = false;
				document.getElementById('ram').disabled  = false;
				document.getElementById('comment').disabled  = false;
			}	
		}
		</script>
		<?php
		
		show_header_end();
		show_menu();

		$tbloption = 0;
		$cid = $_GET["cid"];
	    $option = $_GET["opt"];
		  
	  $db = db_connect();

	  // get cluster data and show in form
	  $db_query = "SELECT * FROM cluster WHERE is_bookable!=3 and id = ".$cid;
	  $res = $db->query($db_query);
	  $db_row = $res->fetch();		

	   if($db_row['is_bookable'] == 1)
	  {
  		 $option  = 1;
	  }else
	  {
  		 $option  = 0;
	  }

	  echo "<center>";
	  echo "<form action=\"editclusters.php\" method=\"POST\">";
	  echo "<table border=1>\n";
	  echo "<tr><th>id</th><td>".$db_row['id']."</td></tr>\n";
	  echo "<tr><th>Cluster Name   </th><td><input type=text name=\"cname\" id=\"cname\" size=\"50\""; if($option == 0) { echo " disabled ";}  echo "value=\"".$db_row['name']."\"></td></tr>\n";
	  echo "<tr><th>Cpu  </th><td><input type=text id=\"cpu\" name=\"cpu\"  size=\"50\""; if($option == 0) { echo " disabled ";}  echo "value=\"".$db_row['cpu']."\">          </td></tr>\n";
	  echo "<tr><th>Cores    </th><td><input type=text name=\"cores\" id=\"cores\" size=\"50\""; if($option == 0) { echo " disabled ";}  echo "  value=\"".$db_row['cores']."\">            </td></tr>\n";	
  	  echo "<tr><th>Ram    </th><td><input type=text id=\"ram\" name=\"ram\" size=\"50\""; if($option == 0) { echo " disabled ";}  echo "  value=\"".$db_row['ram']."\">            </td></tr>\n";	
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
	  echo"</td></tr>\n";	
	  echo "<tr><th>Comment</th><td><textarea id=\"comment\" name=\"comment\""; if($option == 0) { echo " disabled ";}  echo " rows=5 cols=50 >".$db_row['comment']."</textarea></td></tr>\n";
		// submit lines
	  echo "<tr><th></th><td>\n";	  
	  echo "<input type=\"submit\" name = \"save\" value=\"Save\">\n";
	  echo "<input type=\"hidden\" name=\"id\" value=\"".$db_row['id']."\">\n";

	  echo "</td></tr>\n";
	  echo "</table>\n";
  	  echo "</form>\n";
	  echo "</center>";
	  
	  show_footer();

  }else
	{
	    //redirect to cluster.php if cluster id is not given
	  	header("Location:cluster.php");
		die();
	}
}

?>
