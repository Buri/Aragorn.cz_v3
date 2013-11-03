<?php
if ($LogedIn) {
//if ($LogedIn) {

  $banIp = explode("@", addslashes($ipe));
  $banIp = implode("%' OR b.ipe LIKE '%", $banIp);
	$uid = $row->uid;

	mysql_query("DELETE FROM 3_ban AS d WHERE d.assignedin + d.time <= " . time());
	$sB = mysql_query ("SELECT b.id, b.uid, b.reason, b.time, b.assignedin, u.ip, u.login, v.login AS admin FROM 3_ban AS b LEFT JOIN 3_users AS u ON u.id = b.uid LEFT JOIN 3_users AS v ON v.id = b.fid WHERE b.uid = '$uid' OR b.ipe LIKE '%".$banIp."%'");

	if (mysql_num_rows($sB) > 0) {

		$oB = mysql_fetch_object($sB);
	  if (($oB->time + $oB->assignedin) > time()) {
			$day = date("d.m.Y \v H:i", $oB->assignedin);
			$day2 = date("d.m.Y \v H:i", $oB->assignedin + $oB->time);
			$reason = stripslashes($oB->reason);

			$multinick = "";
			$nicking = "<p>Dne <b>$day</b>";

			if ($oB->uid != $uid) {
				$multinick = "<p>Nick, ze kterého se hlásíte ($login), má naneštěstí shodnou internetovou adresu.</p>\n";
				$nicking .= " byl udělen ban (zákaz přístupu) od admina/správce <u>$oB->admin</u> na&nbsp;uživatelské jméno <u>$oB->login</u> z&nbsp;internetové adresy <u>$oB->ip</u></p>";
			}
			else {
				$nicking .= " byl udělen ban (zákaz přístupu) od admina/správce <u>$oB->admin</u>.</p>";
			}
			$nicking .= "\n".$multinick;

			new_auth_do_clear($login_rew);
			$_SESSION = array();
      session_destroy();
      session_write_close();
			mysql_query("UPDATE 3_users SET online = '0', timestamp = '0' WHERE id = $uid");

			$LogedIn = false;
  	  die("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-language\" content=\"cs\" />
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<meta http-equiv=\"pragma\" content=\"no-cache\" />
<meta name=\"Description\" content=\"Aragorn.cz - Ban\" />
<meta name=\"Keywords\" content=\"\" />
<style type='text/css'>
body { background-color: #EEE; color: #333; font-family: Verdana, Tahoma, Arial, sans; text-align: center; }
div { width: 500px; margin: 0 auto; text-align: left; }
h1 { display: block; font-size: 180%; border-bottom: 1px solid #999; margin: 10px 0; }
h2 { margin: 5px 0; font-size: 120%; }
p { text-align: left; font-size: 100%; line-height: 1.2em; }
small { font-size: 70%; }
</style>
<title>Aragorn.cz - Ban</title>
</head>
<body>
<div>
<h1>Ban</h1><h2>aneb jak Vy k nám, tak my k Vám</h2>
$nicking
<p>Důvod: $reason</p>
<p>Ban vyprší <b>$day2</b></p>
<p>V případě pochybností či důvodů, proč by Vám měl být ban zrušen, se obraťte na mail ixiik&#64;aragorn.cz a do předmětu uveďte \"ban\" a Váš nick. Obsah zprávy by měl vysvětlit, proč si myslíte, že je ban nespravedlivý a podobné informace. Omluvy bývají taktéž na místě.</p>
<p>Systém Vás odhlásil, nadále si můžete server <a href='/' title='Aragorn.cz - nejen online herna'>Aragorn.cz</a> prohlížet jako nepřihlášený uživatel.</p>
<p><small>Další přihlášení před vypršením doby platnosti banu skončí opět na této stránce.</small></p>
</div>
</body>
</html>");
			exit;
  	}
  	else {
	  	mysql_query("DELETE FROM 3_ban WHERE id = $oB->id");
		}
	}
}

?>
