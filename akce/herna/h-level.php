<?php
$error = $ok = 0;

if (!$LogedIn || $hFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}
if ($pFound !== true) {
	header ("Location: $inc/herna/$slink/");
	exit;
}
if ($hItem->uid == $_SESSION['uid'] && $hItem->id == $postava->cid && $hItem->typ == 0) {
	include "./add/drd-fce.php";
	if ($_GET['akce'] == "postava-level-up") {
		level_up($postava->id,$postava->uroven,$postava->xp,$postava->povolani,$postava->odolnost);	
		header ("Location: $inc/herna/$slink/$sslink/?ok=2");
		exit;
	}
	elseif ($_GET['akce'] == "postava-level-down") {
		level_down($postava->id,$postava->uroven);
		header ("Location: $inc/herna/$slink/$sslink/?ok=3");
		exit;
	}
}
else {
	header ("Location: $inc/herna/$slink/$sslink/");
	exit;
}

?>
