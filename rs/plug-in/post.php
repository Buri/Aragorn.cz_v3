<?php

$menuLinks['post'] = "Sys. Pošta";
$requireRights['post'] = true;

function post_head($rub) {
global $time;
switch ($_GET['op']) {
	case 1:
		if (strlen($_POST['text']) < 6){
			$info = 2;
		}else{
			$sql_add = "";
			if (isset($_POST['jen_adminum']) && $_POST['jen_adminum'] == "only") {
				$sql_add = " AND level>2";
			}
			elseif (isset($_POST['jen_adminum']) && $_POST['jen_adminum'] == "myself"){
				$sql_add = " AND id=$_SESSION[uid]";
			}
			$timeToSend = $time - 3600*24*30;
			$sel = mysql_query("SELECT id FROM 3_users WHERE last_login > $timeToSend $sql_add");
			$us = array();
			while ($o = mysql_fetch_row($sel)){
				$us[] = $o[0];
			}
			$r = sysPost($us, $_POST['text']);
			$info = '1&c='.$r;
		}
	break;
}
Header ("Location: /rs/$rub/?info=$info");
exit;
}

function post_body() {
	switch($_GET['info']){
		case 1:
			echo "<span class='ok'>Ok: pošta rozeslána</span>";
		break;
		case 2:
			echo "<span class='error'>Chyba: text musí mít alespoň 6 znaků</span>";
		break;
	}
	echo "<h2>Rozeslat hromadnou systémovou poštu</h2>";
	echo "<form action='/rs/post/?op=1' method='post'>";
	echo "<table width='80%'>";
	echo "<tr><td width='20%' valign='top'>Text</td><td><textarea rows='13' cols='50' name='text'></textarea></td></tr>";
	echo "<tr><td colspan='2'><label for='vsem_userum'><input id='vsem_userum' name='jen_adminum' type='radio' value='all' /> všem uživatelům</label> <label for='jen_sobe'><input id='jen_sobe' name='jen_adminum' type='radio' value='myself' /> jen sobě</label> <label for='jen_adminum'><input id='jen_adminum' name='jen_adminum' type='radio' value='only' /> jen adminům</label></td></tr>";
	echo "<tr><td colspan='2' align='center'><input type='submit' value='Rozeslat' /><input type='button' value='Zavřít' onClick=\"window.location.href='/rs/post/'\" /></td></tr>";
	echo "</table>";
	echo "</form>";
// END BODY FUNC
}
?>
