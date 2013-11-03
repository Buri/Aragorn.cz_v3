<?php
//editace clanku

if (!$LogedIn) {
	Header ("Location:$inc/clanky/");
	exit;
}

$nazev_rew = addslashes($_GET['edit']);
$existujeSrc = mysql_query("SELECT * FROM 3_clanky WHERE nazev_rew = '$nazev_rew' AND schvaleno < 0 AND autor = '$_SESSION[uid]'");

if($existujeSrc && mysql_num_rows($existujeSrc)>0) {

	$clnk = mysql_fetch_object($existujeSrc);

	$anotace = addslashes(trim($_POST['anotace']));
	$text = strtr(trim($_POST['mess']),$changeToXHTML);
	$admins = addslashes(trim($_POST['admins']));

	if (isset($_POST['finalizace']) && $_POST['finalizace'] == "yes") {
		$admins .= "', schvalenotime = '$time', schvaleno = '0";
	}

	if (mb_strlen($text)<40){
		$error = 3;
	}
	elseif (mb_strlen($anotace)<5) {
		$error = 2;
	}
	else {
//		$binarka = gzcompress($text,9);
//		if (strlen($binarka) < strlen($text)) {
//			$binarka = bin2hex($binarka);
//			$sql = "UPDATE 3_clanky SET anotace = '$anotace', admins = '$admins', compressed = 1, text = 0x$binarka WHERE id = '$clnk->id' AND autor = '$_SESSION[uid]'";
//			mysql_query($sql);
//			$ok = 6;
//		}
//		else {
			$text = addslashes($text);
			$ok = 7;
			$sql = "UPDATE 3_clanky SET anotace = '$anotace', admins = '$admins', text = '$text', compressed = 0 WHERE id = '$clnk->id' AND autor = '$_SESSION[uid]'";
			mysql_query($sql);
//		}
	}

}
else {
	$error = 8;
}


//redirect pri chybe / uspesny redirect
if (isSet($error)){
	Header ("Location:$inc/clanky/my/?error=$error");
}else{
	Header ("Location:$inc/clanky/my/?edit=$nazev_rew&ok=$ok");
}
exit;
?>
