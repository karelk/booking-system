<?php
 
//check is user is logged in
include "inc/session_check.php";

//Create page to list past booking details
  $title = "Past Booking";
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
  echo "<th><b>status</b></th>\n";
  echo "</tr>\n";

//Get past booking details from booking and booking_map table
  $query='  SELECT 
		    booking.id AS id, booking.user AS user, booking.begin AS begin, booking.end AS end,
		    GROUP_CONCAT(server.name ORDER BY server.name SEPARATOR \', \') AS servers,
		    booking.comment AS comment, booking.status AS status
	      FROM 
		    booking
		    INNER JOIN booking_map ON booking.id = booking_map.booking_id
		    INNER JOIN server ON booking_map.server_id = server.id
                  WHERE booking.end > NOW() - INTERVAL 1 MONTH AND booking.end < NOW()
	  GROUP BY
		    booking.id';


  $res = $db->query($query);

//retrive booking data and display it in table
  while($row = $res->fetch()) {
		echo "<tr>";
		echo "<td width=\"35px;\">".$row['id']."</td>\n";
		echo "<td width=\"110px;\">".$row['user']."</td>\n";
		$bdt = $row['begin'];
		$bdt = substr($bdt, 0, -3);
		echo "<td width=\"120px;\">".$bdt."</td>\n";

		$edt = $row['end'];
		$edt = substr($edt, 0, -3);
		echo "<td width=\"120px;\">".$edt."</td>\n";
		echo "<td width=\"510px;\">".$row['servers']."</td>\n";
		echo "<td width=\"250px;\">".$row['comment']."</td>\n";

if($row['status'] == 1) {
		echo "<td width=\"50px;\">expired</td>\n";
} else
{
		echo "<td width=\"50px;\"><font color=\"red\">deleted</font></td>\n";
}

		echo "</tr>";


  }


  echo "</table>";
  echo "</center>";
  echo "</body>";

  show_footer();
?>
