<?php

  function db_connect()
  {
    try {
      $handle = new PDO("mysql:host=localhost;dbname=booking;charset=latin2", 'localhost', 'YOUR-MYSQL-PASSWORD');
    }
    catch(PDOException $e) {
      Die('ERR');
      echo $e->getMessage();
    }
    return $handle;
  }

 function show_header_start()
 {
	echo "<html>";
    echo "<head>";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"inc/style.css\">";

 }

 function show_header_end()
 { 
	echo "</head>";	
 }


function show_menu()
  {
    global $title;
?>
	
	<table class="menubar" width ="100%" border="0" style="font-size:15px;background-color:#ffffff;">
	<tr width ="100%">
	<td><a href="newbooking.php">New Booking</a> &nbsp;| 
    <a href="past.php">Past</a> &nbsp;
    <a href="index.php">Current</a> &nbsp;
    <a href="future.php">Future</a> &nbsp; | &nbsp;
    <a href="cluster.php">All Clusters</a></td>
	<td align="right">Logged in as : <?php echo $_SESSION['user'];?>&nbsp;&nbsp;&nbsp;<a href="login.php?out=o">Logout</a>
	</tr>
	</table>

<?php
    echo "<center><p><b>".strtoupper($title)."</b><p></center>";
    echo "<center><p>all times are CET (Zurich, Switzerland)<p></center>";

  }

  function show_footer()
  {
    echo "</html>";
  }    


?>
