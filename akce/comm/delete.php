<?php
//delete kom

function deconvert_base($n) {
    return addslashes(base_convert($n, 35, 10));
}

$ids = explode(",",$_GET['ids']);
$ids = addslashes(join(",",array_map("deconvert_base", $ids)));
$deleted = false;

if ($LogedIn && $link=="diskuze" && $dFound == true) {
	$AllowedTo = GetPravaHere($dItem->id, $dItem->owner, $dItem->prava_reg, $dItem->prava_guest, $LogedIn);
	if ($dItem->closed == "0"){
		if ($AllowedTo == "all" || $AllowedTo == "superall") {
		  mysql_query ("DELETE FROM 3_comm_3 WHERE id IN($ids) AND aid = $dItem->id");
			$deleted = mysql_affected_rows();
		}
		elseif ($AllowedTo == "read" || $AllowedTo == "write") {
 		  mysql_query ("DELETE FROM 3_comm_3 WHERE id IN($ids) AND uid = $_SESSION[uid] AND aid = $dItem->id");
			$deleted = mysql_affected_rows();
		}
	}
}
elseif ($LogedIn && $_SESSION['lvl'] > 2) {
  mysql_query ("DELETE FROM 3_comm_$sid WHERE id IN($ids)");
	$deleted = mysql_affected_rows();
}
elseif ($LogedIn && $link=="herna" && $hFound == true) {
	if ($hItem->uid == $_SESSION['uid'] || $allowsPJ['prispevky']) {
		mysql_query ("DELETE FROM 3_comm_4 WHERE id IN($ids) AND aid = '$hItem->id'");
		$deleted = mysql_affected_rows();
	}
	else {
		mysql_query ("DELETE FROM 3_comm_4 WHERE id IN($ids) AND aid = '$hItem->id' AND uid = '$_SESSION[uid]'");
		$deleted = mysql_affected_rows();
	}
}
elseif ($LogedIn) {
  mysql_query ("DELETE FROM 3_comm_$sid WHERE id IN($ids) AND uid = '$_SESSION[uid]'");
	$deleted = mysql_affected_rows();
}

$addon = "";
if ($deleted != 0) {
	$addon = "?_t=$time";
	if ($link == "diskuze" && $dFound) {
		recountVisited(3,$dItem->id);
	}
	elseif (($link == "galerie" || $link == "clanky") && !empty($id)) {
		recountVisited($sid,$id);
	}
	elseif ($link == 'herna' && !empty($hItem)) {
		recountVisited(4,$hItem->id);
	}
}

$index = 0;
if (isset($_GET['index'])) {
	$index = intval($_GET['index']);
}
if ($index > 1) {
	$addon .= $addon?"&index=".$index:"?index=$index";
}

Header ("Location:$inc/$link/$slink/$addon#kom");
exit;
?>
