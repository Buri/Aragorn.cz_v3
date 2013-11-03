<?php
//postnuti prispevku do diskuze/kommentare/herna
$AllowedTo = 1;
$aSb = "";
$aSa = "";
$whisperThis = false;

if (isset($_GET['ajaxed']) && $_GET['ajaxed'] > 0) {
	$ajaxed = true;
}


$changeToXHTML = array("b>"=>"strong>","i>"=>"em>");
$inserted = false;

if ($LogedIn && $dFound && $sid == 3) {
	$id = $dItem->id;

	$AllowedTo = GetPravaHere($dItem->id, $dItem->owner, $dItem->prava_reg, $dItem->prava_guest, $LogedIn);
	if ($AllowedTo != "read" && $AllowedTo != "hide" && $dItem->closed == "0" && mb_strlen(trim($_POST['mess']),"UTF-8")>0) {
		$text = strtr(editor(trim($_POST['mess'])),$changeToXHTML);

		$text = addslashes($text);

		if ($splitMetaFromText) {
			mysql_query("INSERT INTO 3_comm_3_texts (text_content) VALUES ('$text');");
			$lid = mysql_insert_id();
			mysql_query("INSERT INTO 3_comm_3 (uid, time, aid, mid) VALUES ('$_SESSION[uid]', '$time', '$dItem->id', '$lid');");
		}
		else {
			mysql_query("INSERT INTO 3_comm_3 (uid, text, time, aid) VALUES ('$_SESSION[uid]', '$text', '$time', '$dItem->id')");
		}

/*
		$binarka = gzcompress($text,9);
		if (strlen($text)>strlen($binarka)) {
			$text = bin2hex($binarka);
			mysql_query ("INSERT INTO 3_comm_3 (uid, text, time, aid, compressed) VALUES ($_SESSION[uid], 0x$text, $time, $dItem->id, 1)");
		}
		else {

			$text = addslashes($text);
			mysql_query ("INSERT INTO 3_comm_3 (uid, text, time, aid, compressed) VALUES ($_SESSION[uid], '$text', $time, $dItem->id, 0)");
		}
*/

		$inserted = true;
  }
}
elseif ($LogedIn == true && mb_strlen(trim($_POST['mess'])) > 0){

	if ($link == "clanky" || $link == "galerie") {
		$AllowedTo = get_prava_sekce($sid,$id);
		if ($AllowedTo == 0) {
			if ($ajaxed) {
				echo "right";
				exit;
			}
			else {
				header("Location: $inc/$link/$slink/#kom");
				exit;
			}
		}
	}

	$aSb = $aSa = $aSa1 = $aSa2 = $aSb1 = $aSb2 = "";

	//septani
	if (isSet($_POST['septat']) && $sid == 4 && isSet($id) && $hFound == true){
		$id = $hItem->id;

		$septat = $_POST['septat'];
		$aSa = ", whispering, whisText";
		$aSa1 = ", text_whisText";
		$aSa2 = ", whispering";
		$whisperThis = $whisperNames = array();
		$whisperPJ = false;
		$whisperText = "";
		$septat = "'".join("','",$septat)."'";
		$septatIdS = mysql_query("SELECT id FROM 3_users WHERE login_rew IN ($septat)");
		if (mysql_num_rows($septatIdS)>0) {
			while ($septatID = mysql_fetch_object($septatIdS)) {
				if ($septatID->id != $hItem->uid) {
					if (isset($uzivatele[$septatID->id]['postava'])) {
						$whisperNames[] = $uzivatele[$septatID->id]['postava'];
					}
				}
				else {
					$whisperPJ = true;
				}
				$whisperThis[] = $septatID->id;
			}
			$whisperThis = "#".join ("#",$whisperThis)."#";
			if ($whisperPJ) {
				$whisperNames[-1] = "Pán Jeskyně";
			}
			$whisperText = addslashes(join(", ",$whisperNames));
			$aSb = ", '".$whisperThis."', '".$whisperText."'";
			$aSb1 = ", '".$whisperText."'";
			$aSb2 = ", '".$whisperThis."'";
		}
		else {
			$aSb = $aSa = $aSa1 = $aSa2 = $aSb1 = $aSb2 = "";
		}
	}

	$text = strtr(editor(trim($_POST['mess'])),$changeToXHTML);
	$text = addslashes($text);

	if ($splitMetaFromText || $_SESSION['uid'] == 2 || $_SESSION['uid'] == 1990) {
		$sql = "INSERT INTO 3_comm_".$sid."_texts (text_content $aSa1) VALUES ('$text' $aSb1)";
		mysql_query($sql);
		$lid = mysql_insert_id();
		$sql = "INSERT INTO 3_comm_".$sid." (uid, time, aid, mid $aSa2) VALUES ('$_SESSION[uid]', '$time', '$id', '$lid' $aSb2)";
		$a = mysql_query($sql);
	}
	else {
		$sql = "INSERT INTO 3_comm_$sid (uid, text, time, aid $aSa) VALUES ('$_SESSION[uid]', '$text', '$time', '$id' $aSb)";
		$inserted = true;
		$a = mysql_query($sql);
	}

/*	$binarka = gzcompress($text,9);
	if (strlen($text)>strlen($binarka)) {
		$text = bin2hex($binarka);
		$sql = "INSERT INTO 3_comm_$sid (uid, text, time, compressed, aid $aSa) VALUES ($_SESSION[uid], 0x$text, $time, 1, $id $aSb)";
	}
	else {
		$text = addslashes($text);
		$sql = "INSERT INTO 3_comm_$sid (uid, text, time, compressed, aid $aSa) VALUES ($_SESSION[uid], '$text', $time, 0, $id $aSb)";
	}
*/

	if ($a) {
		if (mysql_affected_rows() > 0) {
			$inserted = true;
		}
	}


	if ($sid == 4 && $hFound == true) {
		$sqlAd = "";
		if ($hItem->uid == $_SESSION['uid']) {
			$sqlAd = "aktivitapj='$time', ";
		}
		else {
			mysql_query("UPDATE 3_herna_pj SET aktivita='$time' WHERE cid = '$hItem->id' AND uid = '$_SESSION[uid]' AND schvaleno='1'");
			mysql_query("UPDATE 3_herna_postava_$jTypString SET aktivita='$time' WHERE cid = '$hItem->id' AND uid = '$_SESSION[uid]' AND schvaleno='1'");
		}
		mysql_query("UPDATE 3_herna_all SET $sqlAd ohrozeni='0', aktivita = '$time' WHERE id = '$hItem->id'");
	}
}
elseif ($slink != "") {
	if ($ajaxed) {
		echo "text";
		exit;
	}
	else {
	}
}
else {
	if ($ajaxed) {
		if ($slink == "") {
			echo "--";
			exit;
		}
		if (!$LogedIn) {
			echo "off";
			exit;
		}
	}
	else {
		Header ("Location:$inc/$link/");
		exit;
	}
}

$addon = "";
if ($inserted) {
	if ($sid != 4) {
		addOneVisited($sid,$id);
	}
	$addon = "?_t=$time";
}

if ($ajaxed) {
	if ($inserted) {
		$ajaxed = true;
		include "./add/dis.php";
	}
	else {
		echo "--";
	}
	exit;
}

Header ("Location:$inc/$link/$slink/$addon#kom");
exit;
?>