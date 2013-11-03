<?php
mb_internal_encoding("UTF-8");
$time = time();

if (isSet($_GET['akce'])) {
switch($_GET['akce']){
//filtrovani
case 1:
if ($_POST['filtr'] > 0){
	$fu = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_filters WHERE uid = $_SESSION[uid] AND rid = $_GET[id] AND fid = $_POST[filtr]"));
	if ($fu[0] > 0){
		mysql_query("DELETE FROM 3_chat_filters WHERE uid = $_SESSION[uid] AND rid = $_GET[id] AND fid = $_POST[filtr]");
	}else{
		mysql_query ("INSERT INTO 3_chat_filters (uid, rid, fid) VALUES ($_SESSION[uid], $_GET[id], $_POST[filtr])");
	}
}
$ok = 3;
break;

case 2:
	if ($isadmin) {
		$ban = 60*60*$_POST['ban'];
		$uN = mysql_fetch_row(mysql_query ("SELECT login, level, ip FROM 3_users WHERE id = '".addslashes($_POST['kick'])."'"));
		if ($uN[1] < 3 && strlen($_POST['reason']) > 0){
			$text = "$_SESSION[login] udělil(a) ban uživateli $uN[0].";
			mysql_query ("DELETE FROM 3_chat_mess WHERE uid = '$_POST[kick]'");
			mysql_query ("INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, $_GET[id], $time, '$text')");
		  $lastInserted = mysql_insert_id();
  		if ($vuItem->saving > 0) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (0, $_GET[id], 0, $lastInserted, '$text', $time)");
			mysql_query ("DELETE FROM 3_chat_users WHERE uid = '$_POST[kick]'"); //vypadne ze vsech roomu
			mysql_query ("INSERT INTO 3_ban (uid, fid, time, assignedin, reason, ipe) VALUES ($_POST[kick], '$_SESSION[uid]', $ban, $time, '$_POST[reason]', '$uN[2]')");
			mysql_query ("UPDATE 3_users SET timestamp = 0 WHERE id = '$_POST[kick]'");
			$ok = 4;
		}else{
			$error = 5;
		}
	}
break;

case 3:
	//testovani formatu grafiky
	function format_test($ico){
		$size = getimagesize($ico);
		if (($size[2] != 1) && ($size[2] != 2) && ($size[2] != 3)){
			return 1; 
		}else{
			return 0;
		}
	}
	//rozmery ikony
	function ico_size($ico){
	$size = getimagesize($ico);
		if ($size[0] > 50 || $size[0] < 40 || $size[1] < 40 || $size[1] > 70){
			return 1;
		}else{
			return 0;
		}
	}
	//velikost ikony
	function ico_dat($ico){
		if ($ico > 16384){
			return 1;
		}else{
			return 0;
		}
	}
	//nahrani ikonky na server
	if (is_uploaded_file($_FILES['ico']['tmp_name'])) {
		$type = strtolower(ereg_replace("^.+\.(.+)$","\\1",$_FILES["ico"]["name"]));
		$ico_n = Rand(1,9).Rand(1,9).Rand(1,9)."_".$_SESSION[uid].".".$type;
		move_uploaded_file ($_FILES["ico"]["tmp_name"], "../system/roz_icos/$ico_n");
		if (mb_strlen($_FILES["ico"]["name"]) < 3){
			$error = 1;
		}elseif ( format_test("../system/roz_icos/$ico_n") > 0 ){
			$error = 2;
		}elseif( ico_size("../system/roz_icos/$ico_n") > 0 ){
			$error = 3;
		}elseif( ico_dat($_FILES["ico"]["size"]) > 0 ){
			$error = 4;
		}else{
			$uIco = mysql_fetch_object( mysql_query("SELECT roz_ico FROM 3_users where id = $_SESSION[uid]") );
			//neni-li ikona defaultni, smaze se stara
			if ($uIco->roz_ico !== "default.jpg"){
				@unlink("../system/roz_icos/$uIco->roz_ico");
			}
			mysql_query ("UPDATE 3_users SET roz_ico = '$ico_n' WHERE id = $_SESSION[uid]");
			$vuItem->roz_ico = $ico_n;
		}
		if (!isSet($error)){
			$ok = 1;
		}
		if ($error>0 && $ico_n != "default.jpg") {
			@unlink("../system/roz_icos/$ico_n");
		}
	}
break;

//zmena jmena a popisu postavy
case 4:
	$rn = htmlspecialchars(mb_substr($_POST['rn'],0,20),ENT_QUOTES,"UTF-8");
	$rp = htmlspecialchars(mb_substr($_POST['rp'],0,150),ENT_QUOTES,"UTF-8");
	mysql_query ("UPDATE 3_users SET roz_name = '".trim($rn)."', roz_popis = '".trim($rp)."', roz_pj = ".($_POST['usePJ'] == 'yes' ? 1 : 0)." WHERE id = $_SESSION[uid]");
	//aktualni hodnoty
	$s = mysql_fetch_object(mysql_query ("SELECT roz_name,roz_popis,roz_pj FROM 3_users WHERE id = $_SESSION[uid]"));
	$vuItem->roz_name = $s->roz_name;
	$vuItem->roz_popis = $s->roz_popis;
	$vuItem->roz_pj = $s->roz_pj;
	$ok = 2;
break;

// pridani XP
case 5:
	if($isadmin){
		$uiid = intval($_POST['user']);
		if ($uiid < 2) {
		}
		else {
			$us = mysql_fetch_object(mysql_query ("SELECT login FROM 3_users WHERE id = '$uiid'"));
			mysql_query ("UPDATE 3_users SET roz_exp = roz_exp + 1 WHERE id = '$uiid'");
			if ($_POST['reas'] == 'x') {
				$_POST['reas'] = $_POST['reas2'];
			}
			$text = addslashes("$_SESSION[login] udělil XP uživateli $us->login za $_POST[reas].");
$FILE = '../xplog.txt';
$put = date('d.m.Y H:i - ') . stripslashes($text) . "\n" . file_get_contents($FILE);
file_put_contents($FILE, $put, LOCK_EX);
			mysql_query ("INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, $_GET[id], $time, '$text')");
		  $lastInserted = mysql_insert_id();
	 		if ($vuItem->saving > 0) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (0, $_GET[id], 0, $lastInserted, '$text', $time)");
			$ok = 5;
		}
	}
break;

// odebrani XP
case 6:
	if($isadmin){
		$uiid = intval($_POST['user']);
		if ($uiid < 2){
		}
		else {
			$reas = $_POST['reas'];
			$us = mysql_fetch_object(mysql_query ("SELECT login FROM 3_users WHERE id = '$uiid'"));
			mysql_query ("UPDATE 3_users SET roz_exp = roz_exp - 1 WHERE id = '$uiid'");
			$text = addslashes("$_SESSION[login] odebral XP uživateli $us->login za $reas.");
$FILE = '../xplog.txt';
$put = date('d.m.Y H:i - ') . stripslashes($text) . "\n" . file_get_contents($FILE);
file_put_contents($FILE, $put, LOCK_EX);
			mysql_query ("INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, $_GET[id], $time, '$text')");
		  $lastInserted = mysql_insert_id();
	 		if ($vuItem->saving > 0) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (0, $_GET[id], 0, $lastInserted, '$text', $time)");
			$ok = 5;
		}
	}
break;

case 7:
	if($isadmin){
		$situace = addslashes($_POST['situace']);
		$sitS = mysql_query ("SELECT nazev,popis,category FROM 3_roz_situace WHERE id = '$situace' AND nadrazena = '0'");
		if (mysql_num_rows($sitS)>0){
			$sit = mysql_fetch_object($sitS);
			$text = "<span class=\'vypravec\'>".addslashes($sit->nazev)."</span><br />".addslashes($sit->popis);
			mysql_query("INSERT INTO 3_chat_mess (uid, rid, time, text, special, type) VALUES (1, $_GET[id], $time, '$text', '$situace', 1)");
		  $lastInserted = mysql_insert_id();
  		if ($vuItem->saving > 0) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (1, $_GET[id], 0, $lastInserted, '$text', $time)");
			mysql_query("UPDATE 3_chat_rooms SET category = '$sit->category', popis = '".addslashes($sit->nazev)."' WHERE id = $_GET[id]");
			$ok = 7;
		}
	}else{
		$canStartVote = mysql_fetch_object(mysql_query("SELECT vote_time FROM 3_chat_rooms WHERE  id = $_GET[id]"));
		if($canStartVote->vote_time < time() - 5*60){
			$situace = addslashes($_POST['situace']);
			mysql_query("DELETE FROM 3_chat_votes WHERE rid = $_GET[id]");
			mysql_query("UPDATE 3_chat_rooms SET vote_uid = " . $_SESSION['uid'] . ", vote_time = " . time() . ", vote_situation = '$situace' WHERE id = $_GET[id]");
			$ok = 11;
		}else{
			$error = 6;
		}
	}
break;

case 8:
	if($isadmin){
		$podsituace = addslashes($_POST['podsituace']);
		$sitS = mysql_query ("SELECT id,popis FROM 3_roz_situace WHERE id = '$podsituace'");
		if (mysql_num_rows($sitS)>0){
			$sit = mysql_fetch_object($sitS);
			mysql_query("INSERT INTO 3_chat_mess (uid, rid, time, text, special, special2, type) VALUES (1, $_GET[id], $time, '".addslashes($sit->popis)."', 0, $podsituace, 1)");
		  $lastInserted = mysql_insert_id();
  		if ($vuItem->saving > 0) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (1, $_GET[id], 0, $lastInserted, '".addslashes($sit->popis)."', $time)");
			$ok = 8;
		}
	}
break;

case 9:
	if ($isadmin) {
		$uN = mysql_fetch_row(mysql_query ("SELECT login, level, ip FROM 3_users WHERE id = '".addslashes($_POST['kick'])."'"));
		if ($uN[1] < 3){
			$text = "$_SESSION[login] vyhodil uživatele $uN[0] z místnosti.";
			mysql_query ("INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, $_GET[id], $time, '$text')");
		  $lastInserted = mysql_insert_id();
  		if ($vuItem->saving > 0) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (0, $_GET[id], 0, $lastInserted, '$text', $time)");
			mysql_query ("UPDATE 3_chat_users SET odesel=1 WHERE uid = '$_POST[kick]' AND rid=$_GET[id]");
			$ok = 9;
		}else{
		}
	}
break;

case 10:
	if ($isadmin) {
	  if (!isset($_POST['akce']) || $_POST['akce'] == "") {
		}
		else {
		  $act = $_POST['akce'];
		  if ($act == "end") {
			  $lastChatId = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM 3_chat_save_text WHERE rid = '$_GET[id]'"));
			  mysql_query("UPDATE 3_chat_save_data SET toId = '$lastChatId[0]', timeEnd = '$time', aktivni = '0' WHERE rid = '$_GET[id]' AND aktivni = '1'");
			  mysql_query("UPDATE 3_chat_rooms SET saving = '0' WHERE id = '$_GET[id]'");
				mysql_query("INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, $_GET[id], $time, '".addslashes("<span style='color:red;font-weight:bold'>Záznam hry v tomto Rozcestí byl ukončen.</span>")."')");
			}
			else if ($act == "new") {
			  $lastChatId = mysql_fetch_row(mysql_query("SELECT max(id) FROM 3_chat_save_text WHERE rid = '$_GET[id]'"));
			  mysql_query("UPDATE 3_chat_save_data SET toId = '$lastChatId[0]', timeEnd = '$time', aktivni = '0' WHERE rid = '$_GET[id]' AND aktivni = '1'");
			  mysql_query("INSERT INTO 3_chat_save_data (fromId, timeStart, aktivni, rid) VALUES ('".($lastChatId[0]+1)."', '$time', '1', '$_GET[id]')");
			  mysql_query("UPDATE 3_chat_rooms SET saving = '1' WHERE id = '$_GET[id]'");
				mysql_query("INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, $_GET[id], $time, '".addslashes("<span style='color:red;font-weight:bold'>Byl zapnut záznam hry v tomto Rozcestí. Od této chvíle veškeré texty (i šeptané) jsou do odvolání zaznamenávané.</span>")."')");
			}
			else if ($act == "show" && isset($_POST['data']) && $_POST['data'] > 0) {
			  $what = addslashes($_POST['data']);
			  $toShow = mysql_query("SELECT * FROM 3_chat_save_data WHERE rid = '$_GET[id]' AND aktivni = '0' AND id = '$what'");
			  if ($toShow && mysql_num_rows($toShow)>0) {
			    header("Location: http://".$_SERVER['HTTP_HOST']."/saves.php?id=$what");
			    exit;
				}
			}
			else if ($act == "delete" && isset($_POST['data']) && $_POST['data'] > 0) {
			  $what = addslashes($_POST['data']);
			  $toShow = mysql_query("SELECT * FROM 3_chat_save_data WHERE rid = '$_GET[id]' AND aktivni = '0' AND id = '$what'");
			  if ($toShow && mysql_num_rows($toShow)>0) {
			    $selecta = mysql_fetch_object($toShow);
			    mysql_query("DELETE FROM 3_chat_save_text WHERE rid = '$_GET[id]' AND id >= $selecta->fromId AND id <= $selecta->toId");
			    mysql_query("DELETE FROM 3_chat_save_data WHERE id = '$what'");
				}
			}
		}
	}
break;

case 13:
	if ($isadmin) {
		if (!isset($_POST['banroz']) || $_POST['banroz'] == "") {
		}
		else {
			$uN = mysql_fetch_row(mysql_query ("SELECT login, level, ip FROM 3_users WHERE id = '".addslashes($_POST['banroz'])."'"));
			if ($uN[1] < 3){
				$sql = "INSERT INTO 3_chat_admin (uid, typ, cas) VALUES ('$_POST[banroz]', '-1', '".(3600+$time)."')";
				mysql_query($sql);

				$text = addslashes("$uN[0] má po dobu 60 minut zákaz vstupu na Rozcestí. Udělil(a) $_SESSION[login].");

				$sql = "INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, $_GET[id], $time, '$text')";
				mysql_query($sql);

			  $lastInserted = mysql_insert_id();
	  		if ($vuItem->saving > 0) mysql_query("INSERT INTO 3_chat_save_text (uid, rid, tid, mid, text, cas) VALUES (0, $_GET[id], 0, $lastInserted, '$text', $time)");

				mysql_query("DELETE FROM 3_chat_users WHERE uid = '".addslashes($_POST['banroz'])."' AND prava = 0 AND rid = $_GET[id]");

				$ok = 10;
			}else{
			}
		}
	}
break;


case 14:
	if ($isadmin) {
		if (!isset($_GET['a']) || !isset($_GET['uid']) || $_GET['a'] == "" || $_GET['uid'] == "") {
		}
		else {
			mysql_query("DELETE FROM 3_chat_admin WHERE typ='-1' AND uid='".addslashes($_GET['uid'])."' AND id='".addslashes($_GET['a'])."'");
		}
	}
break;

}
}

?>