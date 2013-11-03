<?php
//nahrani ikonky na server

if (!$LogedIn || $hFound !== true || !isSet($_FILES['ico'])) {
	Header ("Location:$inc/herna/");
	exit;
}
if ($hItem->uid == $_SESSION['uid'] || $allowsPJ['prispevky']) {

	if (strlen($_FILES["ico"]["name"])>4) {
	//nahrani ikonky na server
		$type = ereg_replace("^.+\.(.+)$","\\1",$_FILES["ico"]["name"]);
		if ($type != "jpg" && $type != "gif" && $type != "png") {
			$error = 2;
			$type = "tmp";
		}
		$ico_n = "p_".$hItem->id."_".Rand(1,9).Rand(1,9).Rand(1,9)."_".$_SESSION['uid'].".".$type;
		if ($_SESSION['uid'] != $hItem->uid) {
			$ico_n = 'p_'.$ico_n;
		}
		$cesta = "./system/icos/$ico_n";
		move_uploaded_file ($_FILES["ico"]["tmp_name"], $cesta);
		$size = getimagesize($cesta);
		$width = $size[0];
		$height = $size[1];
		if (strlen($_FILES["ico"]["name"]) < 5) {
			$error = 2;
		}	elseif (($size[2] != 1) && ($size[2] != 2) && ($size[2] != 3)){
			$error = 3;
		} elseif ($_FILES["ico"]["size"] > 16384){
			$error = 4;
		} elseif (($width > 50) || ($width < 40) || ($height < 50) || ($height > 70)) {
			$error = 5;
		} else{
			$error = 0;
		}
		if ($error == 0) {
			if ($hItem->uid == $_SESSION['uid']) {
				$uIco = mysql_fetch_object( mysql_query("SELECT ico FROM 3_herna_all WHERE id = '$hItem->id'"));
				//neni-li ikona defaultni, smaze se stara
				mysql_query ("UPDATE 3_herna_all SET ico = '$ico_n' WHERE id = '$hItem->id'");
			}
			else {
				$uIco = mysql_fetch_object( mysql_query("SELECT ico FROM 3_herna_pj WHERE cid = '$hItem->id' AND uid = '$_SESSION[uid]' AND schvaleno='1' AND prispevky='1'"));
				mysql_query ("UPDATE 3_herna_pj SET ico = '$ico_n' WHERE cid = '$hItem->id' AND uid = '$_SESSION[uid]' AND schvaleno='1' AND prispevky='1'");
			}
			if ($uIco->ico != "" && $error == 0 && $uIco->ico != "default.jpg") {
				@unlink("./system/icos/$uIco->ico");
			}
		}
	}
	//redirect pri chybe / uspesny redirect
	if ($error > 0){
		//smazani
	  @unlink($cesta);
	  Header ("Location:$inc/herna/$slink/$sslink/?error=$error");
	}else{
	  Header ("Location:$inc/herna/$slink/$sslink/?ok=2");
	}
	exit;

}
else {
	Header ("Location:$inc/$slink/$sslink/");
	exit;
}
?>
