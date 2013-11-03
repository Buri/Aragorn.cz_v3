<?php

$error = 0;

//vymaz vybranych zprav u posty
if (!$LogedIn) {
	header("Location: $inc");
	exit;
}

$idS = explode(",",$_GET['ids']);
$ids = array();
for($a = 0;$a < count($idS);$a++){
	if (ctype_digit($idS[$a])) $ids[$a] = $idS[$a];
}
$ids = join(",",$ids);

$TextIDs = $StavToDel = $StavFromDel = $delNormal = $changeWhisToDel = array();

mysql_query("LOCK TABLES 3_post_new WRITE, 3_post_text");

switch($_GET['akce']) {
	case "postolka-delete-in":
	case "postolka-delete-out":
		if (!isset($_POST['olderThan'])){
			$error = 21;
		}
		else {
			$cas = intval(mktime(0,0,1,$_POST['olderThan']['mesic'],$_POST['olderThan']['den'],$_POST['olderThan']['rok']));
			if ($_GET['akce'] == "postolka-delete-out") {
				$olderS = mysql_query("SELECT id FROM 3_post_new WHERE cas <= $cas AND tid = $_SESSION[uid] LIMIT 1");
				if ($olderS && mysql_num_rows($olderS)>0) {
					$older = array_pop(mysql_fetch_row($olderS));
					$messS = mysql_query("SELECT id,whis,whisstav,stavto FROM 3_post_new WHERE fid = $_SESSION[uid] AND stavfrom = '1' AND id < $older");
					if ($messS && mysql_num_rows($messS)>0) {
						while($mess = mysql_fetch_object($messS)){
							if ($mess->whis == '') { // neni hromadna posta pro vice lidi ode mne
								if ($mess->stavto == '3') { // muzu ji smazat
									$delNormal[] = $mess->id; // klasicke smazani
								}
								else {
									$StavFromDel[] = $mess->id; // zmenit stavFrom na smazano
								}
							}
							else { // hromadna pro vice lidi
								if ($mess->whisstav == str_pad("",count(explode(",",$mess->whis)),"3")) { // vsichni jiz smazali
									$delNormal[] = $mess->id; // klasicke smazani
								}
								else {
									$delNormal[] = $mess->id; // klasicke smazani bez textu
								}
							}
						}
					}
					else {
						// nothing to delete
					}
				}
				else {
					// no older id
				}
			}
		}
	break;
	case "postolka-delete":
		$messS = mysql_query("SELECT id,mid,parent,tid,fid,stavto,stavfrom,whis,whisstav FROM 3_post_new WHERE id IN ($ids) AND ((tid = $_SESSION[uid] AND (stavto = '1' OR stavto = '0')) OR (fid = $_SESSION[uid] AND stavfrom = '1'))");
		if (isset($AragornCache)) {
			$AragornCache->delVal("post-unread:$_SESSION[uid]");
		}
		while($mess = mysql_fetch_object($messS)){
			$TextIDs[] = $mess->mid;
			if ($mess->tid == $_SESSION['uid']) { // MessToMe
				if ($mess->parent == '0') { // neni z hromadne posty
					if ($mess->stavfrom == '3') { // muzu ji smazat
						$delNormal[] = $mess->id; // klasicke smazani
					}
					else {
						$StavToDel[] = $mess->id; // zmenit stavTo na smazano
					}
				}
				else { // je z hromadne posty ToMe
					$changeWhisToDel[] = $mess->parent; // zmenit cast whisStav na smazano
					if ($mess->stavfrom == '3') { // muzu ji smazat (VZDY ANO)
						$delNormal[] = $mess->id; // klasicke smazani
					}
					else {
						$StavToDel[] = $mess->id; // zmenit stavTo na smazano
					}
				}
			}
			else {	// MessFromMe
				if ($mess->whis == '') { // neni hromadna posta FromMe
					if ($mess->stavto == '3') { // muzu ji smazat
						$delNormal[] = $mess->id; // klasicke smazani
					}
					else {
						if (isset($AragornCache)) {
							$AragornCache->delVal("post-unread:$mess->tid");
						}
						$StavFromDel[] = $mess->id; // zmenit stavFrom na smazano
					}
				}
				else { // je hromadna posta FromMe
					if ($mess->whisstav == str_pad("",count(explode(",",$mess->whis)),"3")) { // vsichni jiz smazali
						$delNormal[] = $mess->id; // klasicke smazani
					}
					else {
						$delNormal[] = $mess->id; // klasicke smazani bez textu
					}
				}
			}
		}
		mysql_free_result($messS);
	break;
}

if (count($StavToDel)>0) {
	$StavToDel = join(",",$StavToDel);
	mysql_query("UPDATE 3_post_new SET stavto = '3' WHERE id IN($StavToDel) AND tid = $_SESSION[uid]");
	if (isset($AragornCache)) {
		$AragornCache->delVal("post-unread:$_SESSION[uid]");
	}
}
if (count($StavFromDel)>0) {
	$StavFromDel = join(",",$StavFromDel);
	mysql_query("UPDATE 3_post_new SET stavfrom = '3' WHERE id IN($StavFromDel) AND fid = $_SESSION[uid]");
}
if (count($delNormal)>0) {
	$delNormal = join(",",$delNormal);
	mysql_query("DELETE FROM 3_post_new WHERE id IN ($delNormal)");
}
if (count($changeWhisToDel)>0) {
	$changeWhisToDel = join(",",$changeWhisToDel);
	$whatS = mysql_query("SELECT id,whis,whisstav FROM 3_post_new WHERE id IN ($changeWhisToDel) AND stavfrom = '1'");
	while($what = mysql_fetch_object($whatS)){
		$users = explode(",",$what->whis);
		$pozice = array_search($_SESSION['uid'], $users);
		$whisNew = substr_replace($what->whisstav,'3',$pozice,1);
		mysql_query("UPDATE 3_post_new SET whisstav='$whisNew' WHERE id = $what->id");
	}
}
if (count($TextIDs)>0) {
	$TextToBeDeleted = array();
	for($a=0;$a<count($TextIDs);$a++){
		$allowDelS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_post_new WHERE mid = $TextIDs[$a]"));
		if ($allowDelS[0] == '0') {
			$TextToBeDeleted[] = $TextIDs[$a];
		}
	}
	if (count($TextToBeDeleted)>0) {
		$TextToBeDeleted = join("','",$TextToBeDeleted);
		mysql_query("DELETE FROM 3_post_text WHERE id IN ('$TextToBeDeleted')");
	}
}

mysql_query("UNLOCK TABLES");

$addUrl = "";
if ($slink != "") {
  $addUrl .= $slink."/";
}
if ($sslink != "") {
  $addUrl .= $sslink."/";
}

//redirect pri chybe / uspesny redirect
if ($error > 0){
	header ("Location:$inc/$link/$addUrl?error=$error&_t=$time");
	exit;
}else{
	header ("Location:$inc/$link/$addUrl?ok=2&_t=$time");
	exit;
}
?>
