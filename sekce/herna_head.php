<?php
$title = "Výpis jeskyní";

if (!isset($uzivatele)) {
	$uzivatele = array();
}

$allowsPJ = array('nastenka' => 0, 'poznamky' => 0, 'mapy' => 0, 'postavy' => 0, 'obchod' => 0, 'prispevky' => 0);

$herna_nebonus = 4;
$vypisMsg = $subSection = $jInc = $hInc = "";
$hFound = $pFound = $uzMamPostavu = false;
$error = $ok = 0;
$hCh = "┼";
$okruh = $slink = $nazev = $popis = "";
$uzivateleVeHre = array();

$datum = array();
$datum["den"] = date("j",$time);
$datum["mesic"] = date("n",$time);
$datum["rok"] = date("Y",$time);

$titlePodle = "";
$H3Add = true;
if (isset($_GET['podle'])) {
	switch ($_GET["podle"]) {
		case "aktivity":
			$titlePodle = "aktivity";
		break;
		case "zalozeni":
			$titlePodle = "založení";
		break;
		case "vlastnika":
			$titlePodle = "vlastníka";
		break;
		case "nazvu":
			$titlePodle = "názvu";
		break;
		default:
			$titlePodle = "založení";
			$H3Add = false;
		break;
	}
	if ($titlePodle != "") $titlePodle = ": podle ".$titlePodle; 
}

$usersOnlineS = mysql_query("SELECT login_rew,id FROM 3_users WHERE online = '1'");
$usersOnline = array();
$usersOnlineArray = array();

if (mysql_num_rows($usersOnlineS)>0) {
	while ($usersOnlineR = mysql_fetch_row($usersOnlineS)) {
		$usersOnline[] = $usersOnlineR[0];
		$usersOnlineArray[$usersOnlineR[1]] = 1;
	}
}

function name_is_bad($what){
	$forbidden = array("reg","chat","pj","shop","orp","mapy","ch");
	return in_array($what, $forbidden);
}

//posle postu(info) uzivateli
function herna_posta($text,$komu) {
	global $time, $AragornCache;
	$hash = addslashes(md5($text));
	$sql = "";
	$messId = 0;
	mysql_query("LOCK TABLES 3_post_text WRITE, 3_post_new WRITE");
	$jeHashS = mysql_query("SELECT id,content,compressed FROM 3_post_text WHERE hash = '$hash' ORDER BY id ASC");
	if ($jeHashS && mysql_num_rows($jeHashS)>0){
		while($jeHash = mysql_fetch_row($jeHashS)) {
			if ($jeHash[2]>0) $jeHash[1] = gzuncompress($jeHash[1]); 
			if ($jeHash[1] == $text) {
				$messId = $jeHash[0];
				break;
			}
		}
	}
	if ($messId == 0) {
		$text = addslashes($text);
		mysql_query("INSERT INTO 3_post_text (compressed, content, hash) VALUES ('0','$text','$hash')");
		$messId = mysql_insert_id();
	}
	if (is_array($komu)) {
		$localSQL = "";
		for ($i=0,$cntr=count($komu);$i<$cntr;$i++) {
			$localSQL .= "INSERT INTO 3_post_new (mid, tid, fid, stavfrom, stavto, cas) VALUES ('$messId', '$komu[$i]', '0', '3', '0', '$time');\n";
			$AragornCache->incVal("post-unread:$komu[$i]", 1, 3600);
		}
		mysql_query($localSQL);
	}
	else {
		mysql_query("INSERT INTO 3_post_new (mid, tid, fid, stavfrom, stavto, cas) VALUES ('$messId', '$komu', '0', '3', '0', '$time');");
		$AragornCache->incVal("post-unread:$komu", 1, 3600);
	}
	mysql_query("UNLOCK TABLES");
}

//ukaze jednoduchou cenu, nebo finance
function item_cena($prize) {
	$zl = floor($prize);
	$st = floor(($prize-$zl)*10);
	$md = round(($prize-$zl-0.1*$st)*100);
	return "$zl zl $st st $md md";
}

function arrayCena($cena) {
	$zl = floor($cena);
	$st = floor(($cena-$zl)*10);
	$md = floor(($cena-$zl-$st*0.1)*100);
	return array($zl,$st,$md);
}

function item_nabidka($povol,$idi) {
	if ($povol) {
		$txt = "<a href='?akce=obchod&amp;c=z&amp;v=$idi'>Zakázat</a>|<a href='?do=c&amp;v=$idi'>Změna&nbsp;ceny</a>";
	}
	else {
		$txt = "<a href='?akce=obchod&amp;c=n&amp;v=$idi'>Povolit</a>";
	}
	return $txt;
}

//vypise warning - siroky
function infow($text){
	global $LogedIn;
	echo "\n<p class='info infow' id='infw'><span class='war' title='Varování'></span>$text";
	if ($LogedIn) {
		echo " <a href='javascript: hide(\"infw\")' class='permalink2' title='Zavřít'>Zavřít</a>";
	}
	echo "</p>\n";
}
//vypise ok - siroke
function okw($text){
	global $LogedIn;
	echo "\n<p class='info infow' id='infw'><span class='inf' title='Ok'></span>$text";
	if ($LogedIn) {
		echo " <a href='javascript: hide(\"infw\")' class='permalink2' title='Zavřít'>Zavřít</a>";
	}
	echo "</p>\n";
}

function preloz_ids($ar_id_login,$what) {
	$t = substr($what,1,-1);
	$t = explode ("#",$t);
	$prelozeno = array();
	for ($i=0;$i<count($t);$i++) {
		$prelozeno[] = $ar_id_login[$t[$i]];
	}
	$prelozeno = join (", ",$prelozeno);
	return $prelozeno;
}

function herna_omezeni($uid,$lvl) {
	$cMyAll = 10;
	$ar = array(1, 2);
	if ($uid == 0) {
		return $ar;
	}
	$ar = join(",", $ar);
	if ($lvl > 1) {
		$cMyAll = 0;
	}
	else {
		$q = "SELECT SUM(x.col1), x.uid, group_concat(x.col1) FROM (
				SELECT COUNT(cid) as col1, uid FROM 3_herna_pj WHERE uid = '$uid' AND cid NOT IN ($ar) GROUP BY uid
				UNION ALL
				SELECT COUNT(id) as col1, uid FROM 3_herna_all WHERE uid = '$uid' AND id NOT IN ($ar) GROUP BY uid
				UNION ALL
				SELECT COUNT(id) as col1, uid FROM 3_herna_postava_orp WHERE uid = '$uid' AND cid NOT IN ($ar) GROUP BY uid
				UNION ALL
				SELECT COUNT(id) as col1, uid FROM 3_herna_postava_drd WHERE uid = '$uid' AND cid NOT IN ($ar) GROUP BY uid
			) x GROUP BY x.uid";
		$cMyS = mysql_query($q);
		$cMy = mysql_fetch_row($cMyS);
		$cMyAll = $cMy[0];
	}
	return $cMyAll;
}

if (isSet($_GET['error'])) {
	$error = $_GET['error'];
}
elseif (isSet($_GET['ok'])) {
	$ok = $_GET['ok'];
}

if (isset($_GET['slink'])) {
	$slink = $_GET['slink'];
	if (strlen($slink) < 2) {
		header("Location: $inc/herna/");
		exit;
	}
	if ($slink == "new") {
		$title = "Založení nové jeskyně";
	}
	elseif($slink == "my") {
		$title = "Mé jeskyně a postavy";
	}
	elseif (strlen($slink) > 2) {
		$slink = $_GET['slink'];
		if (isset($_SESSION['uid']) && isset($_SESSION['login'])) {
			$title = "";
			$sel_herna = mysql_query ("SELECT j.*, u.login AS vlastnik, u.login_rew AS vlastnik_rew FROM 3_herna_all AS j, 3_users AS u WHERE (j.uid = $_SESSION[uid] OR j.schvaleno = '1') AND j.nazev_rew = '$slink' AND u.id = j.uid");
		}
		else {
			$sel_herna = mysql_query ("SELECT j.*, u.login AS vlastnik, u.login_rew AS vlastnik_rew FROM 3_herna_all AS j, 3_users AS u WHERE j.schvaleno = '1' AND j.nazev_rew = '$slink' AND u.id = j.uid");
		}
		$hC = mysql_num_rows($sel_herna);

		if ($hC > 0){
			$hFound = true;
			$hItem = mysql_fetch_object($sel_herna);

			$hItem->activities = array();
			$hItem->PJs = false;
			$hItem->allPJs = false;

			/* pomocny PJs START */
			$helpPJs = mysql_query("SELECT p.*, u.login, u.login_rew FROM 3_herna_pj AS p LEFT JOIN 3_users AS u ON u.id = p.uid WHERE p.cid = '$hItem->id'");
			if ($helpPJs && mysql_num_rows($helpPJs) > 0) {
				$hItem->PJs = array();
				$hItem->allPJs = array();
				while($hPJ = mysql_fetch_object($helpPJs)) {
					$uzivateleVeHre[$hPJ->uid] = 1;
					$hItem->allPJs[$hPJ->uid] = $hPJ;
					if ($hPJ->schvaleno) {
						$hItem->PJs[$hPJ->uid] = $hPJ;
						$uzivatele[$hPJ->uid] = array('login' => $hPJ->login, 'login_rew' => $hPJ->login_rew, 'ico' => $hPJ->ico, 'aktivita' => $hPJ->aktivita);
						$uzivatele[$hPJ->uid]['stav'] = isset($usersOnlineArray[$hPJ->uid]);
					}
				}
			}

			if (isset($_SESSION['uid']) && $hItem->uid == $_SESSION['uid']) {
				$uzivateleVeHre[$hItem->uid] = 1;
				function truify() {
					return 1;
				}
				$allowsPJ = array_map('truify', $allowsPJ);
				header('X-MESSAGE: Welcome home, Master!');
				//$allowsPJ = array('nastenka' => 1, 'poznamky' => 1, 'mapy' => 1, 'postavy' => 1, 'obchod' => 1, 'prispevky' => 1);
			}
			elseif (isset($_SESSION['uid']) && isset($hItem->PJs[$_SESSION['uid']])) {
				header('X-MESSAGE: Stay alert, young padawan.');
				$a = array_keys($allowsPJ);
				$allowsPJ = array();
				foreach ($a as $k) {
					$allowsPJ[$k] = $hItem->PJs[$_SESSION['uid']]->$k;
				}
			}

			/* pomocny PJs END */

			$id = $hItem->id;
			$sid = 4;
			$pAktivPlayers = 0;

			$hItem->nazev = _htmlspec(odhtml(stripslashes($hItem->nazev)));
			$hItem->popis = _htmlspec(odhtml(stripslashes($hItem->popis)));
			$hItem->ico = ((strlen($hItem->ico) > 3) ? $hItem->ico : "default.jpg");
			$hItem->hraci_hleda = _htmlspec(odhtml(stripslashes($hItem->hraci_hleda)));

			$title = $hItem->nazev;
			$GLOBAL_description = $title." ~ ".$GLOBAL_description;

			if ($hItem->typ == "0") {
				$jTypString = "drd";
			} else {
				$jTypString = "orp";
			}

			$pAktivPlayersS = mysql_query ("SELECT h.*, u.login, u.login_rew FROM 3_herna_postava_$jTypString AS h, 3_users AS u WHERE h.cid = $hItem->id AND h.uid = u.id ORDER BY u.login ASC");
			$pPlayers = mysql_num_rows($pAktivPlayersS);
			$pp = array();
			$jeskyneHraci = array();
			$uzMamPostavu = 0;
			$uzivatele[$hItem->uid] = array();
			$uzivatele[$hItem->uid]['ico'] = $hItem->ico;
			if ($pPlayers > 0) {
				$counte = 0;
				while ($player = mysql_fetch_object($pAktivPlayersS)) {
					$uzivateleVeHre[$player->uid] = 1;
					if (isset($_SESSION["uid"]) && $player->uid == $_SESSION['uid']) {
						$uzMamPostavu = 1;
						$schvalenaPostava = $player->schvaleno;
						$mojePostavaObjekt = $player;
					}
					if ($player->schvaleno == '1') {
						$pAktivPlayers++;
						$hrac = "";
						$hrac = array();
						$hrac['jmeno'] = $player->login;
						$hrac['jmeno_rew'] = $player->login_rew;
						$hrac['postava'] = htmlspecialchars($player->jmeno,ENT_COMPAT,"UTF-8");
						$hrac['postava_rew'] = $player->jmeno_rew;
						$hrac['objekt'] = $player;
						if ($jTypString == "drd") {
							$zivoty_pomer = $player->zivoty/$player->zivoty_max;
							if ($zivoty_pomer == 1) {
								$zr = "Jako rybička";
							}
							elseif ($player->zivoty == 1) {
								$zr = "V bezvědomí";
							}
							elseif ($zivoty_pomer > 0.95) {
								$zr = "Několik málo šrámů";
							}
							elseif ($zivoty_pomer > 0.75) {
								$zr = "Lehká zranění";
							}
							elseif ($zivoty_pomer > 0.5) {
								$zr = "Střední zranění";
							}
							elseif ($zivoty_pomer > 0.25) {
								$zr = "Těžká zranění";
							}
							elseif ($player->zivoty == 0) {
								$zr = "Na nule! Mrtvola";
							}
							else {
								$zr = "Velmi těžká zranění";
							}
							if (strlen($player->ico)>3) {
								$hrac['popis'] = "<img src='http://s1.aragorn.cz/i/".$player->ico."' /><br />";
							}
							else {
								$hrac['popis'] = "";
							}
							$hrac['popis'] .= $hrac['postava']."<br /><em>Zdr.Stav: $zr</em>";
						}
						else {
							if (strlen($player->ico)>3) {
								$hrac['popis'] = "<img src='http://s1.aragorn.cz/i/".$player->ico."' /><br />".$hrac['postava'];
							}
							else {
								$hrac['popis'] = $hrac['postava'];
							}
						}
						$jeskyneHraci[] = $hrac;
						if (isset($usersOnlineArray[$player->uid])) {
							$sOnline = "<span class='hpositive'>Online</span>";
						}
						else {
							$sOnline = "<span class='hnegative'>Offline</span>";
						}
						$pp[] = "\n\t\t\t<a class='permalink2' href='/herna/$slink/$player->jmeno_rew/' onmouseover=\"ddrivetip('".addslashes($sOnline)."<br /> Posl.aktivita ve hře: ".date("j.n.Y H:i", $player->aktivita)."<br />".addslashes($hrac['popis'])."')\" onmouseout='hidedrivetip();'>"._htmlspec($player->login)."</a>";
						$uzivatele[$player->uid] = array();
						$uzivatele[$player->uid]['postava'] = $hrac['postava'];
						$uzivatele[$player->uid]['postava_rew'] = $hrac['postava_rew'];
						if ($player->ico != "") {
							$uzivatele[$player->uid]['ico'] = $player->ico;
						}
					}
				}
				$pp = join(", ",$pp);
			}
			else {
				$pp = "";
			}

/* ------ nalezena jeskyne: povoli dalsi podlinky (mapy, chat, obchod, postavy 
	 ------ ale jen pro SCHVALENE jeskyne */

			if (isSet($_GET['sslink']) && $hItem->schvaleno == '1'){
				switch ($_GET['sslink']) {
				case "mapy":
					$title = "Mapy | ".$title;
					$subSection = " - Mapy";
					$GLOBAL_description = "Mapy pro hru ".$hItem->nazev." | Aragorn.cz Herna";
					$jInc = "herna_mapy.php";
				break;
				case "reg":
					$title = "Přihláška do jeskyně | ".$title;
					$hInc="herna_reg";
				break;
				case "ch":
				case "chat":
					$title = "Cave | ".$title;
					$subSection = " - Cave";
				break;
				case "shop":
					$title = "Obchod | ".$title;
					$subSection = " - Obchod";
					$jInc = "herna_shop.php";
				break;
//				case "hrac":
//					$title = "Volby hráče | ".$title;
//				break;
				case "pj":
					$title = "Volby Pána jeskyně | ".$title;
					$subSection = " - PJ";
					$jInc = "herna_pj.php";
				break;
				default:
					$sslink = addslashes($_GET['sslink']);
					$postavaS = mysql_query("SELECT h.*,u.login AS vlastnik, u.login_rew AS vlastnik_rew FROM 3_herna_postava_$jTypString AS h, 3_users AS u WHERE h.cid = '$id' AND h.jmeno_rew = '$sslink' AND u.id = h.uid");
					if (mysql_num_rows($postavaS)>0) {
						$pFound = true;
						$postava = mysql_fetch_object($postavaS);
						$postava_jmeno = _htmlspec(stripslashes($postava->jmeno));
						$title = "$postava_jmeno | ".$title;
						$jInc = "herna_postava.php";
						$GLOBAL_description = "Postava: $postava_jmeno, hra: ".$hItem->nazev." | Aragorn.cz Herna";
						$postavaP = mysql_query("SELECT poznamka FROM 3_herna_poznamky WHERE id_postava = '$postava->id'");
						if (mysql_num_rows($postavaP)>0) {
							$postava_poznamka = mysql_fetch_object($postavaP);
						}
					}
					else {
						header("Location: $inc/herna/$slink/");
						exit;
					}
				break;
				}
			}
			elseif (isSet($_GET['sslink']) && $hItem->schvaleno != '1') {
				if ($_GET['sslink'] == "pj") {
					$title = "Volby Pána jeskyně";
					$jInc = "herna_pj.php";
				}
			}
//			mysql_free_result($sel_herna);
		}
		else {
			$title = "Chyba: adresa "._htmlspec($slink.($sslink!=""?"/".$sslink:""))." nenalezena";
			$vypisMsg = "Nepodařilo se nalézt jeskyni podle hledané části adresy:<br /><em>"._htmlspec($slink.($sslink!=""?"/".$sslink:""))."</em>";
			$slink = "";
			$sslink = "";
		}
	}
	else {
		$slink = "";
	}
}
elseif (isSet($_GET['sekce'])) {
	switch ($_GET['sekce']){
		case "drd":
			$title = "Jeskyně systému DrD";
			$GLOBAL_description = "Výpis jeskyní na systému Dračí Doupě.";
		break;
		case "orp":
			$title = "Jeskyně systému ORP";
			$GLOBAL_description = "Výpis jeskyní na neurčitém pseudo-systému ORP.";
		break;
		case "vse":
			$title = "Výpis všech jeskyní";
		break;
		default:
			$title = "Výpis jeskyní"; 
		break;
	}
}
else {
		$slink = $okruh = "";
}

if ($slink == "" && isset($_GET['search'])){
	$title = "Hledání | ".$title;
}

if ($hFound) {
	$titlePodle = "";
	$H3Add = false;
}

$title = $title.$titlePodle;

$shortTitle = $title;

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
	$GLOBAL_description .= " ($pg_index stran$konc zpět)";
}
elseif (isset($_GET['index'])) {
	$title .= " ($time)";
}
elseif ($pg_index == 1) {
	$title .= " (1. strana)";
	$GLOBAL_description .= " (1. strana)";
}

$title .= " | Herna";
if (!$GLOBAL_description) $GLOBAL_description = $shortTitle." | Herna";

$sid = 4;
?>
