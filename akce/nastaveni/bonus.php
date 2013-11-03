<?php
// bonus - faze zadosti

if ($_SESSION['lvl'] < 2) {
	if ($_POST['bonus'] > 0) {
		$var = 1;
		$_SESSION['lvl'] = 1;
	  $add = "?bon=".intval($_POST['bonus']);
	}else{
	  $var = 0;
		$_SESSION['lvl'] = 0;
	  $add = "?ok=1";
	}
	
	mysql_query ("UPDATE 3_users SET level = '$var' WHERE id = '$_SESSION[uid]'");

	//uspesny redirect
	Header ("Location:$inc/bonus/$add");
	exit;
}
else {
	Header("Location:$inc/bonus/");
	exit;
}
?>
