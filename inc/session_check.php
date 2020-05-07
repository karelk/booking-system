<?php

//check is user is logged in
session_start();
if (!isset($_SESSION['user'])) {
	header('Location: login.php?out=o');
}
?>