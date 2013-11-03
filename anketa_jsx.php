<?php
session_start();
if (isset($_SESSION["uid"]) && isset($_GET['anketa']) && isset($_GET['volba'])) {
	$anketa = addslashes(intval($_GET['anketa']));
	$volba = addslashes(intval($_GET['volba']));
	$moznosti = "";
	include "./db/conn.php";
	$jeAnketa = mysql_query("SELECT * FROM 3_ankety WHERE id = '$anketa' AND aktiv = '1'");
	if (mysql_num_rows($jeAnketa)>0) {
		$anketaO = mysql_fetch_object($jeAnketa);
		mysql_free_result($jeAnketa);
		$moznosti = explode(">", $anketaO->odpoved);
		$hlasoval = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_ankety_data WHERE uid = '$_SESSION[uid]' AND ank_id = $anketaO->id"));
		if ($hlasoval[0]==0) {
			if (isSet($moznosti[$volba]) && $volba != "" && ctype_digit($volba)) {
				mysql_query("INSERT INTO 3_ankety_data (uid,hlas,ank_id) VALUES ('$_SESSION[uid]','$volba','$anketaO->id')");
			}
		}
	}
	else {
		$anketa = 0;
	}
}else {
	$anketa = 0;
}

header("Content-Type: text/xml");
echo "<anketa>\n";
if ($anketa > 0) {
	$pocty = mysql_query("SELECT count(*) AS pocet, hlas FROM 3_ankety_data WHERE ank_id = '$anketa' GROUP BY hlas ORDER BY hlas ASC");
	$hlasy = array_fill(0, 20, 0);
	$hlasyAll = 0;
	if (mysql_num_rows($pocty)>0) {
		while ($hlasOne = mysql_fetch_object($pocty)) {
			$hlasy[$hlasOne->hlas] = $hlasOne->pocet;
			$hlasyAll += $hlasOne->pocet;
		}
		mysql_free_result($pocty);
		for ($i=0;$i<count($moznosti);$i++) {
			echo "<odpoved id='pocet$i'>$hlasy[$i]</odpoved>\n";
		}
	}
	$konc = "ů";
	if ($hlasyAll == 1) {
		$konc = "";
	}
	elseif ($hlasyAll > 1 && $hlasyAll < 5) {
		$konc = "y";
	}
	echo "<odpovedelo>$hlasyAll hlas$konc</odpovedelo>\n";
}
echo "</anketa>\n";
?>
