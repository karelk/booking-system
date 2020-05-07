<?php
session_start();
if (!isset($_SESSION['user'])) {
	header('Location: login.php?out=o');
}

  $title = "Free";
  include "inc/config.php";
  show_header();
    $db = db_connect();

  echo "<center>";
  echo "<table border=1 style=\"font-size: 9pt;\">\n";
  echo "<tr>\n";
  echo "<th><b>cluster</b></th>\n";
  echo "<th><b>total</b></th>\n";
  echo "<th><b>free</b></th>\n";
  echo "<th><b>cpu</b></th>\n";
  echo "<th><b>cores</b></th>\n";
  echo "<th><b>ram</b></th>\n";
  echo "<th><b>comment</b></th>\n";
  echo "<th>show</th>";
  echo "</tr>\n";


  $query='SELECT 
cluster.id, cluster.name, COUNT(server.cluster_id) AS available FROM cluster 
INNER JOIN server ON cluster.id = server.cluster_id AND server.id NOT IN    
(SELECT booking_map.server_id FROM booking INNER JOIN booking_map ON booking.ID = booking_map.booking_ID WHERE NOW() BETWEEN booking.BEGIN AND booking.END AND booking.status=1) 
GROUP BY cluster.name ORDER BY cluster.name ';


  $res = $db->query($query);


  while($row = $res->fetch()) {
      echo "<tr>";
      echo "<td>".$row['name']."</td>\n";
      echo "<td>".$row['total']."</td>\n";
      echo "<td>".$row['free']."</td>\n";
      echo "<td>".$row['cpu']."</td>\n";
      echo "<td>".$row['cores']."</td>\n";
      echo "<td>".$row['ram']."</td>\n";
      echo "<td>".$row['comment']."</td>\n";
      echo "<td><a href=\"cluster.php?id=".$row['id']."\">show</a></td>\n";
      echo "</tr>";
  }

  echo "</table>";
  echo "</center>";


  show_footer();
?>
