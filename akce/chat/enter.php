<?php
if ($link != "chat" || $LogedIn == false || !isSet($_GET['id'])) {
  	Header ("Location: $inc/chat/");
	exit;
}
if (!ctype_digit($_GET['id'])) {
  Header ("Location: $inc/chat/");
	exit;
}

$cItem = mysql_fetch_object(mysql_query ("SELECT id, locked, type, elite FROM 3_chat_rooms WHERE id = '$_GET[id]'"));
$cU = mysql_fetch_row (mysql_query("SELECT count(*) FROM 3_chat_users WHERE rid = '$_GET[id]' AND odesel = '0'"));
$xp = mysql_fetch_row(mysql_query("SELECT roz_exp FROM 3_users WHERE id = '$_SESSION[uid]'"));

if ($_SESSION['lvl']>2) {
	if ($_SESSION['lvl']>3) $cPrava = array(1,1);
	else {
		$selS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = $_SESSION[uid] AND chat = 1"));
		$cPrava = $selS;
	}
}
else {
	$cPrava = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_admin WHERE typ='$cItem->type' AND uid = '$_SESSION[uid]'"));
}

if ($cItem->id < 1){
  $error = 4;
}elseif ($cItem->locked == "1" && $_SESSION['lvl'] < 3){
  $error = 5;
}elseif ($cItem->type > 0 && $cPrava[0]<1 && $cU[0] > 5){
  $error = 6;
}elseif ($cItem->elite && $xp[0] < 2) {
	$error = 5;
}else{

	//oznameni o prichodu ajax/normal
	function insertInfo($id,$text){
		global $time;
	  if($id > 1){
	  	$text = addslashes($text);
	    mysql_query ("INSERT INTO 3_chat_mess (uid, rid, time, text) VALUES (0, $id, $time, '$text')");
	  }else{
			//system upozorni na novyho negra
			ajaxChatInsertSystem($text, $id);
	  }
	}

  $cUsrc = mysql_query ("SELECT odesel FROM 3_chat_users WHERE uid = '$_SESSION[uid]' AND rid = '$_GET[id]'");
if($cPrava[0] > 0){

		mysql_query("UPDATE 3_chat_rooms SET need_admin = 0 WHERE id = " . $_GET['id']);
}
	if (mysql_num_rows($cUsrc)>0){ // user is in room = only update
		$cU = mysql_fetch_row($cUsrc);
		if ($cU[0]==0) {
			mysql_query ("UPDATE 3_chat_users SET prava = '$cPrava[0]', timestamp = '$time', odesel = '0' WHERE uid = '$_SESSION[uid]' AND rid = '$_GET[id]'");
		}
		else {
			mysql_query ("UPDATE 3_chat_users SET prava = '$cPrava[0]', timestamp = $time, odesel = '0' WHERE uid = '$_SESSION[uid]' AND rid = '$_GET[id]'");
			$text = $_SESSION['login']." se vrací do místnosti.";
			insertInfo($_GET['id'],$text);
		}
	} else { // user is not in room = perform insert and incoming text
	  mysql_query ("INSERT INTO 3_chat_users (uid, rid, timestamp, prava) VALUES ($_SESSION[uid], $_GET[id], $time, '$cPrava[0]')");
		$text = $_SESSION['login']." přichází do místnosti.";
	  if ($cItem->type > 0 && $cPrava[0] > 0){
		 $text = "Správce ".$_SESSION['login']." přichází do místnosti.";
	}
	  insertInfo($_GET['id'],$text);
	}

	if (isset($AragornCache)) {
		$AragornCache->replaceVal("chat-room-".$_GET['id'].":users-".$_SESSION['uid'], array(
			'uid' => $_SESSION['uid'],
			'prava' => $cPrava[0],
			'odesel' => 0,
			'timestamp' => $time,
			'rid' => $_GET['id']
		), 900);
	}

}
if ($cItem->type > 0) { // Rozcesti?
	$chatUser = mysql_fetch_object( mysql_query ("SELECT id, roz_name, roz_popis FROM 3_users WHERE id = ".$_SESSION['uid'].""));	
	if ($chatUser->roz_name == "" || $chatUser->roz_popis == ""){ // pokud nema vyplnene polozky, posle mu septanou zpravu od systemu
		$time = time();
		$txt_Warn = "Nemáte vyplněné jméno Vaší postavy, nebo popis postavy. Ostatní hráči tak nevědí, s kým hrají. Chtělo by to napravit ;-) . Učinit tak můžete dole pod tlačítkem <a href=\"game.php?id=".$_GET[id]."&amp;set=1\" title=\"Nastavení\">Nastavení</a>.";
//		$txt_Warn = $chatUser->roz_name." ".$chatUser->roz_popis." ".$_SESSION[uid];
		mysql_query ("INSERT INTO 3_chat_mess (uid, rid, time, text, wh, special) VALUES (0, $_GET[id], $time, '$txt_Warn', $_SESSION[uid], 0)");
	}
}

if ($_GET['id'] > 1) {
	header("Location: $inc/room/$_GET[id]/");
} else {
	header("Location: $inc/ajax_room/$_GET[id]/");
}
exit;
