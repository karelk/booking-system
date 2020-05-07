<?php

//check if user is logged in
include "inc/session_check.php";
if ($_SESSION['user'] != "mainadmin") {
	header('Location: adminlogin.php');
	die();
}
include "inc/config.php";


//get cluster id
if (isset($_GET['cid']) and !empty($_GET['cid'])) {
	$cid = $_GET['cid'];
	echo "\nC = ".$cid."\n"; 

	$db = db_connect();
	
	//get server id
	if (isset($_GET['sid']) and !empty($_GET['sid'])) 
	{
		$sid = $_GET['sid'];
		//mark server as deleted
		$query = "update server set is_bookable=3 where id = ".$sid;
		$res = $db->query($query);

	}else
	{		
		//mark server as deleted in particular cluster
		$query = "update server set is_bookable=3 where cluster_id=".$cid;
		$res = $db->query($query);

		//mark cluster as deleted
		$query = "update cluster set is_bookable=3 where id=".$cid;
		$res = $db->query($query);
	}
	//redirect to cluster.php
	header('Location: cluster.php');
	die();
}
	