<?php

if (!$LogedIn || !$hFound) {
	header ("Location: $inc/herna/?");
	exit;
}

$okPostava = false;

for ($a=0;$a<count($jeskyneHraci);$a++) {
	if ($jeskyneHraci[$a]['objekt']->uid == $_SESSION['uid'] && $jeskyneHraci[$a]['objekt']->schvaleno == "1") {
		$postava = $jeskyneHraci[$a]['objekt'];
		$okPostava = true;
		break;
	}
}

if ($okPostava == true && isset($_GET['v']) && ctype_digit($_GET['v']) && $_GET['v']!="") {

	if ($hItem->shoped != "") {
		$obchodEdSrc = explode("*",$hItem->shoped);
		$obchodEd = array();
		for ($i=0;$i<count($obchodEdSrc);$i++){
			$oneItem = explode("/",$obchodEdSrc[$i]);
			$obchodEd[$oneItem[0]] = $oneItem[1];
		}
	}
	else {
		$obchodEd = array();
	}

	include_once("./add/xml_parser_func.php");
	if (strlen($postava->inventar)>5) {
		$isItem = mysql_query("SELECT * FROM 3_herna_items WHERE id = '$_GET[v]'");
		if (mysql_num_rows($isItem) != 1) {
			Header("Location: $inc/herna/$slink/shop/");
			exit;
		}
		$itemInv = mysql_fetch_object($isItem);
		$inventory = inventar_read(stripslashes($postava->inventar));
		$item2add = array("id"=>$_GET['v'],"pocet"=>1);
		$inventory = inventar_uprava($inventory,"add",$item2add);
		$povoleni = true;
		if (isSet($obchodEd[$itemInv->id])) {
			if ($obchodEd[$itemInv->id]>=0) {
				$cena = $obchodEd[$itemInv->id];
			}
			else {
				$povoleni = false;
				$cena = 0;
			}
		}
		else {
			$cena = $itemInv->cena;
		}
		if ($postava->penize<$cena || !$povoleni) {
			Header("Location: $inc/herna/$slink/shop/");
			exit;
		}
		elseif ($inventory[1] == true && $povoleni) {
			$inventory[0] = addslashes($inventory[0]);
			$postava->penize = ($postava->penize)-($cena);
			mysql_query("UPDATE 3_herna_postava_drd SET penize = $postava->penize, inventar = '$inventory[0]' WHERE id = $postava->id and uid = $postava->uid and cid = $postava->cid");
			Header("Location: $inc/herna/$slink/shop/");
			exit;
		}
	}
	else {
		$isItem = mysql_query("SELECT * FROM 3_herna_items WHERE id = '$_GET[v]'");
		if (mysql_num_rows($isItem) != 1) {
			Header("Location: $inc/herna/$slink/shop/");
			exit;
		}
		$itemInv = mysql_fetch_object($isItem);
		$inventory = inventar_make();
		$item2add = array("id"=>$_GET['v'],"pocet"=>1);
		$inventory = inventar_uprava($inventory,"add",$item2add);
		$povoleni = true;
		if (isSet($obchodEd[$itemInv->id])) {
			if ($obchodEd[$itemInv->id]>=0) {
				$cena = $obchodEd[$itemInv->id];
			}
			else {
				$povoleni = false;
				$cena = 0;
			}
		}
		else {
			$cena = $itemInv->cena;
		}
		if ($postava->penize<$cena || !$povoleni) {
			Header("Location: $inc/herna/$slink/shop/");
			exit;
		}
		elseif ($inventory[1] == true && $povoleni) {
			$inventory[0] = addslashes($inventory[0]);
			$postava->penize = ($postava->penize)-($cena);
			mysql_query("UPDATE 3_herna_postava_drd SET penize = $postava->penize, inventar = '$inventory[0]' WHERE id = $postava->id and uid = $postava->uid and cid = $postava->cid");
			Header("Location: $inc/herna/$slink/shop/");
			exit;
		}
	}
}
else {
	header ("Location: $inc/herna/$slink/");
	exit;
}

Header("Location: $inc/herna/$slink/");
exit;

?>
