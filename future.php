<?php

//check is user is logged in
include "inc/session_check.php";

//Create page to list future booking details
  $title = "Future Booking";
  include "inc/config.php";
  show_header_start();
  show_header_end();
  
  echo "<body>";
  show_menu();
  
  $db = db_connect();

  echo "<center>";
  echo "<table border=1 style=\"font-size: 9pt;\">\n";
  echo "<tr>\n";
  echo "<th><b>id</b></th>\n";
  echo "<th><b>user</b></th>\n";
  echo "<th><b>begin</b></th>\n";
  echo "<th><b>end</b></th>\n";
  echo "<th><b>servers</b></th>\n";
  echo "<th><b>comment</b></th>\n";
  echo "<th>delete</th>";
  echo "</tr>\n";

//Get future booking details from booking and booking_map table
  $query='  SELECT 
		    booking.id AS id, booking.user AS user, booking.begin AS begin, booking.end AS end,
		    GROUP_CONCAT(server.name ORDER BY server.name SEPARATOR \', \') AS servers,
		    booking.comment AS comment
	      FROM 
		    booking
		    INNER JOIN booking_map ON booking.id = booking_map.booking_id
		    INNER JOIN server ON booking_map.server_id = server.id
	     WHERE
		    booking.begin > NOW() AND booking.status=1
	  GROUP BY
		    booking.id';

  $res = $db->query($query);

  while($row = $res->fetch()) {
		echo "<tr>";
		echo "<td width=\"30px;\">".$row['id']."</td>\n";
		echo "<td width=\"200px;\">".$row['user']."</td>\n";

		$bdt = $row['begin'];
		$bdt = substr($bdt, 0, -3);
		echo "<td width=\"120px;\">".$bdt."</td>\n";

		$edt = $row['end'];
		$edt = substr($edt, 0, -3);
		echo "<td width=\"120px;\">".$edt."</td>\n";

		echo "<td width=\"480px;\">".$row['servers']."</td>\n";
		echo "<td width=\"150px;\">".$row['comment']."</td>\n";

		$user = array();
		$user = explode(",",$row['user']);

	    //if admin user or owner, allow to edit and delete
		if ((in_array($_SESSION['user'], $user)) || ($_SESSION['user'] == 'mainadmin') || ($_SESSION['user'] == 'karelk'))
		{	
			echo "<td width=\"60px;\"><a href=\"delete.php?id=".$row['id']."\">delete</a></td>\n";
		}else
		{
			echo "<td width=\"60px;\"><font color=\"grey\">delete</font></td>\n";	
		}
		echo "</tr>";
  }

  echo "</table>";
  echo "</center>";
echo "</body>";

  show_footer();
?>
