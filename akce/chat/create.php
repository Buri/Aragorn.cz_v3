<?php
mb_internal_encoding("UTF-8");

if ($link != "chat" || $_SESSION['lvl'] < 3 || !$hasRight) {
  Header ("Location: $inc/chat/");
	exit;
}

if (mb_strlen(trim($_POST['chat_nazev'])) < 3){
  $error = 1;
}elseif(mb_strlen(trim($_POST['chat_popis'])) < 3){
  $error = 2;
}elseif(ctype_digit($_POST['chat_type']) && $_POST['chat_type'] != ""){
	$numStatika = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_rooms WHERE staticka = '0'"));
	$numStatika = (int)$numStatika[0];
	if ($numStatika<2) {
		$nazev = htmlspecialchars(trim($_POST['chat_nazev']),ENT_QUOTES,"UTF-8");
		$popis = htmlspecialchars(trim($_POST['chat_popis']),ENT_QUOTES,"UTF-8");
		$type = 0;
		if ((int)$_POST['chat_type'] > 0) $type = 1;

		$catA = $catB = "";
		if ($type > 0){
			$catA = ", category";
			$catB = ", '".roz_types($_POST['chat_type'])."'";
		}

		$sql = "INSERT INTO 3_chat_rooms (nazev, popis, type$catA) values ('$nazev','$popis','$type'$catB)";
		mysql_query($sql);
	  $ok = 1;
	}
	else $error = 9;
}
else {
	$error = 8;
}

if (isSet($ok)){
	Header ("Location:$inc/$link/$slink/?ok=$ok");
}else{
	Header ("Location:$inc/$link/$slink/?error=$error");
}

exit;
?>