<?php

function end_ankety($id,$moznosti) {
	$pocty = mysql_query("SELECT count(*) AS pocet, hlas FROM 3_ankety_data WHERE ank_id = '$id' GROUP BY hlas ORDER BY hlas ASC");
	$hlasy = array_fill(0, count($moznosti), 0);
	$hlasyAll = 0;
	if (mysql_num_rows($pocty)>0) {
		while ($hlasOne = mysql_fetch_object($pocty)) {
			$hlasy[$hlasOne->hlas] = $hlasOne->pocet;
		}
		mysql_free_result($pocty);
	}
	$hlasy = join(">",$hlasy);
	mysql_query("UPDATE 3_ankety SET counts = '$hlasy', aktiv='0' WHERE id = '$id'");
	mysql_query("DELETE FROM 3_ankety_data WHERE ank_id = '$id'");
}


if ($LogedIn && isset($_GET['do']) && $link == "diskuze" && $dFound == true) {
  $AllowedTo = GetPravaHere($dItem->id, $dItem->owner, $dItem->prava_reg, $dItem->prava_guest, $LogedIn);
  if ($AllowedTo != "superall" && $AllowedTo != "all") {
  	Header ("Location: $inc/diskuze/$slink/");
  	exit;
  }

  if ($_GET['do'] == "smazat" && isset($_GET['anketa'])) {
  	$anketa = addslashes($_GET['anketa']);
  	$anketaEx = mysql_query("SELECT * FROM 3_ankety WHERE id = '$anketa' AND dis = '$dItem->id'");
  	if (mysql_num_rows($anketaEx)>0) {
  		$anketaOnE = mysql_fetch_object($anketaEx);
  		mysql_query("DELETE FROM 3_ankety WHERE id = '$anketaOnE->id'");
  		mysql_query("DELETE FROM 3_ankety_data WHERE ank_id = '$anketaOnE->id'");
  	}
  	Header ("Location: $inc/diskuze/$slink/ankety/");
  	exit;
  }

  $jeAnketa = mysql_query("SELECT * FROM 3_ankety WHERE dis = '$dItem->id' AND aktiv = '1'");
  if (mysql_num_rows($jeAnketa) < 1) {
  switch ($_GET['do']) {
  	case "new":
  		$moznosti = array();
  		if (isset($_POST['new_otazka']) && isset($_POST['new_moznost']) && count($_POST['new_moznost'])>=2 && is_array($_POST['new_moznost'])) {
  			for ($i=0;$i<count($_POST['new_moznost']);$i++) {
  				if (mb_strlen(trim($_POST['new_moznost'][$i]),"UTF-8") >= 1) {
  					$moznosti[] = _htmlspec(trim($_POST['new_moznost'][$i]));
  				}
  			}
  			$otazka = _htmlspec(trim($_POST['new_otazka']));
  			if (count($moznosti)>=2 && mb_strlen($otazka,"UTF-8")>=1) {
  				$odpoved = join(">",$moznosti);
  				mysql_query("INSERT INTO 3_ankety (otazka,dis,odpoved,aktiv) VALUES ('$otazka',$dItem->id,'$odpoved','1')");
  			}
  			Header ("Location: $inc/diskuze/$slink/ankety/");
  			exit;
  		}
  	break;
  }
  	Header ("Location: $inc/diskuze/$slink/ankety/");
  	exit;
  }
  else {
  	$anketa = mysql_fetch_object($jeAnketa);
  	mysql_free_result($jeAnketa);
  	$moznosti = explode(">", $anketa->odpoved);
  
  switch ($_GET['do']) {
  	case "new":
  		end_ankety($anketa->id,$moznosti);
  		unset($moznosti);
  		$moznosti = array();
  		if (isset($_POST['new_otazka']) && isset($_POST['new_moznost']) && count($_POST['new_moznost'])>=2 && is_array($_POST['new_moznost'])) {
  			for ($i=0;$i<count($_POST['new_moznost']);$i++) {
  				if (mb_strlen(trim($_POST['new_moznost'][$i]),"UTF-8") >= 1) {
  					$moznosti[] = _htmlspec(trim($_POST['new_moznost'][$i]));
  				}
  			}
  			$otazka = _htmlspec(trim($_POST['otazka']));
  			if (count($moznosti)>=2 && mb_strlen($otazka,"UTF-8")>=1) {
  				$odpoved = join(">",$moznosti);
  				mysql_query("INSERT INTO 3_ankety (otazka,dis,odpoved,aktiv) VALUES ('$otazka',$dItem->id,'$odpoved','1')");
  			}
  		}
  	break;
  	case "edit":
  		if (isset($_POST['otazka']) && isset($_POST['moznosti']) && count($_POST['moznosti'])>=2 && is_array($_POST['moznosti'])) {
  			$otazka = $anketa->otazka;
  			if(mb_strlen($_POST['otazka'],"UTF-8")>0) {
  				$otazka = addslashes(_htmlspec($_POST['otazka']));
  			}
  			for ($i=0;$i<count($moznosti);$i++) {
  				if (trim($_POST['moznosti'][$i]) != "") {
  					$moznosti[$i] = _htmlspec($_POST['moznosti'][$i]);
  				}
  			}
  			$moznosti = join(">",$moznosti);
  			mysql_query("UPDATE 3_ankety SET otazka = '$otazka', odpoved = '$moznosti' WHERE id = '$anketa->id'");
  		}
  		header ("Location: $inc/diskuze/$slink/ankety/");
  		exit;
  	break;
  	case "end":
  		end_ankety($anketa->id,$moznosti);
  	break;
  	case "null":
  		mysql_query("DELETE FROM 3_ankety_data WHERE ank_id = '$anketa->id'");
  	break;
  }
  
  /* --- end switch "do" ---- */
  
  	header ("Location: $inc/diskuze/$slink/ankety/");
  	exit;
  }

}
else {
	header ("Location: $inc/diskuze/");
	exit;
}
?>
