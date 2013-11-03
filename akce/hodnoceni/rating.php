<?php
//hodnoceni (clanky, galerka)

	if ($link == "clanky"){
		$sid = 1;
	}else{
		$link == "galerie";
		$sid = 2;
	}
	
	$sA = mysql_query ("SELECT id FROM 3_$link WHERE nazev_rew = '$slink' AND schvaleno = '1' AND autor != $_SESSION[uid]");
	$cA = mysql_num_rows($sA);
	$oA = mysql_fetch_object($sA);

if ($cA > 0 && is_numeric($_POST['rating']) && $_POST['rating']<=5 && $_POST['rating']>0){

	$sR = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM 3_rating WHERE uid = $_SESSION[uid] AND aid = $oA->id AND sid = $sid"));
	$rate = round($_POST['rating'], 1);
	if ($sR[0] < 1){
				
		$ok = 1;
		mysql_query ("INSERT INTO 3_rating (uid, aid, sid, rate) VALUES ($_SESSION[uid], $oA->id, $sid, '$rate')");
		mysql_query ("UPDATE 3_$link SET hodnoceni = hodnoceni + $rate, hodnotilo = hodnotilo+1 WHERE id = $oA->id");
	}else{
		$error = 1;
	}
}else{
	$error = 1;
}

//redirect pri chybe / uspesny redirect
if (isSet($ok)){
	Header ("Location:$inc/$link/$slink/?ok=$ok");
}else{
	Header ("Location:$inc/$link/$slink/?error=$error");
}
exit;
?>
