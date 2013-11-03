<?php
	setcookie("enquiry_vote", "", -100, "/", "http://kristalova.lupa.cz");
	session_start();
	include "./db/conn.php";
	if (isset($_SESSION['uid'])){
		mysql_query("UPDATE 3_users SET hlasoval = '1' WHERE id = $_SESSION[uid] AND hlasoval = '0'");
		if (mysql_affected_rows() > 0) echo "ok";
		else echo "error";
	}
?>