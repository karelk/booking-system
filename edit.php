<?php

//check is user is logged in
include "inc/session_check.php";

  $title = "edit";
  include "inc/config.php";

  $db = db_connect();
  $user ="";
  $all_servers="";
//get booking id to be edited

  if ($_GET["id"] > 0)
    $db_id = $_GET["id"];    
  elseif ($_POST["id"] > 0)
    $db_id = $_POST["id"];



   

  // check if saving, then update the data
  if ($_POST["save"] == 1) {
	  $usr_exist = 0;	
	  if ($_SESSION['user'] != 'mainadmin') 
	  {
     	$usr_exist = 1;
		$user = $_SESSION['user'];
	  }

	  $edt = $_POST['edate'];
	  $ett = $_POST['etime'];
	  $edt = $edt." ".$ett.":59";

	  $tkn = array();
	  if (isset($_POST['tokenize'])) 
	  {	
		$tkn = $_POST['tokenize'];
	  }

	  $user_count = count($tkn);
	  for($count=0;$count < $user_count;$count++)
	  {
		  if($usr_exist == 1 )
		  {
			$user = $user.",".$tkn[$count];
		  }else
		  {
			  $user = $tkn[$count];
		  }
		  $usr_exist = 1;	 		
	  }

	  //update booking data in table
      $db_query="UPDATE booking SET user=\"".$user."\", begin=\"".$_POST['begin']."\", end=\"".$edt."\", comment=\"".$_POST['comment']."\" WHERE id=".$_POST["id"];
      $res = $db->query($db_query);

  	  $servers = array();
	  $servers = $_POST['serv'];  	  

	  $serversid = array();
	  $serversname = array();
	  //get servers mapped to booking
      $db_query="select server_id from booking_map WHERE booking_id=".$_POST["id"];
	  $res = $db->query($db_query);
	  $cnt = 0;	
	  while($row1 = $res->fetch()) {
		 $serversid[$cnt] = $row1[0];
		 $cnt++;
	  }

	  for($count=0;$count < count($serversid);$count++){


		if (!in_array($serversid[$count], $servers)){
			//delete servers from mapped to booking if servers is removed
			 $db_query="delete from booking_map WHERE server_id = ".$serversid[$count]." and booking_id=".$_POST["id"];
      		 $res = $db->query($db_query);		    
		}else
		  {
			 $db_query="select name from server WHERE id=".$serversid[$count];
			  $res = $db->query($db_query);
			  while($row1 = $res->fetch()) {
				$all_servers = $all_servers." ".$row1[0];
			  }
		  }
	  }

//check if sending mail is enable/checked
	if (isset($_POST['semail']) && isset($_SESSION['mailid'])) 
		{

			//read all user's for sending mail
			$tkn = array();
			if (isset($_POST['tokenize'])) 
			{	
				$tkn = $_POST['tokenize'];
			}

			if ($_SESSION['user'] != 'mainadmin') 
			{		
				$tkn[count($tkn)] = $_SESSION['user'];
			}
			$user_count = count($tkn);

			//set subject of mail
			$email_subject = "Your server booking details updated by ".$_SESSION['user'];

			function died($error) 
			{					
			}

			//check if data is any bad string like cc etc. 
			function clean_string($string) {
			  $bad = array("content-type","bcc:","to:","cc:","href");
			  return str_replace($bad,"",$string);
			}
					
			//iterate for each user.
			for($count=0;$count < $user_count;$count++)
			{
					//create email id for user.
					$email_to = $tkn[$count]."@your-domain.org";

					$email_from = "admin@your-server.your-domain.org";
					
					$error_message = "Dear ".$tkn[$count]."\n";
					$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
					
					//Check for valid email
					if(!preg_match($email_exp,$email_from)) {
						$error_message .= 'The Email Address you entered does not appear to be valid.<br />';
					}
					if(strlen($error_message) > 0) {
						died($error_message);
					}

					//create email body.
					$email_message = "Updated booking details is given below.\n\n";
					$email_message .= " Users : ".clean_string($user)."\n";
					$email_message .= " Begin Date : ".clean_string($_POST['begin'])."\n";
					$email_message .= " End Date : ".clean_string($_POST['end'])."\n";
					$email_message .= " Servers: ".clean_string($all_servers)."\n";
					$email_message .= " Comment: ".clean_string($_POST['comment'])."\n";

					// create email headers
					$headers = 'From: '.$email_from."\r\n".
					'Reply-To: '.$email_from."\r\n" .
					'X-Mailer: PHP/' . phpversion();

					// send mail
					@mail($email_to, $email_subject, $email_message, $headers);
			}
		}
	  //redirect to index.php
	  header("Location:index.php");
  }

  // show booking data and the form
  $db_query = "SELECT * FROM booking WHERE id = ".$db_id;
  $res = $db->query($db_query);
  $db_row = $res->fetch();

  show_header_start();


?>
	
		<link rel="stylesheet" type="text/css" href="inc/style.css" />
		<link rel="stylesheet" type="text/css" href="inc/datepicker.css" /> 
		<link rel="stylesheet" type="text/css" href="inc/jquery.tokenize.css" /> 
		<script type="text/javascript" src="inc/datepicker.js"></script> 
		<script type="text/javascript" src="inc/jquery.min.js"></script>
		<script type="text/javascript" src="inc/jquery.tokenize.js"></script>
		<script language="JavaScript" type="text/JavaScript">
		function validate(begindate,enddate)
		{
			if(document.getElementById("isadmin").value == 1)
		   {
				if (editbooking.elements["tokenize[]"].selectedIndex == -1) 
				{
					alert("Please select user");	
					return false;
				}
		   }

		   if (editbooking.elements["serv[]"].selectedIndex == -1) 
			{
					alert("Cannot delete all servers. Atleast 1 server should be included in booking.");	
					return false;
			}
			
			var edt =  document.getElementById("edate").value;

			var ett = document.getElementById("etime").value;

			edt = edt + " " + ett + ":59:00"; 
			if(enddate < edt)
			{
				alert("End date should be less/equal to " + enddate );
				return false;
			}
			if(edt < begindate)
			{
				alert("End date should be greater then " + begindate );
				return false;
			}
			
		   return true;
		}
		</script>

	<?php

		
		show_header_end();
		echo "<body>";

		show_menu();

		echo "<center>";
		echo "<form action=\"edit.php\" id =\"editbooking\" name=\"editbooking\" onsubmit=\"return validate('".$db_row['begin']."','".$db_row['end']."')\"  method=\"POST\">";
		echo "<table border=1>\n";
		echo "<tr><th>id</th><td>".$db_row['id']."</td></tr>\n";
		echo "<tr><th>user   </th><td>";

		if($_SESSION['user'] != "mainadmin") { ?>  <input type="button" height="100%" value = "<?php echo $_SESSION['user'];?>" disabled/><?php } ?>
       <select id="tokenize" name="tokenize[]" multiple="multiple"  class="tokenize-sample">
		<?php
		//read imap user from users.txt
		$usr = array();
		$usr = explode(",",$db_row['user']);		
	   $handle = fopen("inc/users.txt", "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$line = rtrim($line, "\r\n");
				if($line != $_SESSION['user'])
				{					
					if (in_array($line, $usr))
					{	
						echo"<option value=\"".trim($line)."\" selected>".$line."</option>\n";						  
					}else
					{
					echo"<option value=\"".trim($line)."\">".$line."</option>\n";
					}
				}
			}
			fclose($handle);
		}
?>
		
	</select>
<script type="text/javascript">
    $('#tokenize').tokenize();	
</script>
     </td>  
  </tr>
  <?php
  echo "<tr><th>begin  </th><td>".$db_row['begin']."</td></tr>\n";
  echo "<tr><th>end    </th><td><input type='text' style=\"width:90px;\" class='datepicker' name='edate' id='edate' value=";
  $dt = date('Y-m-d', strtotime($db_row['end']));
  echo $dt;
  echo " required />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
  
	$hr = explode(" ",$db_row['end']);
	$hr = $hr[1];
	$hr = explode(":",$hr);
	$hr = $hr[0];
	echo "<div style=\"display:inline\"><select name= 'etime' id='etime' required>";
	for($i=0;$i<24;$i++)
	{
		$i = sprintf('%02d', $i); 
		if($i == $hr)
		{
			echo "<option value=\"".$i."\" selected>".$i.":59</option>\n";
		}else
		{
			echo "<option value=\"".$i."\">".$i.":59</option>\n";
		}
	}
  echo"</td></tr>\n";
  echo "<tr><th>servers</th><td>";
   echo "<select id=\"serv\" name=\"serv[]\" multiple=\"multiple\" required class=\"tokenize-sample\">";
   //get only servers mapped to booking
  $db_query = "SELECT server.name,server.id FROM server,booking_map WHERE ((server.id = booking_map.server_id) AND (booking_map.booking_id = ".$db_row['id']."))";
  $res1 = $db->query($db_query);

  
  while($row1 = $res1->fetch()) { 	   
		echo"<option value=\"".$row1['id']."\" selected>".$row1['name']."</option>\n";						  	  
	  }

?>
		
	</select>
<script type="text/javascript">
    $('#serv').tokenize();	
</script>

     </td></tr>
<?php
  echo "<tr><th>comment</th><td><textarea name=\"comment\" rows=5 cols=50>".$db_row['comment']."</textarea>";
   
		if (isset($_SESSION['mailid'])) 
		{ ?>
			<div style="padding-left:10px;">Send Email : <input id="" type="checkbox" name ="semail" value ="1"><div>
		<?php 
		}	
		

  echo"</td></tr>\n";

    // submit lines
  echo "<tr><th></th><td>\n";
  echo "<input type=\"submit\" value=\"save\">\n";
  echo "<input type=\"hidden\" name=\"isadmin\" id=\"isadmin\" value=\"";
	if($_SESSION['user'] == "mainadmin") 
	{
		echo "1";
	}else
	{
		echo "0";
	}
	echo"\">\n";
  echo "<input type=\"hidden\" name=\"save\" value=\"1\">\n";
  echo "<input type=\"hidden\" name=\"begin\" value=\"".$db_row['begin']."\">\n";

  if ($db_id != NULL) {
      echo "<input type=\"hidden\" name=\"id\" value=\"".$db_id."\">\n";
  }

  echo "</td></tr>\n";
  echo "</table>\n";
  echo "</center>";
 echo "</body>";
 echo "</html>";
 
?>
