<?php

//check is user is logged in
include "inc/session_check.php";

include "inc/config.php";

//check if request for new booking is submitted along with data ()
if (isset($_POST['servers'])) {
	//get all posted data
	$servers = array();
	$usr_exist = 0;	
	if ($_SESSION['user'] != "mainadmin") 
	{
     	$usr_exist = 1;
		$user = $_SESSION['user'];
	}
	$servers = $_POST['servers'];
	$date = $_POST['bdate'];
	$btimeh = $_POST['btimeh'];
	$edate = $_POST['edate'];
	$etimeh = $_POST['etimeh'];

	$tkn = array();
	if (isset($_POST['tokenize'])) 
	{	
		$tkn = $_POST['tokenize'];
	}
	
	//format all user's
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

	//format begin date and end date
	$date = date("Y-m-d",strtotime($date));
	$edate = date("Y-m-d",strtotime($edate));

	if($etimeh != "now")
	{
		$etimeh = $etimeh.":59:59";
	}else
	{
		$etimeh = date('H:i').":59";
	}

	if($btimeh != "now")
	{
		$btimeh = $btimeh.":00:00";
	}else
	{
		$btimeh = date('H:i').":00";
	}

	$date = $date." ".$btimeh;
	$edate = $edate." ".$etimeh;
	$comment = $_POST['comment'];
	
	$db = db_connect();
		
	//create query string to insert booking data in table
	$query="insert into booking (user,begin,end,comment,status) values ('$user','$date','$edate','$comment',1)";
	$res = $db->query($query);

	$id = -1;
	//retrieve id of newly created boking
	$query="select id from booking where user='".$user."' and begin='".$date."'";
	$res = $db->query($query);
	while($row = $res->fetch()) 
	{
		$id = $row[0];
	}

	$all_servers="";
	if($id >=0 )
	{
		$server_count = count($servers);
		for($count=0;$count < $server_count;$count++)
		{
			//create server mapping of newly created booking
			$query="insert into booking_map (booking_id,server_id,status) values ($id,$servers[$count],1)";
			$res = $db->query($query);

			//get name of the servers selected in new booking
			$query1="SELECT name from server where is_bookable != 3 and id =".$servers[$count];
			$res = $db->query($query1);
			while($row = $res->fetch()) 
			{
				$all_servers = $all_servers." ".$row[0];
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

			if ($_SESSION['user'] != "mainadmin") 
			{
				$tkn[count($tkn)] = $_SESSION['user'];
			}
			$user_count = count($tkn);

			//set subject of mail
			$email_subject = "Your server booking details";

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
					$email_message = "Booking details below.\n\n";
					$email_message .= " Users : ".clean_string($user)."\n";
					$email_message .= " Begin Date : ".clean_string($date)."\n";
					$email_message .= " End Date : ".clean_string($edate)."\n";
					$email_message .= " Servers: ".clean_string($all_servers)."\n";
					$email_message .= " Comment: ".clean_string($comment)."\n";

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
	
	//create new booking page	
	$title = "New Booking";
	
	show_header_start();
	?>
	<link rel="stylesheet" type="text/css" href="inc/style.css" /> 
	<link rel="stylesheet" type="text/css" href="inc/datepicker.css" /> 
	<link rel="stylesheet" type="text/css" href="inc/jquery.tokenize.css" /> 
	<script type="text/javascript" src="inc/jquery.min.js"></script>
	<script type="text/javascript" src="inc/jquery.tokenize.js"></script>
	<script type="text/javascript" src="inc/datepicker.js?v=<?php echo microtime(); ?>"></script> 
	<script language="JavaScript" type="text/JavaScript">
	<!--

	$(window).bind("load", function() {
	  RetrieveServer();
	});

	menu_status = new Array();
	// function to show annd hide cluster/server
	function showHide(theid,cnt){		
		if (document.getElementById) {
		var switch_id = document.getElementById(theid);
		document.getElementById("serverdiv20").style.colr = "blue";
			

			if(menu_status[theid] != 'show') {
				switch_id.className = 'show';
				menu_status[theid] = 'show';
				document.getElementById('img_expand'+cnt).src = 'inc/minus.jpg';
			}else{
				switch_id.className = 'hide';
				menu_status[theid] = 'hide';

				document.getElementById('img_expand'+cnt).src = 'inc/plus.jpg';   			   
			}
		}
	}

	//function to convert date to yyyymmdd format
	var convertDate = function(usDate) {
	  var dateParts = usDate.split(/(\d{1,4})-(\d{1,2})-(\d{2})/);
	  return (dateParts[1]<10?"0"+dateParts[1]:dateParts[1]) + dateParts[2] + dateParts[3] ;
	}

	//open pop-up to retrieve server list from server available in begin and end date range
	function RetrieveServer()
	{

		var bdt = document.getElementById("bdate").value;

		var begindateresult = convertDate(bdt);		
		var btt = document.getElementById("btimeh").value;
		var bttsend = btt;

		if(btt == 'now')
		{
			var d = new Date(),
			h = (d.getHours()<10?'0':'') + d.getHours(),
		    m = (d.getMinutes()<10?'0':'') + d.getMinutes();
			s = (d.getSeconds()<10?'0':'') + d.getSeconds();
			btt = h+m+s;
		}

		begindateresult = begindateresult + btt;

		var edt = document.getElementById("edate").value;

		var enddateresult =  convertDate(edt);

		var ett = document.getElementById("etimeh").value;
		var ettsend = ett;
		if(ett == 'now')
		{
			var d = new Date(),
			h = (d.getHours()<10?'0':'') + d.getHours(),
		    m = (d.getMinutes()<10?'0':'') + d.getMinutes();
			s = (d.getSeconds()<10?'0':'') + d.getSeconds();
			ett = h+m+s;
		}

		enddateresult = enddateresult + ett;

		if(begindateresult >= enddateresult)
		{
		   alert ("InValid Dates. End date should be greater than begin date!!!");
		   return false;
	   }

   		ins=document.getElementsByName('servers[]');
		for (i = 0; i < ins.length; i++)   
		{
			ins[i].disabled = "true";
		}

		window.open("servers.php?bdate="+bdt+"&btime="+bttsend+"&edate="+edt+"&etime="+ettsend,"Servers","scrollbars=1,width=550,height=400,left=50,top=50,toolbar=0,resizable=no,status=1"); 			
	}

 	/* function to check if server is checked*/
   function validate()
   {

	   	var bdt = document.getElementById("bdate").value;

		var begindateresult = convertDate(bdt);		
		var btt = document.getElementById("btimeh").value;

		if(btt == 'now')
		{
			var d = new Date(),
			h = (d.getHours()<10?'0':'') + d.getHours(),
		    m = (d.getMinutes()<10?'0':'') + d.getMinutes();
			s = (d.getSeconds()<10?'0':'') + d.getSeconds();
			btt = h+m+s;
		}

		begindateresult = begindateresult + btt;
		
		var edt = document.getElementById("edate").value;

		var enddateresult =  convertDate(edt);

		var ett = document.getElementById("etimeh").value;

		if(ett == 'now')
		{
			var d = new Date(),
			h = (d.getHours()<10?'0':'') + d.getHours(),
		    m = (d.getMinutes()<10?'0':'') + d.getMinutes();
			s = (d.getSeconds()<10?'0':'') + d.getSeconds();
			ett = h+m+s;
		}

		enddateresult = enddateresult + ett;

		if(begindateresult >= enddateresult)
		{
		   alert ("InValid Dates. End date should be greater than begin date!!!");
		   return false;
	   }

	   if(document.getElementById("isadmin").value == 1)
	   {
			if (new_booking.elements["tokenize[]"].selectedIndex == -1) 
			{
				alert("Please select user");	
				return false;
			}
	   }
	 	
		ins=document.getElementsByName('servers[]');
		for (i = 0; i < ins.length; i++)   
		{
			if(ins[i].checked)
			{				
				return true;
			}
		}
		alert("Please select server");		

		return false;
   }		
	</script>
		
	<?php
	show_header_end();
echo "<body>";
	show_menu();

?>
<center>
	<form id='new_booking' name='new_booking' action='newbooking.php' onsubmit="return validate()" method='post' />

<table class="fixed">
<tr>
	<!-- date picker control linked with input box -->
	<td width="100"><div style="padding-left:10px;">Begin Date : </div></td>
	<td width="400"><div style="display:inline;padding-left:20px;"><input type='text' style="width:90px;" class='datepicker' name='bdate' id='bdate' value="<?php echo date("Y-m-d");?>" required /></div>
	<div style="display:inline;padding-left:75px;">Begin Time : </div>
		<!-- populate time from 0 to 23 hrs -->
		<?php 

				$t=time();
				$hr = date("h",$t);
				$min = date("m",$t);
				echo "<div style=\"display:inline\"><select name= 'btimeh' id='btimeh' required>";
				for($i=0;$i<24;$i++)
				{
					$i = sprintf('%02d', $i); 					
					echo "<option value=\"".$i."\">".$i.":00</option>\n";					
				}
				echo "<option value=\"now\" selected>Now</option>\n";
				echo"</select></div>";
				
			?>
</td></tr>
</table>
<table class="fixed">
<tr>
	<td width="100"><div style="padding-left:10px;">End Date : </div></td>
	<td width="400"><div style="display:inline;padding-left:20px;">
	<input type='text' style="width:90px;" class='datepicker' name='edate' id='edate' value="<?php $dt = date('Y-m-d', strtotime('+2 Hours'));echo $dt;?>" required /></div>
	<div style="display:inline;padding-left:85px;">End Time : </div>

			<?php 
				echo "<div style=\"display:inline\"><select name ='etimeh' id='etimeh' required>";
				for($i=0;$i<24;$i++)
				{
					$i = sprintf('%02d', $i); 
					if($i == 23)
					{
						echo "<option value=\"".$i."\" selected>".$i.":59</option>\n";
					}else
					{						
						echo "<option value=\"".$i."\">".$i.":59</option>\n";
					}
				}
				echo"</select></div>";			
		?>
</td></tr>
</table>
<table class="fixed">
<tr>
	<!-- get all user from users.txt file and allow addition of user through tag -->
	<td width="100" ><div style="padding-left:10px;">User : </div></td>
	<td width="400"><?php if($_SESSION['user'] != "mainadmin"){?><div style="display:inline;"><input type="button" height="100%" value = "<?php echo $_SESSION['user'];?>" disabled/>&nbsp;</div><?php } ?><div style="display:inline">	
       <select id="tokenize" name="tokenize[]" multiple="multiple"  class="tokenize-sample">
		<?php
	   $handle = fopen("inc/users.txt", "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$line = rtrim($line, "\r\n");
				if($line != $_SESSION['user'])
				{
					echo"<option value=\"".trim($line)."\">".$line."</option>\n";
				}
			}

			fclose($handle);
		}
		?>
		
	</select>

	<!-- initialize select tag -->
	<script type="text/javascript">
		$('#tokenize').tokenize();
	</script>

	</div>
	</td></tr>
</table>
	<table class="fixed">
<tr><td width="100"><div style="padding-left:10px;">Cluster : </div></td>
	<td width="400"><div  style="display:inline;padding-left:20px;"><input type='button' name='selser' id='sel' value="Retrieve Server" onclick="RetrieveServer()" /> </div>
	<div style="padding-left:20px;">(Retrieve servers when begin and end date is changed/updated) </div>
	<div>&nbsp;</div>


	<div>&nbsp;</div>
	<!-- retrieve all the servers available within selected begin and end date -->

<?php
		$cluster_count=1;
		$server_count=1;
		$db = db_connect();
		$query='SELECT id,name,is_bookable from cluster where is_bookable != 3 order by id';
		$res = $db->query($query);
		while($row = $res->fetch()) {				
//			echo"<div id=\"cluster".$row['id']."\" style=\"cursor: pointer;display:none;padding-left:20px;\"> <img id=\"img_expand".$cluster_count."\" src =\"inc/plus.jpg\"/> <a class=\"menu".$cluster_count."\"";if($row['is_bookable'] == '0'){echo" disabled><font color=\"grey\">".$row['name']."</font></a></div>";}else{ echo " onclick=\"showHide('mymenu".$cluster_count."',".$cluster_count.")\">".$row['name']."</a></div>\n";}
			echo"<div id=\"cluster".$row['id']."\" style=\"cursor: pointer;display:none;padding-left:20px;\"> <a class=\"menu".$cluster_count."\"";if($row['is_bookable'] == '0'){echo" disabled><font color=\"grey\">".$row['name']."</font></a></div>";}else{ echo " onclick=\"showHide('mymenu".$cluster_count."',".$cluster_count.")\">".$row['name']."</a></div>\n";}

//			echo"<div id=\"mymenu".$cluster_count."\" class=\"hide\">";


			$query1="SELECT id,name,is_bookable from server where cluster_id=".$row['id'];
			$res_server = $db->query($query1);
			while($row_server = $res_server->fetch()) { 				
				echo"<div id=\"serverdiv".$row_server['id']."\"  style=\"margin-left:20px;\"><input id=\"inputserver".$row_server['id']."\" type=\"checkbox\""; echo "disabled";	echo" name =\"servers[]\" value =\"".$row_server['id']."\">".$row_server['name']."</div>\n";	
				$server_count++;
			}
			$cluster_count++;
			?>
			</div>					
		<?php
		};
		?>

	</td></tr>
	</table>
    <table class="fixed">
	<tr>
		<td width="100"><div style="padding-left:10px;">Comment : </div></td>
		<td width="400"><div style="display:inline;padding-left:20px;"><textarea name="comment" id="comment"  rows=6 cols=42 required></textarea></div>
		<!-- option to enable mail send -->
		<?php 
		if (isset($_SESSION['mailid'])) 
		{ ?>
			<div style="padding-left:20px;">Send Email : <input id="" type="checkbox" name ="semail" value ="1"><div>
		<?php 
		}	
		?>
	</td></tr>
	</table>
	<table class="fixed">
	<tr ><td width="517" colspan=2 ><div style="padding-left:220px;"><input type="submit" name="submit" value="Book Now"/><input type="hidden" id ="isadmin" name="isadmin" value="<?php 
	if($_SESSION['user'] == "mainadmin") 
	{
		echo "1";
	}else
	{
		echo "0";
	}
	?>"/></div>
	</td></tr>
	</table>
	</form>
	</center>
	</body>
<?php  
  show_footer();
  
?>
