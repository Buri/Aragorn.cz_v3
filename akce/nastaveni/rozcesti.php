<?php
$error = $ok = 0;

//testovani formatu grafiky
function roz_format_test($ico){
	$size = getimagesize($ico);
	if (($size[2] != 1) && ($size[2] != 2) && ($size[2] != 3)){
		return 1; 
	}else{
		return 0;
	}
}
//rozmery ikony
function roz_ico_size($ico){
	$size = getimagesize($ico);
	if ($size[0] > 50 || $size[0] < 40 || $size[1] < 40 || $size[1] > 70){
		return 1;
	}else{
		return 0;
	}
}
//velikost ikony
function roz_ico_dat($ico){
	if ($ico > 16384){
		return 1;
	}else{
		return 0;
	}
}

switch ($_GET['do']){
	case "ico-delete":
		if (isset($_GET['c'])) {
			if ($_GET['c'] == md5("roz-ico-delete-".$_SESSION['login_rew'])) {
				$uIco = mysql_fetch_object( mysql_query("SELECT roz_ico FROM 3_users WHERE id = '$_SESSION[uid]'") );
				mysql_query("UPDATE 3_users SET roz_ico = '' WHERE id = '$_SESSION[uid]'");
				if ($uIco->roz_ico && $uIco->roz_ico != 'default.jpg' && unlink("./system/roz_icos/".$uIco->roz_ico)) {
					$ok = 8;
				}
				else {
					$error = 10;
				}
			}
		}
	break;
	case "ico-upload":
		//nahrani ikonky na server
		if (is_uploaded_file($_FILES['ico']['tmp_name'])) {
			$type = strtolower(ereg_replace("^.+\.(.+)$","\\1",$_FILES["ico"]["name"]));
			$ico_n = Rand(1,9).Rand(1,9).Rand(1,9)."_".$_SESSION['uid'].".".$type;
			if (!move_uploaded_file ($_FILES["ico"]["tmp_name"], "./system/roz_icos/$ico_n")){
				$error = 17;
			}elseif (mb_strlen($_FILES["ico"]["name"]) < 3){
				$error = 1;
			}elseif ( roz_format_test("./system/roz_icos/$ico_n") > 0 ){
				$error = 2;
			}elseif( roz_ico_size("./system/roz_icos/$ico_n") > 0 ){
				$error = 3;
			}elseif( roz_ico_dat($_FILES["ico"]["size"]) > 0 ){
				$error = 4;
			}else{
				$uIco = mysql_fetch_object( mysql_query("SELECT roz_ico FROM 3_users WHERE id = '$_SESSION[uid]'") );
				//neni-li ikona defaultni, smaze se stara
				if ($uIco->roz_ico != "default.jpg" && $uIco->roz_ico != "") {
					@unlink("./system/roz_icos/$uIco->roz_ico");
				}
				mysql_query("UPDATE 3_users SET roz_ico = '$ico_n' WHERE id = '$_SESSION[uid]'");
			}
			if ($error==0){
				$ok = 1;
			}
			if ($error>0) {
				@unlink("./system/roz_icos/$ico_n");
			}
		}
		else {
			$error = 16;
		}
break;

//zmena jmena a popisu postavy
case "edit":
	if (isset($_POST['jmeno_postavy'],$_POST['popis_postavy'])) {
		$rn = htmlspecialchars(mb_substr($_POST['jmeno_postavy'],0,20),ENT_QUOTES,"UTF-8");
		$rp = htmlspecialchars(mb_substr($_POST['popis_postavy'],0,200),ENT_QUOTES,"UTF-8");
		mysql_query ("UPDATE 3_users SET roz_name = '".trim($rn)."', roz_popis = '".trim($rp)."' WHERE id = '$_SESSION[uid]'");
		//aktualni hodnoty
		$ok = 9;
	}
break;
}
if ($ok > 0 || $error > 0) {
	if ($ok > 0)
		header("Location: $inc/nastaveni/rozcesti/?ok=$ok");
	elseif ($error > 0)
		header("Location: $inc/nastaveni/rozcesti/?error=$error");
}
else {
	header("Location: $inc/nastaveni/rozcesti/");
}
exit;
?>
