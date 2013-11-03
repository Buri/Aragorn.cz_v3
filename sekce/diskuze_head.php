<?php
$title = "Diskuzní oblasti";

$dFound = $oItem = false;
$error = $ok = 0;
unset($error,$ok);
$okruh = $slink = $nazev = $popis = "";

//vypise warning - siroky
function infow($text){
  echo "<p class='info infow' id='inf'><span class='war' title='Varování'></span>$text <a href='javascript: hide(\"inf\")' class='permalink2' title='Zavřít'>Zavřít</a></p>";
}
//vypise ok - siroke
function okw($text){
  echo "<p class='info infow' id='inf'><span class='inf' title='Ok'></span>$text <a href='javascript: hide(\"inf\")' class='permalink2' title='Zavřít'>Zavřít</a></p>";
}
// zjisti prava pro dotycneho uzivatele na zaklade dat z tabulek prav a temat, Sessions nebo LogedIn...
function GetPravaHere($id_diss, $id_owner, $p_reg, $p_guest, $prihlasen){
		if ($prihlasen == true){
			$vysl = "write";
			if ($id_owner == $_SESSION['uid']) {
				$vysl = "superall";
			}
			else {
				if ($p_reg == "read") {
					$vysl = "read";
				}
				elseif ($p_reg == "write") {
					$vysl = "write";
				}
				elseif ($p_reg == "hide") {
					$vysl = "nothing";
				}
				$dPravaSrcWRHX = mysql_query("SELECT d.prava FROM 3_diskuze_prava AS d, 3_diskuze_topics AS t WHERE (d.id_dis = '$id_diss' OR (d.id_dis = '0' AND d.prava = 'admin')) AND d.id_user = $_SESSION[uid] AND t.id = '$id_diss'");
				if (mysql_num_rows($dPravaSrcWRHX)>0) {
					$dPravaWRHX = mysql_fetch_row($dPravaSrcWRHX);
					mysql_free_result($dPravaSrcWRHX);
					switch ($dPravaWRHX[0]) {
						case "admin":
							$vysl = "superall";
							break;
						case "moderator":
							$vysl = "all";
							break;
						case "writer":
							$vysl = "write";
							break;
						case "hide":
							$vysl = "nothing";
							break;
						case "reader":
							$vysl = "read";
							break;
						default:
							$vysl = "read";
						break;
					}
				}
			}
		}
		else {
			$vysl = "read";
			if ($p_guest == "hide") {
				$vysl = "nothing";
			}
		}
	return $vysl;
}

if (isSet($_GET['error'])) {
$error = $_GET['error'];
}
elseif (isSet($_GET['ok'])) {
$ok = $_GET['ok'];
}

if (isSet($_GET['slink'])) {
	$slink = $_GET['slink'];
	/* Anti "Mauz" Protection */
	if(($slink === "administratori" || $slink === "rozcesti-sprava")&& $_SESSION['login'] == "Mikymauz"){
		die('<h2>403 Forbidden</h2>');
	}
	if (strlen($slink) < 2) {
		$slink = "";
	}
	if ($slink == "new") {
		$title = "Diskuze - nové téma";
	}
	elseif ($slink == "my") {
		$title = "Diskuze - moje témata";
	}
	elseif($slink == "ad") {
		$title = "Diskuze - Administrace TOP-LEVEL";
  }
	elseif (strlen($slink)>2) {

		$sel_diskuze = mysql_query ("SELECT d.id, d.nazev, d.popis, d.owner, d.prava_reg, d.prava_guest, d.closed, u.login from 3_diskuze_topics AS d LEFT JOIN 3_users AS u ON u.id = d.owner WHERE d.schvaleno = '1' AND d.nazev_rew = '".addslashes($slink)."'");
		$dC = mysql_num_rows($sel_diskuze);

		if ($dC > 0){
			$title = "";
			$dFound = true;
			$dItem = mysql_fetch_object($sel_diskuze);
			$id = $dItem->id;
			$sid = 3;
			$GLOBAL_description = "diskuzní téma: "._htmlspec($dItem->nazev).", vlastník: $dItem->login, popis: "._htmlspec($dItem->popis);
			if ($sslink != "") {
				if ($sslink == "ankety") {
					$title = "Ankety | ";
					$GLOBAL_description = "Ankety - ".$GLOBAL_description;
				}
				elseif ($sslink == "stats") {
					$GLOBAL_description = "Statistiky přístupů - ".$GLOBAL_description;
					$statsOrder = "u.login ASC";
					$statsTime = "<a href='/diskuze/$slink/$sslink/?podle=cas'>Čas posl.návštěvy</a>";
					$title = "Statistiky | ";
					if (isset($_GET['podle'])) {
						switch($_GET['podle']) {
							case "cas":
								$statsOrder = "v.time DESC";
								$title = "Statistiky (řazeno podle času posl.návštěvy) | ";
								$statsTime = "Čas posl.návštěvy";
							break;
						}
					}
				}
			}
			$title .= _htmlspec(stripslashes($dItem->nazev));
		}
		else {
			$title = "Diskuze "._htmlspec($slink)." nenalezena";
			if ($sslink == "ankety") {
				$title = "Ankety | ".$title;
			}
			elseif ($sslink == "stats") {
				$title = "Statistiky | ".$title;
			}
		}
		mysql_free_result($sel_diskuze);
	}
	else {
		$slink = "";
  }
}
elseif (isSet($_GET['oblast'])) {
	$okruh = addslashes(strip_tags($_GET['oblast']));
	$slink = "";
	$sel_oblast = mysql_query ("SELECT id, nazev, nazev_rew, popis FROM 3_diskuze_groups WHERE id = '$okruh'");
	$oC = mysql_num_rows($sel_oblast);
	if ($oC > 0){
		$oItem = mysql_fetch_object($sel_oblast);
		$title = _htmlspec($oItem->nazev)." | Diskuzní oblast";
		$GLOBAL_description = "Diskuzní oblast: "._htmlspec($oItem->nazev)." -- "._htmlspec($oItem->popis);
		$okruh = $oItem->id;
	}
	else {
		$okruh = "";
	}
	mysql_free_result($sel_oblast);
}
else {
		$slink = $okruh = "";
}

if (!isSet($_GET['index'])) $pg_index = 1;
else $pg_index = (int)($_GET['index']);
if ($pg_index < 1) $pg_index = 1;
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
elseif(isset($_GET['index'])) {
	$title .= " ($time)";
}

if ($okruh == "") $title .= " | Diskuze";

if (!$GLOBAL_description) $GLOBAL_description = $title;

?>