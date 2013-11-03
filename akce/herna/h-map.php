<?php

$error = $ok = 0;

if (!$LogedIn || !$hFound) {
	header ("Location: $inc/herna/");
	exit;
}

$jsemIn = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE cid = $hItem->id AND uid = $_SESSION[uid] AND schvaleno = '1'"));

if ($sslink == "mapy" && ($hItem->uid == $_SESSION['uid'] || $jsemIn[0]>0 || $allowsPJ['mapy'])) {
	switch ($_GET['do']) {
		case "del":
			if (isset($_GET['id']) && ctype_digit($_GET['id']) && $_GET['id']!="") {
				$isMapS = mysql_query("SELECT id,soubor,datas FROM 3_herna_maps WHERE id=$_GET[id] AND cid=$hItem->id");
				if (mysql_num_rows($isMapS)>0) {
					$map = mysql_fetch_object($isMapS);
					if ($hItem->uid == $_SESSION['uid'] && $map->soubor != "js") {
						unlink("./system/mapy/$map->datas");
					}
					mysql_query("DELETE FROM 3_herna_maps WHERE id=$map->id");
					header ("Location: $inc/herna/$slink/mapy/?ok=3");
					exit;
				}
			}
		break;
		case "upload":
		  if ($hItem->uid != $_SESSION['uid'] && !$allowsPJ['mapy']) {
			  Header ("Location: $inc/herna/$slink/mapy/");
			  exit;
			}
			if (!isset($_FILES["map"]) || !isset($_POST['nazev_mapy_img']) || $_FILES["map"]["error"] !== UPLOAD_ERR_OK) {
			  Header ("Location: $inc/herna/$slink/mapy/?error=2");
			  exit;
			}
			$nazev = addslashes($_POST['nazev_mapy_img']);
			$filetype = mb_strtolower(mb_ereg_replace("^.+\.(.+)$","\\1",$_FILES["map"]["name"]));
			$img_src = "map-".$hItem->nazev_rew."-".$_SESSION['uid']."-".rand(0,9).rand(0,9).rand(0,9).".".$filetype;
			$cesta = "./system/mapy/$img_src";
			$maps_size = mysql_fetch_row(mysql_query("SELECT SUM(size) FROM 3_herna_maps WHERE cid=$hItem->id"));
			$sizeIMG = $_FILES["map"]["size"];
			if ($sizeIMG > (1024*1024 - $maps_size[0])) {
				$error=3;
			}
			elseif ($sizeIMG > (1024*200) || $_FILES["map"]["error"] == UPLOAD_ERR_INI_SIZE){
				$error = 2;
			}
			else {
				move_uploaded_file($_FILES["map"]["tmp_name"], $cesta);
				$size = getimagesize($cesta);
				if (mb_strlen($nazev,"UTF-8") < 2){
				  $error = 1;
				} elseif (($size[2] != 1) && ($size[2] != 2) && ($size[2] != 3)){
				  $error = 1;
				} else{
				  $error = 0;
				}
			}
			if ($error>0){
			  @unlink($cesta);
			  Header ("Location: $inc/herna/$slink/mapy/?error=$error");
			  exit;
			}
			else {
				$img_src = addslashes($img_src);
				mysql_query("INSERT INTO 3_herna_maps (cid,nazev,soubor,datas,size) VALUES ($hItem->id, '$nazev', 'file', '$img_src', $sizeIMG)");
			  Header ("Location: $inc/herna/$slink/mapy/?ok=1");
			  exit;
			}
		break;
	}
}

	header ("Location: $inc/herna/$slink/");
	exit;

?>
