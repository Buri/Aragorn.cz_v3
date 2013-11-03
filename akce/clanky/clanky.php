<?php
//nahrani clanku

$nazev_rew = do_seo(trim($_POST['nazev']));
$existuje = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_clanky WHERE nazev_rew = '$nazev_rew'"));

if (mb_strlen(trim($_POST['nazev'])) < 3){
	$error = 1;
}elseif(strlen(trim($_POST['anotace'])) < 10){
	$error = 2;
}elseif(strlen(trim($_POST['mess'])) < 20){
	$error = 3;
}elseif ($existuje[0]>0) {
	$error = 4;
}else{

	$nazev = trim($_POST['nazev']);
	$nazev = addslashes(mb_strtoupper(mb_substr($nazev, 0, 1)).mb_substr($nazev, 1));
	$anotace = addslashes(trim($_POST['anotace']));
	$text = strtr(trim($_POST['mess']),$changeToXHTML);

	switch ($_POST['sekce']) {
		case "0":
		case "1":
		case "2":
		case "3":
		case "4":
		case "5":
			$sekce = $_POST['sekce'];
		break;
		default:
			$sekce = "0";
		break;
	}

//	$binarka = gzcompress($text,9);
//	if (strlen($binarka) < strlen($text)) {
//		$binarka = bin2hex($binarka);
//		mysql_query("INSERT INTO 3_clanky (nazev, nazev_rew, autor, sekce, anotace, text, odeslanotime, compressed) VALUES ('$nazev','$nazev_rew',$_SESSION[uid],'$sekce','$anotace',0x$binarka,$time,'1')");
//	}
//	else {
	$text = addslashes($text);
	mysql_query("INSERT INTO 3_clanky (nazev, nazev_rew, autor, sekce, anotace, text, odeslanotime, compressed) VALUES ('$nazev','$nazev_rew',$_SESSION[uid],'$sekce','$anotace','$text',$time,'0')");
//	}

}


//redirect pri chybe / uspesny redirect
if (isSet($error)){
	Header ("Location:$inc/clanky/new/?error=$error");
}else{
	Header ("Location:$inc/clanky/new/?ok=1");
}
exit;
?>
