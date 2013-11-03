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
if ($postava->uid == $_SESSION['uid']) {
	mysql_query("DELETE FROM 3_herna_postava_$jTypString WHERE cid = $postava->cid AND uid = $_SESSION[uid] AND id = $postava->id");
	if ($postava->ico != "" && $postava->ico != "default.jpg") {
		@unlink("./system/icos/$postava->ico");
	}
	header ("Location: $inc/herna/my/?ok=3");
}
else {
	header ("Location: $inc/herna/$slink/$sslink/");
	exit;
}

?>
