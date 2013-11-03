<?php
$title = $shortTitle = "Výpis obrázků";
$searchUser = false;
$autorSQL = "";

function get_prava_sekce($sid,$aid){
	global $LogedIn;
	if ($LogedIn) {
		if ($_SESSION['lvl'] > 2) {
			return 1;
		}
		else {
			$res = mysql_query("SELECT prava FROM 3_sekce_prava WHERE uid=$_SESSION[uid] AND sid=$sid AND aid=$aid");
			if ($res && mysql_num_rows($res)> 0) {
				$ret = mysql_fetch_row($res);
				return $res[0];
			}
			else return 1;
		}
	}
	else return 0;
}

function get_admin_prava(){
	if ($_SESSION['lvl']>3) return 1;
	$selS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = $_SESSION[uid] AND galerie = 1"));
	return $selS[0];
}

$thumb_prefix = "t_";
$error = $ok = 0;
$vL = "";
$cFound = false;

if (isSet($_GET['error'])) {
$error = $_GET['error'];
}
elseif (isSet($_GET['ok'])) {
$ok = $_GET['ok'];
}

if (isSet($_GET['slink'])) {
	$slink = $_GET['slink'];
	if (strlen($slink)==0) {
		$slink = "";
	}
	if ($slink == 'my') {
		$title = "Moje obrázky";
	}
	elseif ($slink != "new") {
		if ($slink == "od") {
			if ($sslink != ""){
				$searchUserS = mysql_query("SELECT id,login FROM 3_users WHERE login_rew = '".addslashes($sslink)."' LIMIT 1");
				if ($searchUserS && mysql_num_rows($searchUserS)>0) {
					$searchUser = mysql_fetch_row($searchUserS);
					$autorSQL = " AND g.autor = '$searchUser[0]' ";
					$shortTitle = $title = "autor ~ $searchUser[1]";
					$GLOBAL_description = "Výpis všech obrázků a děl v galerii na serveru Aragorn.cz od autora: ".$searchUser[1];
				}
				else {
					header("Location: $inc/galerie/");
					exit;
				}
			}
			else {
				header("Location: $inc/galerie/");
				exit;
			}
		}
		else {
			$slink = addslashes($slink);
			$sel_galerie = mysql_query ("SELECT g.id, g.nazev, g.popis, u.login FROM 3_galerie AS g LEFT JOIN 3_users AS u ON u.id = g.autor WHERE g.nazev_rew = '$slink' AND g.schvaleno = '1'");
			$gC = mysql_num_rows($sel_galerie);
	
			if ($gC > 0){
				$gItem = mysql_fetch_object($sel_galerie);
				$title = _htmlspec(stripslashes($gItem->nazev));
				$GLOBAL_description = "dílo: ".$title.", autor: ".$gItem->login.", popis: "._htmlspec(stripslashes($gItem->popis));
				$id = $gItem->id;
				$aid = $id;
				$sid = 2;
				$cFound = true;
				if ($sslink == "stats") {
					$title = "Statistiky | ".$title;
				}
			}
		}
	}
	elseif ($slink == "new") {
		$title = "Nahrát vlastní obrázek";
	}
}
else {
	$slink = "";
	$gC = 0;
}

$shortTitle = $title;

if (!$cFound && $slink != "od" && $slink != "new" && $slink != "" && $slink != "my") $title .= ": adresa $slink nenalezena";

if ($slink != 'my' && $slink != 'new' && ($slink == '' || ($slink != '' && $cFound))) {
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
		$title .= " - ".($cFound?"Komentáře - ":"")."($pg_index stran$konc zpět)";
		$GLOBAL_description .= " - ".($cFound?"Komentáře - ":"")."($pg_index stran$konc zpět)";
	}
	elseif (isset($_GET['index'])) {
		$title .= " ($time)";
	}
	elseif ($pg_index  == 1) {
		$title .= " (1. strana)";
		$GLOBAL_description .= " - Komentáře - (1. strana)";
	}
}

$title .= " | Galerie";
if (!$GLOBAL_description) $GLOBAL_description = $title;

?>