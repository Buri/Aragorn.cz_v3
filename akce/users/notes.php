<?php

if (($LogedIn == true) && (strlen($_POST['mess']) > 0) && (do_seo($_SESSION['login']) == $slink)){

	$meesa = addslashes($_POST['mess']);
	mysql_query("UPDATE 3_notes SET text='$meesa' WHERE uid = $_SESSION[uid]");

	if (mysql_affected_rows()>0){
	}else{
		mysql_query("INSERT INTO 3_notes (uid,text) VALUES ($_SESSION[uid],'$meesa')");
	}

	Header ("Location:$inc/uzivatele/".$slink."/#poznamky");
	exit;

}
else {
	Header ("Location:$inc/uzivatele/".do_seo($_SESSION['login'])."/");
	exit;
}
?>