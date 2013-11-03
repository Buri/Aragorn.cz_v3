<?php

$LogedIn = false;
$time = time();
$error = 0;

mb_internal_encoding("UTF-8");

//cesta
$inc = "http://".$_SERVER['HTTP_HOST'];
$zalozkyOmezeniCount = 20;

//spojeni s db
	session_set_cookie_params(4800, '/', 'www.aragorn.cz');
	session_start();

$zal = false;

if (isset($_GET['challenge'])) {
	$cookieWriter = "Cookie.write('".session_name()."', '".session_id()."', {domain: '".$_SERVER['HTTP_HOST']."', path:'/'});";
	if (isset($_SESSION['challenge'])) {
		header("Content-type: text/javascript; charset=utf-8;");
		echo "$('f_id_challenge').setProperty('value', '".$_SESSION['challenge']."');";
		echo $cookieWriter;
		unset($_SESSION['challenge']);
	}
	else {
		$noOutputBuffer = true;
		include "./db/conn.php";
		header("Content-type:text/javascript;charset=utf-8;");
		mysql_query("INSERT INTO 3_challenges (created) VALUES ('".date("Y-m-d H:i:s",$time-5)."')");
		$challenge = mysql_insert_id();
		$_SESSION['challenge'] = $challenge;
		echo "$('f_id_challenge').setProperty('value', '".$challenge."');";
		echo $cookieWriter;
		exit;
	}
}

	$SEZENI = $_SESSION;
	session_write_close();
	$ghtR47SHq = "_SESSION";
	$$ghtR47SHq = $SEZENI;

if (isset($_GET['zal'])) {
	if ($_GET['zal'] == "true") {
		$zal = true;
	}
	else {
		$zal = false;
	}
}

switch ($_GET['do']) {
	case "game-name":
	case "clanky-name":
	case "clanek-name":
		$error = "Název musí být delší než 3 znaky!";
		header("Content-type:text/html;charset=utf-8;");
		if (isset($_REQUEST['nazev'])) {
			include "./db/conn.php";
			include "./add/funkce.php";
			include "./add/rewrite.php";
			$nm = trim($_REQUEST['nazev']);
			if (mb_strlen($nm)<4) {
			}
			else {
				$nmR = addslashes(do_seo($nm));
				$nm = addslashes($nm);
				$id = intval($_REQUEST['id']);
				
				if ($_GET['do'] == 'game-name'){
					$q = mysql_query("SELECT count(*) FROM 3_herna_all WHERE (nazev = '$nm' OR nazev_rew = '$nmR') AND id != '$id'");
				}
				else {
					$q = mysql_query("SELECT count(*) FROM 3_clanky WHERE (nazev = '$nm' OR nazev_rew = '$nmR') AND id != '$id'");
				}

				$n = array_shift(mysql_fetch_row($q));
				if ($n > 0) {
					$error = "Podobný název již existuje";
				}
				else {
					$error = "1"; 
				}
			}
		}
		echo $error;
	break;
	case "game-hide":
	case "game-unhide":
		if (isset($_SESSION['login']) && isset($_SESSION['lvl']) && $_SESSION['login'] != "" && isset($_GET['cave']) && strlen($_GET['cave']) > 3) {
			$noOutputBuffer = true;
			include "./db/conn.php";
			include "./add/funkce.php";
			include "./add/rewrite.php";
			$jeOnline = mysql_query("SELECT online, id FROM 3_users WHERE id = $_SESSION[uid] AND online = 1 AND timestamp > ($time - 1800) AND reg_code = 0");
			if ($jeOnline && mysql_num_rows($jeOnline)>0) {
				$u = mysql_fetch_object($jeOnline);
				$LogedIn = true;
				if (isset($_SESSION['updated'])){
					if ($_SESSION['updated'] + 1800 < $time ){
						session_regenerate_id();
						$_SESSION["updated"] = $time;
					}
				}
				else {
					session_regenerate_id();
					$_SESSION["updated"] = $time;
				}
			}

			if ($LogedIn) {
				$n = addslashes($_GET['cave']);
				$games = mysql_query("SELECT id FROM 3_herna_all WHERE nazev_rew = '$n'");
				if ($games && mysql_num_rows($games)) {
					$game = mysql_fetch_object($games);
				}
				else {
					die('0');
				}

				$serials = mysql_query("SELECT serialized FROM 3_users_settings WHERE uid = $u->id");
				if ($serials && mysql_num_rows($serials)) {
					$serialized = mysql_fetch_object($serials);
					$serialized = json_decode($serialized->serialized, true);
					$exists = 1;
				}
				else {
					$exists = 0;
					$serialized = array();
				}

				if (!isset($serialized['game-hide'])) {
					$serialized['game-hide'] = array();
				}

        $serialized['game-hide'] = array_combine($serialized['game-hide'], $serialized['game-hide']);
				
				if ($_GET['do'] == 'game-hide' && $_SESSION['lvl'] >= 2) {
					$serialized['game-hide'][intval($game->id)] = intval($game->id);
				}
				else {
					unset($serialized['game-hide'][intval($game->id)]);
				}

        $serialized['game-hide'] = array_values($serialized['game-hide']);

				if ($exists) {
					$sql = "UPDATE 3_users_settings SET serialized = '".addslashes(json_encode($serialized))."' WHERE uid = '$_SESSION[uid]'";
				}
				else {
					$sql = "INSERT INTO 3_users_settings (uid, serialized) VALUES ($_SESSION[uid], '".addslashes(json_encode($serialized))."')";
				}
				
				$a = mysql_query($sql);
				$q = intval(mysql_affected_rows());
				echo $q;
//				die('');
			}

		}
	break;
	case "preview":
		if (isset($_SESSION['login']) && isset($_SESSION['lvl']) && $_SESSION['login'] != "" && isset($_POST['txt'])) {
			$noOutputBuffer = true;
			include "./db/conn.php";
			include "./add/funkce.php";
			include "./add/rewrite.php";
			$jeOnline = mysql_query("SELECT online FROM 3_users WHERE id = $_SESSION[uid] AND online = 1 AND timestamp > ($time - 1800) AND reg_code = 0");
			if ($jeOnline && mysql_num_rows($jeOnline)>0) {
				$u = mysql_fetch_row($jeOnline);
				$LogedIn = true;
				if (isset($_SESSION['updated'])){
					if ($_SESSION['updated'] + 1800 < $time ){
						session_regenerate_id();
						$_SESSION["updated"] = $time;
					}
				}
				else {
					session_regenerate_id();
					$_SESSION["updated"] = $time;
				}
			}
			if ($LogedIn) {
				echo spit(editor(trim($_POST['txt'])),1);
			}
		}
	break;
	case "postolka":
		$noOutputBuffer = true;
		$error = true;
		if (isset($_SESSION['login']) && isset($_SESSION['lvl']) && $_SESSION['login'] != "" && isset($_GET['num'])) {
			if ($_GET['num'] != "" && ctype_alnum($_GET['num'])) {
				$sslink = $_GET['num'];
				$noOutputBuffer = true;
				include_once "./db/conn.php";
				include_once "./add/memcache.php";
				include_once "./add/funkce.php";

			  if (_check_num($sslink,$_SESSION['uid'])) {
				  $num = addslashes(_decode_num($sslink,$_SESSION['uid']));
				  $messS = mysql_query("SELECT p.id,p.parent,p.stavto,p.stavfrom,p.fid,p.tid,t.content AS text,t.compressed FROM 3_post_new AS p INNER JOIN 3_post_text AS t ON t.id = p.mid WHERE p.id='$num' AND (p.tid = $_SESSION[uid] OR p.fid = $_SESSION[uid])");
				  if ($messS && mysql_num_rows($messS)>0) {
				    $pT = mysql_fetch_object($messS);
				    $error = false;
						if ($pT->compressed)
							$pT->text = gzuncompress($pT->text);

						if ($pT->stavto == '0' && $pT->tid == $_SESSION['uid']) {
							if (isset($_GET['read'])) {
								_postolka_read($pT);
								echo $sslink;
								exit;
							}
							else {
?>
							<p class="c4" id="unr_link_<?php echo $sslink;?>">Nepřečtená zpráva :: <a href="/posta/in/<?php echo $sslink;?>/" onclick="return readIt('<?php echo $sslink;?>');" class="permalink">Označit jako přečtenou</a></p>
<?php
							}
						}
?><p class='c4'><?php echo spit($pT->text, 1);?></p>
<?php
					}
				}
			}
		}
		if ($error) {
			echo "<strong class='hlight2'>Chyba načtení textu.<br />Přečti si poštolku klasickým způsobem (odkaz vlevo)</strong>";
		}
	break;
	case "checker":
		if (isset($_SESSION['login']) && isset($_SESSION['lvl']) && $_SESSION['login'] != "" && isset($_GET['sekce']) && isset($_POST['co']) && $_GET['sekce'] != "" && $_POST['co'] != "") {
			$noOutputBuffer = true;
			include "./db/conn.php";
			include "./add/funkce.php";
			include "./add/rewrite.php";
			@header("Content-Type: text/xml; charset=utf-8");
			echo "<"."?xml version=\"1.0\" encoding=\"utf-8\"?".">";

		  switch ($_GET['sekce']) {
		    case "clanky":
		    case "herna":
		    case "galerie":
		    case "diskuze":
		    case "posta":
				  $sekce = "3_".$_GET['sekce'];
		    break;
		    default:
		      $error = 800;
		    break;
			}
			$jeOnline = mysql_query("SELECT timestamp FROM 3_users WHERE id = $_SESSION[uid] AND online = 1 AND timestamp > ($time - 1800) AND reg_code = 0");
			if (mysql_num_rows($jeOnline)>0) {
				$u = mysql_fetch_row($jeOnline);
				$LogedIn = true;
				if (isset($_SESSION['updated'])){
					if ($_SESSION['updated'] + 1800 < $time ){
						session_regenerate_id();
						$_SESSION["updated"] = $time;
					}
				}
				else {
					session_regenerate_id();
					$_SESSION["updated"] = $time;
				}
				$t = $u[0];
			}
			if ($LogedIn) {
			  switch ($_GET['sekce']) {
					case "posta":
						if (isset($_POST['adv']) && $_POST['adv'] != "") {
						  $adv = explode(",",$_POST['adv']);
						  foreach ($adv as $v) {
						    $v = explode(":",$v);
						    switch ($v[0]) {
									case "us":
									  if ($v[1] < 3) $error = 401;
									break;
									case "mess":
									  if ($v[1] < 2) $error = 402;
									break;
								}
							}
						}
						else $error = 800;
						if (trim($_POST['co']) != "" && mb_strlen(trim($_POST['co'])) > 2) {
							$us = explode(",", $_POST['co']);
							for ($i=0;$i < count($us);$i++){
								if (trim(mb_strtolower($us[$i])) == mb_strtolower($_SESSION['login'])){
									$error = 400;
								  break;
								}
								$usL[] = strip_tags(trim($us[$i]));
								$usJ[] = do_seo(trim($us[$i]));
							}

							if ($error == 0) {
								$usC = count($usJ);
								$usQ = join("','",$usJ);
								$qC = mysql_query("SELECT login_rew FROM 3_users WHERE login_rew IN ('$usQ') AND reg_code = 0 AND id > 1");
								$qCc = mysql_num_rows($qC);
								if ($qCc>0) {
									$notFoundNick = array();
								  if ($qCc != $usC) {
									  $adresati = array();
									  while($adresat = mysql_fetch_row($qC)) {
									    $adresati[$adresat[0]] = $adresat[0];
										}
									  for($a=0;$a<count($usL);$a++) {
									    if ( !in_array($usJ[$a], $adresati) ) {
											  $error = 403;
									      $notFoundNick[] = $usL[$a];
											}
										}
									}
									if ($error == 0 && $usC > 25) {
										$error = 406;
									}
								}
								else {
								  $error = 404;
								  if ($usC > 1)	$error = 405;
								}
							}
						}
						else $error = 401;
						$tt = "";
					break;
					case "clanky":
						if (isset($_POST['adv']) && $_POST['adv'] != "") {
						  $adv = explode(",",$_POST['adv']);
						  foreach ($adv as $v) {
						    $v = explode(":",$v);
						    switch ($v[0]) {
									case "anotace":
									  if ($v[1] < 10) $error = 101;
									break;
									case "sekce":
									  if ($v[1] != 1) $error = 102;
									break;
									case "mess":
									  if ($v[1] < 20) $error = 103;
									break;
								}
							}
						}
						else $error = 800;
						$tt = "článcích již příspěvek";
					break;
					case "herna":
						$tt = "herně již jeskyně";
						$sekce = "3_herna_all";
					break;
					case "galerie":
						if (isset($_POST['adv']) && $_POST['adv'] != "") {
							$adv = explode(",",$_POST['adv']);
							foreach ($adv as $v) {
								$v = explode(":",$v);
								switch ($v[0]) {
									case "popis":
										if ($v[1] < 5) $error = 301;
									break;
									case "nazev":
										if ($v[1] < 4)$error = 302;
									break;
								}
							}
						}
						else $error = 800;
						$tt = "galerii již obrázek";
					break;
					case "diskuze":
						if (isset($_POST['adv']) && $_POST['adv'] != "") {
							$adv = explode(",",$_POST['adv']);
							foreach ($adv as $v) {
								$v = explode(":",$v);
								switch ($v[0]) {
									case "popis":
										if ($v[1] < 5) $error = 301;
									break;
									case "nazev":
										if ($v[1] < 4) $error = 302;
									break;
									case "oblast":
										if ($v[1] < 1) $error = 303;
									break;
								}
							}
						}
						else $error = 800;
						$tt = "diskuzích již téma";
						$sekce = "3_diskuze_topics";
					break;
					default:
						$error = 700;
					break;
				}
				if ($error > 0) {
					switch ($error) {
						case 101:
							echo "<ax><info t='error' f='anotace'><![CDATA["."Minimální délka anotace je 10 znaků."."]]></info></ax>";
						break;
						case 102:
							echo "<ax><info t='error' f='sekce'><![CDATA["."Musíte vybrat sekci, kam článek patří."."]]></info></ax>";
						break;
						case 103:
							echo "<ax><info t='error' f='mess'><![CDATA["."Délka textu článku je minimálně 20 znaků."."]]></info></ax>";
						break;
						case 201:
							echo "<ax><info t='error' f='cave_nazev'><![CDATA["."Minimální délka názvu jsou 4 znaky."."]]></info></ax>";
						break;
						case 301:
							echo "<ax><info t='error' f='popis'><![CDATA["."Minimální délka popisu je 5 znaků."."]]></info></ax>";
						break;
						case 302:
							echo "<ax><info t='error' f='nazev'><![CDATA["."Minimální délka názvu jsou 4 znaky."."]]></info></ax>";
						break;
						case 303:
							echo "<ax><info t='error' f='oblast'><![CDATA["."Musíte vybrat diskuzní oblast."."]]></info></ax>";
						break;
						case 400:
							echo "<ax><info t='error' f='us'><![CDATA["."Pole příjemce zprávy nesmí obsahovat váš nick."."]]></info></ax>";
						break;
						case 401:
							echo "<ax><info t='error' f='us'><![CDATA["."Jméno příjemce zprávy musí být minimálně 3 znaky."."]]></info></ax>";
						break;
						case 402:
							echo "<ax><info t='error' f='mess'><![CDATA["."Zpráva musí obsahovat nějaký text."."]]></info></ax>";
						break;
						case 403:
							if (count($notFoundNick)>1) {
								echo "<ax><info t='error' f='us'><![CDATA["."Uživatelé s nicky ".join(", ",$notFoundNick)." nenalezeni."."]]></info></ax>";
							}
							else echo "<ax><info t='error' f='us'><![CDATA["."Uživatel s nickem ".join(", ",$notFoundNick)." nenalezen."."]]></info></ax>";
						break;
						case 404:
							echo "<ax><info t='error' f='us'><![CDATA["."Příjemce zprávy nebyl nalezen."."]]></info></ax>";
						break;
						case 405:
							echo "<ax><info t='error' f='us'><![CDATA["."Ani jeden příjemce zprávy nebyl nalezen."."]]></info></ax>";
						break;
						case 406:
							echo "<ax><info t='error' f='us'><![CDATA["."Počet příjemců zprávy nesmí překročit 25."."]]></info></ax>";
						break;
						case 700:
							echo "<ax><info t='error'><![CDATA["."Chyba v hlavní stránce skriptu."."]]></info></ax>";
						break;
						case 800:
							echo "<ax><info t='error'><![CDATA["."Nespecifikovaná sekce, název či jiná potřebná informace."."]]></info></ax>";
						break;
					}
				}else {
					if ($_GET['sekce'] == "posta") {
						echo "<ax><info t='ok'><![CDATA["."ok"."]]></info></ax>";
					}
					else {
						$nazev = do_seo($_POST['co']);
						$sq = mysql_fetch_row(mysql_query("SELECT count(*) FROM $sekce WHERE nazev_rew='$nazev'"));
						if ($sq[0]>0) {
							$nzv = "nazev";
							if ($sekce == "3_herna_all") {
								$nzv = "cave_nazev";
							}
							echo "<ax><info t='error' f='$nzv'><![CDATA["."V $tt s podobným názvem existuje. Zkuste název nějak pozměnit."."]]></info></ax>";
						}
						else {
							echo "<ax><info t='ok'><![CDATA["."ok"."]]></info></ax>";
						}
					}
				}
			}
			else {
				echo "<ax><info t='error'><![CDATA["."Již jste déle než 30 minut neaktivní. Otevřete si novou stránku s adresou Aragorn.cz a přihlaste se. Pak můžete odeslat požadavek znovu."."]]></info></ax>";
			}
		}
		else {
			@header("Content-Type: text/xml; charset=utf-8");
			echo "<"."?xml version=\"1.0\" encoding=\"utf-8\"?".">";
			if (isset($_POST['co']) && mb_strlen($_POST['co']) < 3) {
				echo "<ax><info t='error' f='nazev'><![CDATA["."Minimální délka názvu jsou 3 znaky."."]]></info></ax>";
			}
			else {
				echo "<ax><info t='error'><![CDATA["."Chyba v hlavní stránce skriptu."."]]></info></ax>";
			}
		}
	break;
	case "logining":
		if (isset($_SESSION['login']) && isset($_SESSION['lvl']) && $_SESSION['login'] != "" && $_SESSION['lvl']>1) {

			//vlozeni cache_hitu
			include_once "./add/memcache.php";

			include_once "./db/conn.php";
			$jeOnline = mysql_query("SELECT timestamp FROM 3_users WHERE id = $_SESSION[uid] AND online = 1 AND timestamp > ($time - 1800) AND reg_code = 0");
			if (mysql_num_rows($jeOnline)>0) {
				$u = mysql_fetch_row($jeOnline);
				$LogedIn = true;
				if (isset($_SESSION['updated'])){
					if ($_SESSION['updated'] + 1800 < $time ){
						session_regenerate_id();
						$_SESSION["updated"] = $time;
					}
				}
				else {
					session_regenerate_id();
					$_SESSION["updated"] = $time;
				}
				$t = $u[0];
			}
		}
		@header("Content-Type: text/xml");
		echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>';
		echo "<ajax>";
		if ($LogedIn && !$zal) {
			echo "<xtx>$t</xtx>";
			echo "<xtx>$time</xtx>";
		}
		elseif ($LogedIn && $zal) {
			echo "<xtx>$t</xtx>";
			echo "<xtx>$time</xtx>";

			if (isset($AragornCache)) {
				$postActual = $AragornCache->getVal("post-unread:$_SESSION[uid]");
				if (!is_int($postActual)) {
					$postActual = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_post_new WHERE tid = '$_SESSION[uid]' AND stavto = '0'"));
					$AragornCache->replaceVal("post-unread:$_SESSION[uid]", intval($postActual[0]), 900);
					$postActual = $postActual[0];
				}
			}
			echo "<np>".$postActual."</np>";

/* start ZALOZEK */

include "./add/funkce.php";
include "./add/bookmarks.php";

/*
chat = 5
zalozky = 4
users + friends = 2 
*/

$aF = "";
$aCh = "";

/* konec ZALOZEK */

echo "<heads>";
	$usCount = mysql_fetch_row( mysql_query("SELECT count(*) FROM 3_users WHERE online = 1 AND timestamp > 0") );
	$chCount = mysql_fetch_row (mysql_query ("SELECT count(*) FROM 3_chat_users WHERE odesel=0"));
	$oF = mysql_query ("SELECT 3_users.login,3_users.login_rew,3_users.timestamp FROM 3_friends, 3_users WHERE 3_friends.uid = $_SESSION[uid] AND 3_users.id = 3_friends.fid AND 3_users.online = 1 ORDER BY 3_users.login");
	while($ooF = mysql_fetch_object($oF)){
		$friend = _htmlspec(stripslashes($ooF->login));
		$ooF->timestamp = $time - $ooF->timestamp;
		if ($ooF->timestamp < 120) {
			$profil = " (&lt; 2 minuty)";
		}
		elseif ($ooF->timestamp < 300) {
			$profil = " (&lt; 5 minut)";
		}
		elseif ($ooF->timestamp < 600) {
			$profil = " (&lt; 10 minut)";
		}
		else {
			$profil = " (".floor($ooF->timestamp/60)."+ minut)";
		}
		$aF .= "<a href='/uzivatele/$ooF->login_rew/' title='".$friend.$profil."'>$friend</a>";
	}
	if (!$aF){
		$aF = "<a href='/' title='Nikdo z přátel online'>Nikdo z přátel online</a>";
	}
	$sCh = mysql_query ("SELECT id, nazev, popis FROM 3_chat_rooms WHERE locked = 0 ORDER BY type ASC, nazev ASC");
	while($oCh = mysql_fetch_object($sCh)){
		$cCh = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_users WHERE rid = $oCh->id AND odesel = 0"));
		$aCh .= "<a href='/chat/?akce=chat-enter&amp;id=$oCh->id' title='Vstoupit :: ".htmlspecialchars(stripslashes($oCh->popis),ENT_QUOTES,"UTF-8")."'>".stripslashes($oCh->nazev)." ($cCh[0])</a>";
	}

	echo '<mn id="dropmenu2head"><![CDATA[Uživatelé ('.$usCount[0].'&nbsp;online)]]></mn>';
	echo '<mn id="dropmenu4head"><![CDATA[Záložky'.$addZalCount.']]></mn>';
	echo '<mn id="dropmenu5head"><![CDATA[Chat ('.$chCount[0].')]]></mn>';

	echo '<mn id="dropmenu2"><![CDATA[';
		echo $aF;
	echo "]]></mn>";

	echo '<mn id="dropmenu4"><![CDATA[';
		echo $zF;
	echo "]]></mn>";

	echo '<mn id="dropmenu5"><![CDATA[';
		echo $aCh;
	echo "]]></mn>";

	echo "</heads>";

/* konec vpisu změn do JSka */

		}
		else {
			echo "<xtx>off</xtx>";
		}
		echo "</ajax>";
			
	break;
	
//vypis statistik pro galerku, diskuzi a clanky
	case "stats":
		$noOutputBuffer = true;
		include "./db/conn.php";
		switch($_GET['sec']){
			case '1':
			case '2':
				$rub = ($_GET['sec'] > 1) ? '3_clanky': '3_galerie';
/*			$sAuthors = mysql_query("SELECT DISTINCT(autor) FROM $rub");
				while ($oAuthors = mysql_fetch_row($sAuthors)){
					$authors[] = $oAuthors[0];
				}
				$isIn = join($authors, ",");
*/        
				$sStats = mysql_query("SELECT u.login_rew AS l_r, u.login AS name, (SELECT AVG(hodnoceni/hodnotilo) FROM $rub WHERE autor = u.id AND schvaleno = '1') AS prumer, (SELECT count(*) FROM $rub WHERE autor = u.id AND schvaleno = '1') AS pocet, (SELECT SUM(((gs.hodnoceni/gs.hodnotilo)*(gs.hodnotilo))) FROM $rub AS gs WHERE autor = u.id AND schvaleno = '1') AS koeficient FROM 3_users AS u WHERE u.id IN (SELECT DISTINCT(autor) FROM $rub) ORDER BY koeficient DESC, u.login_rew LIMIT 10");

				@header("Content-Type: text/xml");
				echo '<'.'?xml version="1.0" encoding="utf-8"?'.'>';   
				echo "<ajax>";

				while($oStats = mysql_fetch_object($sStats)){
					echo "<stats>";
					echo "<autor><![CDATA[<a href='/uzivatele/".$oStats->l_r."/' class='unr' title='Profil autora'>".$oStats->name."</a>]]></autor>";
					echo "<pocet><![CDATA[".$oStats->pocet."]]></pocet>";
					echo "<prumer><![CDATA[".number_format($oStats->prumer, 2, '.', ' ')."]]></prumer>";
					echo "<koef><![CDATA[".number_format($oStats->koeficient, 1, '.', ' ')."]]></koef>";
					echo "</stats>";
				}

				if ($_GET['loginStats'] > 0){
					$iStats = mysql_fetch_row(mysql_query("SELECT SUM(((hodnoceni/hodnotilo)*(hodnotilo))) AS koe FROM $rub WHERE autor = '$_GET[loginStats]' AND schvaleno > 0"));
					$ko = ($iStats[0] > 0)? number_format($iStats[0], 1, '.', ' ') : '0.0';
					$msg = "Váš koeficient je <strong>".$ko."</strong>.";
				}else{
					$msg = "Pro zjištění svého koeficientu je třeba se přihlásit.";
				}
				echo "<msg><![CDATA[<p class='text'>".$msg."</p>]]></msg>";
				echo "</ajax>";
			break;
		}

	break;
	
	case "poznamky-pj":
	case "poznamky-pj-save":
 		if (isset($_SESSION['login']) && isset($_SESSION['lvl']) && $_SESSION['login'] != "" && isset($_GET['nazev'])) {
			$noOutputBuffer = true;
			include "./db/conn.php";
			include "./add/funkce.php";
			$cave = addslashes($_GET['nazev']);
			$save = false;

			if ($_GET['do'] == "poznamky-pj-save"){
				if (isset($_POST['msg'])) {
					$save = true;
				}
				$sql = "h.id";
			}
			else {
				$sql = "h.poznamky";
			}
			$txtS = mysql_query("SELECT $sql FROM 3_herna_all AS h LEFT JOIN 3_herna_pj AS p ON p.cid = h.id AND p.poznamky = '1' AND p.schvaleno = '1' WHERE h.nazev_rew = '$cave' AND h.schvaleno = '1' AND (h.uid = $_SESSION[uid] OR p.uid = $_SESSION[uid])");

			if ($txtS && mysql_num_rows($txtS)) {
				$txt = mysql_fetch_row($txtS);

				if ($save){
					mysql_query("UPDATE 3_herna_all SET poznamky = '".addslashes($_POST['msg'])."' WHERE nazev_rew = '$cave' AND schvaleno = '1'");
					@header("Content-Type: text/html; charset=utf-8");
					echo "<div>".nl2br($_POST['msg'])."</div>";
				}
				else {
					echo "<a href='#' onclick='hide(\"poznamky_pj_here\");hide(\"poznamky_pj_edit\");return false;'>Zobrazit / Skrýt Poznámky PJ</a>";
					@header("Content-Type: text/html; charset=utf-8");
					if (strlen($txt[0])<1){
						echo "<div id='poznamky_pj_here'>Poznámky jsou prázdné.</div>";
					}
					else {
						echo "<div id='poznamky_pj_here' style='white-space:pre-line;'>".(stripslashes($txt[0]))."</div>";
					}
					echo "<div id='poznamky_pj_edit'><a href='/herna/$cave/pj/#obecne' onclick='var t=$(this);var tx=$(\"poznamky_pj_here\");if(tx){tx=tx.get(\"html\");}else{tx=\"\";}
					t.addClass(\"hide\").getNext().removeClass(\"hide\").adopt(
						new Element(\"form\",{\"class\":\"f\"}).adopt(
							new Element(\"fieldset\").adopt(
								new Element(\"textarea\",{cols:70,rows:15,id:\"poznamky_pj_new\",styles:{width:\"95%\"},\"class\":\"dblock\",name:\"msg\",value:tx==\"Poznámky jsou prázdné.\"?\"\":tx}),
								new Element(\"input\",{
									\"class\":\"button\",
									value:\"Uložit\",type:\"submit\",
									events:{
										click:function(){
											this.getPrevious().focus();
											new Request.HTML({
												update:\"poznamky_pj_here\",method:\"post\",url:\"/ajaxing.php?do=poznamky-pj-save&amp;nazev=$cave\"
											}).post({
												msg:$(\"poznamky_pj_new\").get(\"value\")
											});
											return false;
										}
									}
								})
							)
						)
					);
					$(\"poznamky_pj_new\").focus();
					return false;'>Upravit</a><div class='hide'></div></div>";
				}
			}
			else {
				echo "Nepodařilo se načíst Poznámky PJ.";
			}
		}
	break;
  default:
		die('');
		exit;
	break;
}

?>