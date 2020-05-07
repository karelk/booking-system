<?php

//check if user is logged in
include "inc/session_check.php";

include "inc/config.php";

//get booking id to be deleted
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	
	$db = db_connect();

	//mark mapping of server and booking as deleted
	$query = "update booking_map set status = 0 where booking_id  = $id";
	$res = $db->query($query);

	//mark booking as deleted
	$query = "update booking set status = 0 where id  = $id";
	$res = $db->query($query);

	//update end time to now
//	$query = "update booking set end = NOW() where id  = $id";
//	$res = $db->query($query);

	//redirect to index.php
	header('Location: index.php');
	die();
}
	