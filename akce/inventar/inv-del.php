<?php

if (!$LogedIn || $hFound !== true || $pFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}

if ($_SESSION['uid'] == $postava->uid || $hItem->uid == $_SESSION['uid']) {
	switch ($_GET['do']) {
		case "rm1":
		case "rm10":
		case "rm100":
		case "del":
			if (strlen($postava->inventar)>5 && isset($_GET['v']) && ctype_digit($_GET['v']) && $_GET['v']!="") {
				include_once("./add/xml_parser_func.php");
				$inventory = inventar_read(stripslashes($postava->inventar));
				switch ($_GET['do']) {
					case "rm1":
						$items2remove = $_GET['v'];
						$inventory = inventar_uprava($inventory,"remove",$items2remove);
					break;
					case "rm10":
						$items2remove = array_fill(0,10,$_GET['v']);
						$inventory = inventar_uprava($inventory,"remove",$items2remove);
					break;
					case "rm100":
						$items2remove = array_fill(0,100,$_GET['v']);
						$inventory = inventar_uprava($inventory,"remove",$items2remove);
					break;
					case "del":
						$items2remove = $_GET['v'];
						$inventory = inventar_uprava($inventory,"delete",$items2remove);
					break;
					default:
						$inventory = array(0,0,0);
					break;
				}
				if ($inventory[1] == true) {
					$inventory[0] = addslashes($inventory[0]);
					mysql_query("UPDATE 3_herna_postava_$jTypString SET inventar = '$inventory[0]' WHERE id = $postava->id and uid = $postava->uid and cid = $postava->cid");
					Header("Location: $inc/herna/$slink/$sslink/");
					exit;
				}
			}
		break;
	}
}
else {
	header ("Location: $inc/herna/$slink/");
	exit;
}

?>
