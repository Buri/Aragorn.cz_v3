<?php

$requireRights['uzivatele'] = false;

function uzivatele_head($rub) {
}

function uzivatele_body() {
	global $dbCnt;
	$uid = "";
	if (!isSet($_GET['id']) || !ctype_digit($_GET['id']) || $_GET['id'] < 2){
		echo "<p class='error'>Nebylo zadáno ID uživatele</p>";
	}else {
		$uid = addslashes($_GET['id']);
		$isUserS = mysql_query("SELECT * FROM 3_users WHERE id = '$uid'");
		$dbCnt++;
		if (mysql_num_rows($isUserS)>0) {
			$isUser = mysql_fetch_object($isUserS);
			$uid = $isUser->id;
			$login_user = $isUser->login;
			$login_rew = $isUser->login_rew;
		}
		else {
			$uid = 0;
		}
		if ($uid <= 1) {
			$uid = 0;
			echo "<p class='error'>ID uživatele nenalezeno</p>";
		}
		else {
			$uid = $uid;
		}
	}

if ($uid > 1){
	echo "<p>Uživatel(ka) <strong>".$login_user."</strong> :: <a href='/uzivatele/$login_rew/'>profil</a> via Aragorn.cz</p>\n<p>";
	if ($isUser->timestamp > 0) {
		echo "<span class='ok'>Online</span> - poslední akce v ".date("H:i:s", $isUser->timestamp);
	}
	else {
		if ($isUser->login_count > 0)
			echo "<span class='error'>Offline</span> - naposledy online ".date("j.n.Y H:i:s", $isUser->last_login);
		else
			echo "<span class='error'>Offline</span> - nulový počet přihlášení";
	}
	echo "</p>\n<table border='1' cellspacing='2' cellpadding='4' style='width:90%;text-align:center;margin: 10px auto;'>\n";
	echo "<thead><tr><th>Články</th><th>Galerie</th><th>Jeskyně</th></tr></thead>\n";
	echo "<tbody>\n";
	echo "<tr style='vertical-align: top;text-align:left'><td>\n";
	$fId = mysql_query("SELECT id,nazev FROM 3_clanky WHERE autor = '$uid'");
	$dbCnt++;
	if (mysql_num_rows($fId) > 0){
		echo "<ul>\n";
		while ($oI = mysql_fetch_object($fId)) {
			echo "<li><a href='/rs/clanky/?action=view&amp;id=$oI->id'>".stripslashes($oI->nazev)."</a></li>\n";
		}
		echo "</ul>\n";
	}
	else {
		echo "<p>Žádný článek</p>\n";
	}

	echo "</td><td>\n";

	unset($fId);
	$fId = mysql_query("SELECT id,nazev,thumb FROM 3_galerie WHERE autor = '$uid'");
	$dbCnt++;
	if (mysql_num_rows($fId) > 0){
		echo "<style type='text/css'>td img {border:none}</style>\n";
		while ($oI = mysql_fetch_object($fId)) {
			echo "<a title='"._htmlspec(stripslashes($oI->nazev))."' href='/rs/galerie/?action=view&amp;id=$oI->id'><img title='"._htmlspec(stripslashes($oI->nazev))."' src='/galerie/$oI->thumb' /></a>\n";
		}
	}
	else {
		echo "<p>Žádný obrázek</p>\n";
	}

	echo "</td><td>\n";

	unset($fId);
	$fId = mysql_query("SELECT id,nazev FROM 3_herna_all WHERE uid = '$uid'");
	$dbCnt++;
	if (mysql_num_rows($fId) > 0){
		echo "<ul>\n";
		while ($oI = mysql_fetch_object($fId)) {
			echo "<li><a href='/rs/herna/?action=view&amp;id=$oI->id'>".stripslashes($oI->nazev)."</a></li>\n";
		}
		echo "</ul>\n";
	
	}
	else {
		echo "<p>Nevlastní žádné jeskyně.</p>\n";
	}

	$fId = mysql_query("SELECT h.id, h.nazev FROM 3_herna_all AS h, 3_herna_pj AS p WHERE p.cid = h.id AND p.uid = '$uid'");
	$dbCnt++;
	if (mysql_num_rows($fId) > 0) {
		echo "<ul>\n";
		while ($oI = mysql_fetch_object($fId)) {
			echo "<li>Pomocný PJ: <a href='/rs/herna/?action=view&amp;id=$oI->id'>".stripslashes($oI->nazev)."</a></li>\n";
		}
		echo "</ul>\n";

	}
	else {
		echo "<p>Není nikde pomocným PJ.</p>\n";
	}

	echo "</td></tr>";
	echo "</tbody>\n";
	echo "</table>\n";

}
// END BODY FUNC
}
?>
