<?php

if (isset($_SESSION['lvl'],$_SESSION['uid'])) {
	if ($_SESSION['lvl'] == 3) {
		$hasRightS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = '$_SESSION[uid]' AND chat = 1"));
		$hasRight = $hasRightS[0];
	}
	else if ($_SESSION['lvl']>3) $hasRight = 1;
	else $hasRight = 0;
}
else {
	$hasRight = 0;
}

if ($slink == "ad" && $hasRight){

  $title = "Chat - admin";

}else{

  $title = "Chat";
  
}
if (!$GLOBAL_description) $GLOBAL_description = "Chat, online, on-line, live, živě. Přijďte si s námi psát na chat. Je to skoro jako rozhovor naživo.";
?>
