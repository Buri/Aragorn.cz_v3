<?php
if ($link != "chat" || $_SESSION['lvl'] < 3) {
  	Header ("Location: $inc/chat/");
	exit;
}

if (isset($_POST['chat_action']) && $_POST['chat_action'] == "delete" && ctype_digit($_POST['chat_room'])) {
	mysql_query("DELETE FROM 3_chat_rooms WHERE staticka = '0' AND id = '$_POST[chat_room]'");
	if (mysql_affected_rows()) {
		$ok = 3;
		mysql_query("DELETE FROM 3_chat_users WHERE rid = '$_POST[chat_room]'");
		mysql_query("DELETE FROM 3_chat_mess WHERE rid = '$_POST[chat_room]'");
		mysql_query("DELETE FROM 3_chat_filters WHERE rid = '$_POST[chat_room]'");
	}
	else $error = 7;
}
elseif (isset($_POST['chat_action']) && $_POST['chat_action'] == "adjust") {
	if ($_POST['chat_room'] < 1 || !ctype_digit($_POST['chat_room'])){
	  $error = 3;
	}elseif (mb_strlen(trim($_POST['chat_nazev'])) < 3){
	  $error = 1;
	}elseif(mb_strlen(trim($_POST['chat_popis'])) < 3){
	  $error = 2;
	}elseif($_POST['chat_type'] > 2 || $_POST['chat_type'] < 0 || !ctype_digit($_POST['chat_type'])){
	  $error = 8;
	}else{

		$nazev = htmlspecialchars(trim($_POST['chat_nazev']),ENT_QUOTES,"UTF-8");
		$popis = htmlspecialchars(trim($_POST['chat_popis']),ENT_QUOTES,"UTF-8");

		$roomObj = mysql_fetch_object(mysql_query("SELECT * FROM 3_chat_rooms WHERE id = '$_POST[chat_room]'"));
		if ($roomObj->staticka) {
			mysql_query ("UPDATE 3_chat_rooms SET nazev = '$nazev', popis = '$popis' WHERE id = '$_POST[chat_room]'");
		}
		else {
			$type = 0;
			if ($_POST['chat_type'] > 0) $type = 1;
	
	
			if ($type > 0){
				$cat = "category = '".roz_types($_POST['chat_type'])."', ";
			}
			mysql_query ("UPDATE 3_chat_rooms SET nazev = '$nazev', $cat popis = '$popis', type = '$type' WHERE id = '$_POST[chat_room]'");
		}
		if (mysql_affected_rows()) $ok = 2;
		else $error = 7;
	}
}
else {
	header("Location:$inc/$link/$slink/");
	exit;
}


if (isSet($ok)){
Header ("Location:$inc/$link/$slink/?ok=$ok");
}else{
Header ("Location:$inc/$link/$slink/?error=$error");
}
exit;
?>
