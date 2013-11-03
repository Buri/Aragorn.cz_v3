<?php

if ($LogedIn && isset($_GET['anketa']) && isset($_GET['volba']) && $link == "diskuze") {
	$anketa = addslashes($_GET['anketa']);
	$volba = addslashes($_GET['volba']);
	$moznosti = "";
	if (!ctype_digit($_GET['anketa']) || !ctype_digit($_GET['volba'])) {
		header ("Location: $inc/$link/$slink/");
		exit;
	}
	$jeAnketa = mysql_query("SELECT * FROM 3_ankety WHERE id = '$anketa' AND aktiv = '1'");
	if (mysql_num_rows($jeAnketa)>0) {
		$anketaO = mysql_fetch_object($jeAnketa);
		mysql_free_result($jeAnketa);
		$moznosti = explode(">", $anketaO->odpoved);
		$hlasoval = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_ankety_data WHERE uid = '$_SESSION[uid]' AND ank_id = $anketaO->id"));
		if ($hlasoval[0]==0) {
			if (isSet($moznosti[$volba]) && $_GET['volba'] != "") {
				mysql_query("INSERT INTO 3_ankety_data (uid,hlas,ank_id) VALUES ('$_SESSION[uid]','$volba','$anketaO->id')");
				header ("Location: $inc/$link/$slink/");
				exit;
			}
		}
	}
	else {
	}
}
else {
	header ("Location: $inc/diskuze/");
	exit;
}

?>
