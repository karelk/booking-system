<?php

//check if user is logged in
include "inc/session_check.php";

$title = "Server Details";
include "inc/config.php";

?>

<!-- Create page to list cluster and server details -->

 <html>
 <head>
 	<link rel="stylesheet" type="text/css" href="inc/style.css" /> 

	<script language="JavaScript" type="text/JavaScript">

	//function to hide/show server details
	menu_status = new Array();
	function showHide(theid,cnt){
		if (document.getElementById) {
			if(menu_status[theid] != 'show') {
				menu_status[theid] = 'show';
				document.getElementById('img_expand'+cnt).src = 'inc/minus.jpg';
				document.getElementById(theid).style.display = "";
			}else{
				menu_status[theid] = 'hide';
				document.getElementById('img_expand'+cnt).src = 'inc/plus.jpg';  
				document.getElementById(theid).style.display = "none";
			}
		}
	}
</script>
	
</head>
<body>


	<table class="menubar" width ="100%" border="0" style="font-size:15px;background-color:#ffffff;">
	<tr width ="100%">
	<td><a href="newbooking.php">New Booking</a> &nbsp;| 
    <a href="past.php">Past</a> &nbsp;
    <a href="index.php">Current</a> &nbsp;
    <a href="future.php">Future</a> &nbsp; | &nbsp;
    <a href="cluster.php">All Clusters</a> 
	<?php
	//if admin user then allow adding cluster/server
	if($_SESSION['user'] == "mainadmin")
	{
	    echo " &nbsp; <a href=\"addcluster.php\">Add Cluster</a> &nbsp; \n";
		echo "<a href=\"addserver.php\">Add Server</a>  \n";
	}
	?>
	</td>
	<td align="right">Logged in as : <?php echo $_SESSION['user'];?>&nbsp;&nbsp;&nbsp;<a href="login.php?out=o">Logout</a>
	</tr>
	</table>

	<?php

    echo "<center><p><b>".strtoupper($title)."</b><p></center>";
    echo "<center>";
?>

			<table border="1" style="background-color: #99c3e0;">
					<tr width="1200px;">
					<th  width="251px;" style="padding-left:10px;" >Cluster</th>
					<th  width="50px;" style="padding-left:10px;" >Total</th>
					<th  width="50px;" style="padding-left:10px;" >Free</th>
					<th  width="350px;" style="padding-left:10px;">Cpu</th>
					<th  width="240px;" style="padding-left:10px;">Core</th>
					<th  width="60px;" style="padding-left:10px;">Ram</th>
					<th  width="150px;" colspan="2" style="padding-left:10px;"></th>
				</tr>
			</table>


<?php
	$cluster_count=1;
	
	$db = db_connect();
	//get cluster details
	$query='SELECT id,name,cpu,cores,ram,is_bookable from cluster where is_bookable != 3';
	$res = $db->query($query);
	while($row = $res->fetch()) {

		$server_count=1;
		$total_server = 0;
		$ExtServers = array();

		//get total servers count
		$query1="SELECT id from server where cluster_id =".$row['id'];
		$res_exist = $db->query($query1);
		while($rowExt = $res_exist->fetch()) 
		{			
			$total_server++;
		}

		//get booked server id of particular cluster
		$query1="SELECT server.id AS servers FROM booking INNER JOIN booking_map ON booking.id = booking_map.booking_id INNER JOIN server ON booking_map.server_id = server.id WHERE NOW() <= booking.end AND booking.status=1 and server.is_bookable !=3 and server.cluster_id =".$row['id']." group by server.id";
		$res_exist = $db->query($query1);
			
		while($rowExt = $res_exist->fetch()) 
		{
			$ExtServers[] = $rowExt[0];
			$server_count++;		
		}

		?>
			<table border="1" style="font-family: verdana,arial,sans-serif;font-size:11px;">

				<tr width="1200px;">
					<td  width="250px;" style="background-color: #ebf2f6;cursor: pointer;padding-left:10px;" >
					<?php
						if($row['is_bookable'] == 1)
						{
					?>
						<img id="img_expand<?php echo $cluster_count;?>" src ="inc/plus.jpg"/> <a class="menu<?php echo $cluster_count;?>" onclick="showHide('mymenu<?php echo $cluster_count;?>',<?php echo $cluster_count;?>)"><?php echo $row['name'];?></a>
					<?php
						}else
						{
							echo $row['name'];
						}
					?>
					</td>
					<td  width="50px;" style="background-color: #ebf2f6;padding-left:10px;"><?php 
					$total_server = sprintf('%2d', $total_server); 
					echo $total_server;?></td>
					<?php $total_server = $total_server - ($server_count-1); 
					$total_server = sprintf('%2d', $total_server); 
					?>
					<td  width="50px;" style="background-color: #ebf2f6;padding-left:10px;"><?php echo $total_server;?></td>
					<td  width="350px;" style="background-color: #ebf2f6;padding-left:10px;"><?php echo $row['cpu'];?></td>
					<td width="240px;" style="background-color: #ebf2f6;padding-left:10px;"><?php echo $row['cores'];?></td>
					<td width="60px;" style="background-color: #ebf2f6;padding-left:10px;"><?php echo $row['ram'];?></td>

					<?php
						$opt = 1;
					//if admin user allow edit/delete
					if($_SESSION['user'] == "mainadmin")
					{
						echo"<td width=\"50px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><a href=\"editclusters.php?opt=".$opt."&cid=".$row['id']."\">Edit</a></td>";
						if($row['is_bookable'] == 1 && $server_count == 1)
						{
							echo"<td width=\"50px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><a href=\"deleteservers.php?cid=".$row['id']."&sid=\" onclick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
						}else
						{
							$opt = 0;
							echo"<td width=\"50px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><font color=\"grey\">Delete</font></td>";
						}
					}else
					{
						$opt = 0;
						echo"<td width=\"55px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><font color=\"grey\">Edit</font></td>";
						echo"<td width=\"75px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><font color=\"grey\">Delete</font></td>";


					}
					?>					
				</tr>		
			</table>
			<?php
			if($row['is_bookable'] == 1)
			{
				echo"<table class=\"fixed\"  id = \"mymenu".$cluster_count."\" border=\"1\" style=\"display:none\">";
				// get servers details.
				$query1="SELECT id,name,os,is_bookable from server where is_bookable != 3 and cluster_id=".$row[0];
				$res_server = $db->query($query1);
				while($row_server = $res_server->fetch()) 
				{ 
				?>
					<tr width="1100px;">
						<td width="250px;" style="background-color: #ebf2f6;padding-left:10px;"><?php echo $row_server['name'];?></td>
						<td width="500px;" style="background-color: #ebf2f6;padding-left:10px;"><?php echo $row_server['os'];?></td>
						<?php
						$opt = 1;
						if($_SESSION['user'] == "mainadmin")
						{
							echo"<td width=\"150px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><a href=\"editservers.php?opt=".$opt."&sid=".$row_server['id']."\">Edit</a></td>";

							if($row_server['is_bookable'] == 1)
							{
								if (in_array($row_server['id'], $ExtServers)) 
								{
									$opt = 0;
									echo"<td width=\"150px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><font color=\"grey\">Delete</font></td>";
								}else
								{
									echo"<td width=\"150px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><a href=\"deleteservers.php?sid=".$row_server['id']."&cid=".$row['id']."\"   onclick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
								}
							}else
							{
								$opt = 0;
								echo"<td width=\"150px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><font color=\"grey\">Delete</font></td>";
							}
						}else
						{
							$opt = 0;
							echo"<td width=\"150px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><font color=\"grey\">Edit</font></td>";
							echo"<td width=\"150px;\" style=\"background-color: #ebf2f6;padding-left:10px;\"><font color=\"grey\">Delete</font></td>";
						}
						?>
					</tr>

			<?php
				}
				echo"</table>";	
			}
		$cluster_count++;
	}

   echo "</center>";

  echo "</body>";

  show_footer();
?>
