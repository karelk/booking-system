<?php

//check is user is logged in
include "inc/session_check.php";

include "inc/config.php";
	echo "<html>";
	echo "<head>";

	global $bdt;
	global $btt;
	global $edt;
	global $ett;

	//check and populate dates
	if (isset($_GET['bdate'])) 
	{ 
		$bdt=$_GET['bdate']; 
		$bdt = date('Y-m-d', strtotime($bdt));
	}

	if (isset($_GET['btime'])) 
	{ 
		$btt=$_GET['btime'];
	}

	if (isset($_GET['edate'])) 
	{ 
		$edt=$_GET['edate'];
		$edt = date('Y-m-d', strtotime($edt));
	}

	if (isset($_GET['etime'])) 
	{ 
		$ett=$_GET['etime']; 	
	}

	if($btt != "now")
	{
		$bdatetime = $bdt." ".$btt.":00:00";
	}else
	{
		$bdatetime = $bdt." ".date('H:i').":00";
	}

	if($ett != "now")
	{
		$edatetime = $edt." ".$ett.":59:59";
	}else
	{
		$edatetime = $edt." ".date('H:i').":59";
	}
?>
	<script language="JavaScript" type="text/JavaScript">
	
		//function to retrieve servers from table and update the newbooking page controls with server details.
		function UpdateServer()
		{
		<?php

		echo" var serverids = [];";
		echo" var allserverids = [];";
		echo" var clustersid = [];";
		echo" var clusterslength = 0;";
		$db = db_connect();

		$cnt = 0;
		$query='SELECT id,name,is_bookable from cluster where is_bookable != 3  order by id';
		$res = $db->query($query);
		while($row = $res->fetch()) 
		{			
			echo "clustersid.push(".$row['id'].");\n";
			$cnt++;
		}

		//get cluster details from cluster table
		$query='SELECT id,name from cluster where is_bookable=1 order by id';
		$res = $db->query($query);
		while($row = $res->fetch()) 
		{					
			//get server details from server table for following cluster
			$query1="SELECT id,name,is_bookable from server where is_bookable != 3 and cluster_id=".$row['id'];	


			$res_server = $db->query($query1);
			while($row_server = $res_server->fetch()) 
			{ 				
				//get server details which is available between begin and end date range.
				$query1="SELECT server.id,server.is_bookable AS servers FROM booking INNER JOIN booking_map ON booking.id = booking_map.booking_id INNER JOIN server ON booking_map.server_id = server.id WHERE (COALESCE('".$bdatetime."' BETWEEN booking.begin AND booking.end) OR COALESCE('".$edatetime."' BETWEEN booking.begin AND booking.end)) AND booking.status=1 and server.id =".$row_server['id'];

							
				$res_ser = $db->query($query1);
				if ($res_ser->fetchColumn() > 0) 
				{
					echo "serverids.push(".$row_server['id'].");\n";
				}else
				{
					if($row_server['is_bookable'] == 1)
					{
						echo "allserverids.push(".$row_server['id'].");\n";		
					}else
					{
						echo "serverids.push(".$row_server['id'].");\n";
					}
				}
			}		
		}
		
?>
		<!-- update newbooking page server controls -->
		//

			


		for (i = 1; i < <?php echo $cnt;?>; i++)
		{
			window.opener.document.getElementById('cluster'+clustersid[i]).style.display = "block";	
		}

		for (i = 0; i < allserverids.length; i++)
		{
			window.opener.document.getElementById('inputserver'+allserverids[i]).disabled = false ;
			
			window.opener.document.getElementById('serverdiv'+allserverids[i]).style.color = "black" ;

		}	

		for (i = 0; i < serverids.length; i++)
		{
			window.opener.document.getElementById('inputserver'+serverids[i]).disabled = true ;
			window.opener.document.getElementById('serverdiv'+serverids[i]).style.color = "grey" ;
		}		
		window.close();		
		self.close();
		
		return false;
	}
	</script>
<?php
	//call function to get servers when page is loaded
	echo"</head><body onload=\"UpdateServer()\"></body></html>";
 ?>
