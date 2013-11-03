<?php

if (!$LogedIn || $hFound != true || $pFound != true) {
	header ("Location: $inc/herna/");
	exit;
}

if ($_SESSION['uid'] == $postava->uid || $hItem->uid == $_SESSION['uid']) {
	switch ($_GET['do']) {
		case "add":
			if (isset($_POST['new_kouzlo']) && ctype_digit($_POST['new_kouzlo']) && $_POST['new_kouzlo']!="") {
				$kouzla = stripslashes($postava->kouzla);
				$kouzla = explode(">",$postava->kouzla);
				$kouzla[] = $_POST['new_kouzlo'];
				$kouzla = join(">",$kouzla);
				$kouzla = addslashes($kouzla);
				mysql_query("UPDATE 3_herna_postava_drd SET kouzla = '$kouzla' WHERE id = $postava->id and uid = $postava->uid and cid = $postava->cid");
				Header("Location: $inc/herna/$slink/$sslink/?ok=4");
				exit;
			}
		case "del":
			if (isset($_GET['i']) && ctype_digit($_GET['i']) && $_GET['i']!="" && $_SESSION['uid'] == $hItem->uid) {
				$kouzla = stripslashes($postava->kouzla);
				$kouzla = explode(">",$kouzla);
				for ($i=0;$i<count($kouzla);$i++) {
					if ($kouzla[$i] == $_GET['i']) {
						unset($kouzla[$i]);
						break;
					}
				}
				$kouzla = addslashes(join(">",$kouzla));
				mysql_query("UPDATE 3_herna_postava_drd SET kouzla = '$kouzla' WHERE id = $postava->id and uid = $postava->uid and cid = $postava->cid");
				Header("Location: $inc/herna/$slink/$sslink/?ok=5");
				exit;
			}
		break;
	}
}
else {
	header ("Location: $inc/herna/$slink/");
	exit;
}

?>
