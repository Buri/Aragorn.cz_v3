<?php

$id = -1;

if (isset($_GET['error'])) {
	$error = $_GET['error'];
}
elseif (isset($_GET['ok'])) {
	$error = $_GET['ok'];
}

if ($slink!=""){
	$slinkO = $slink;
	$slink = mb_strtolower((addslashes($slink)));
	if ($slink != $slinkO) {
		header("Location: $inc/uzivatele/$slink/");
		exit;
	}
	$uIs = mysql_query ("SELECT u.*,ua.about_me FROM 3_users AS u LEFT JOIN 3_users_about AS ua ON ua.uid = u.id WHERE u.login_rew = '$slink'");
//	$uIs = mysql_query ("SELECT u.*,ua.about_me FROM 3_users AS u LEFT JOIN 3_users_about AS ua ON ua.uid = u.id WHERE u.login_rew = '$slink' AND u.reg_code = 0");

	if ($uIs && mysql_num_rows($uIs)>0){
		$uI = mysql_fetch_object($uIs);
		
		$id = $uI->id;
		$user = stripslashes($uI->login);
		$ico = $uI->ico;
		$level = $uI->level;
		
		if (mb_strlen($uI->name) > 0) $jmeno = stripslashes($uI->name);
		else $jmeno = "<span style='font-style: italic'>nevyplněno</span>";

		if (mb_strlen($uI->city) > 0) $mesto = $uI->city;
		else $mesto = "<span style='font-style: italic'>nevyplněno</span>";

		$roz_name = $uI->roz_name;
		$roz_popis = $uI->roz_popis;
		$roz_xp = $uI->roz_exp;
		$roz_ico = $uI->roz_ico;
		$ipp;
		if ($_SESSION['lvl'] > 2) {
			$multi = mysql_query("SELECT login,login_rew FROM 3_users WHERE ip='$uI->ip';");
			$multinicks = array();
			while($mn = mysql_fetch_object($multi))
				$multinicks[] = "<a href='/uzivatele/".$mn->{'login_rew'}."/'>".$mn->login."</a>";
			$multinicks = implode(', ', $multinicks);
			$ipp = "<tr><td>IP :</td><td>".$uI->ip." (".$multinicks.")</td></tr>\n<tr><td>Mail :</td><td>".$uI->mail." (<a href=\"http://www.facebook.com/search/results.php?q=" . rawurlencode($uI->mail) . "\" target=\"_blank\">FB search</a>, 
<a href=\"https://www.google.com/search?btnG=1&pws=0&q=$uI->mail\" target=\"_blank\">Google</a>,
<a href=\"http://who.is/whois-ip/ip-address/$uI->ip\" target=\"_blank\">WhoIS</a>)</td></tr>\n";
		}

		if (mb_strlen($uI->icq) > 0){
			$icq_number = preg_replace('/[^0-9]/', '', $uI->icq);
			if (strlen($icq_number) > 5) {
				$icq = "$icq_number <img src=\"http://status.icq.com/online.gif?icq=".$icq_number."&amp;img=5\" alt='ICQ status' title='ICQ status' />";
			}
			else {
				$icq = "<span style='font-style: italic'>"._htmlspec($uI->icq)."</span>";
			}
		}else $icq = "<span style='font-style: italic'>nevyplněno</span>";
		$lastLogin = "";
		if ($uI->last_login > 0 || $uI->login_count > 0){
			if ($uI->timestamp == 0) {
			  $status = "offline";
				$lastAction = "<span style='font-style: italic'>není online</span>";
			  if ($uI->last_login > 0) $lastLogin = "		<tr><td width='40%'>Naposledy online :</td><td>".sdh($uI->last_login)."</td></tr>";
				else $lastLogin = "		<tr><td width='40%'>Poslední přihlášení :</td><td><span style='font-style: italic'>nelze zjistit</span></td></tr>";
			}
			else {
				$lastAction = date("H:i",$uI->timestamp);
				$lastac = $time-$uI->timestamp;
				if ($lastac < 120) {
					$lastac = "&lt; 2 minuty";
				}
				elseif ($lastac < 300) {
					$lastac = "&lt; 5 minut";
				}
				elseif ($lastac < 600) {
					$lastac = "&lt; 10 minut";
				}
				else {
					$lastac = "&gt; 10 minut";
				}
				$status = "online";
				$lastAction .= " (".$lastac.")";
			  if ($uI->last_login > 0) $lastLogin = "		<tr><td width='40%'>Poslední přihlášení :</td><td>".sdh($uI->last_login)."</td></tr>";
				else $lastLogin = "		<tr><td width='40%'>Poslední přihlášení :</td><td><span style='font-style: italic'>nelze zjistit</span></td></tr>";
			}
		}
		else{
			$lastLogin = "		<tr><td width='40%'>Poslední přihlášení :</td><td><span style='italic'>zatím neproběhlo</span></td></tr>";
			$lastAction = "<span style='font-style: italic'>není online</span>";
			$status = "offline";
		}

		$status = "<span class='$status' style='position: relative; right: 30px'></span>";

		$accCreated = sd($uI->account_created);

		if($uI->level == 2){
			$bC = sd($uI->bonus_expired);
			$bon = "<tr><td width='40%'>Bonus vyprší :</td><td>$bC</td></tr>";
		}
		
		if (mb_strlen($uI->about_me) > 0) $about = spit($uI->about_me, 1);

		if ($id > 0) {
			$GLOBAL_description = "Profil uživatele $user, diskuze, články, obrázky z galerie, hry a postavy z herny, přátelé, komentáře uživatelů.";
			$title = "Profil uživatele $user | Uživatelé";
		}
		else {
			$title = "Systém | Uživatelé";
		}


	}else{
			Header ("Location: $inc/uzivatele/?error=1");
		exit;
	}
}else{
	$title = "Uživatelé";
}

if (!isSet($_GET['index'])) $pg_index = 1;
else $pg_index = (int)($_GET['index']);
if ($pg_index < 2) $pg_index = 1;

if ($pg_index > 1) {
	$pg_index--;
	$konc = "a";
	if ($pg_index > 1) {
		if ($pg_index > 4) $konc = "";
		else $konc = "y";
	}
	$title .= " ($pg_index stran$konc zpět)";
}
elseif ($pg_index  == 1) {
	if ($slink == "") $title .= " (1. strana)";
}

?>