<?php
$cFound = false;
$sekce_max = 6;
$title = $shortTitle = "Výpis článků";
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
	$selS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = $_SESSION[uid] AND clanky = 1"));
	return $selS[0];
}

$sekceSEO = array(
0=>"povidky",1=>"poezie",2=>"uvahy",3=>"recenze",4=>"postavy",5=>"ostatni",6=>"vildovy-cesty",7=>"rozhovory",8=>"300-z-mista",9=>"predmety",
"povidky"=>0,"poezie"=>1,"uvahy"=>2,"recenze"=>3,"postavy"=>4,"ostatni"=>5,"vildovy-cesty"=>6,"rozhovory"=>7,"300-z-mista"=>8,"predmety"=>9);

function ss($s){
			switch ($s){
				case "0":
				case "povidky":
					$sekce = "Povídky";
				break;
				case "1":
				case "poezie":
					$sekce = "Poezie";
				break;
				case "2":
				case "uvahy":
					$sekce = "Úvahy";
				break;
				case "3":
				case "recenze":
					$sekce = "Recenze";
				break;
				case "4":
				case "postavy":
					$sekce = "Postavy";
				break;
				case "5":
				case "ostatni":
					$sekce = "Ostatní";
				break;
				case "6":
				case "vildovy-cesty":
					$sekce = "Vildovy cesty";
				break;
				case "7":
				case "rozhovory":
					$sekce = "Rozhovory";
				break;
				case "8":
				case "300-z-mista":
					$sekce = "300 z místa";
				break;
				case "9":
				case "predmety":
					$sekce = "Předměty";
				break;
				default:
					$sekce = "Nezařazeno";
				break;
			}
	return $sekce;
}

	if ($slink == "new"){
		$title = "Odeslat nový článek";
	}elseif ($slink == "my") {
		$title = "Moje Články";
	}elseif ($slink != "" && $slink != "new"){
		$slink = addslashes($slink);
		if ($slink == "od") {
			if ($sslink != ""){
				$searchUserS = mysql_query("SELECT id,login FROM 3_users WHERE login_rew = '".addslashes($sslink)."' LIMIT 1");
				if ($searchUserS && mysql_num_rows($searchUserS) > 0) {
					$searchUser = mysql_fetch_row($searchUserS);
					$autorSQL = " AND c.autor = '$searchUser[0]' ";
					$shortTitle = $title = "autor ~ $searchUser[1]";
					$GLOBAL_description = "Výpis všech článku na serveru Aragorn.cz od autora: $searchUser[1]";
				}
				else {
					header("Location: $inc/clanky/");
					exit;
				}
			}
			else {
				header("Location: $inc/clanky/");
				exit;
			}
		}
		else {
			$sA = mysql_query ("SELECT c.*, u.login, u.login_rew, u.level FROM 3_clanky AS c, 3_users AS u WHERE c.nazev_rew = '$slink' AND c.schvaleno = '1' AND c.autor = u.id");
			$cA = mysql_num_rows($sA);
			$oA = mysql_fetch_object($sA);
			if ( $cA > 0){
				$id = $oA->id;$aid = $id;$sid = 1;
				$nazev = stripslashes($oA->nazev);$nazev = mb_strtoupper(mb_substr($nazev, 0, 1)).mb_substr($nazev, 1);
				$nazev_rew = $oA->nazev_rew;
				$anotace = stripslashes($oA->anotace);
				if ($oA->compressed) {
					$clanek = spit(gzuncompress($oA->text), 0);
				}
				else {
					$clanek = spit($oA->text, 0);
				}
				$autor = $oA->login;$autor_rew = $oA->login_rew;$autorId = $oA->autor;
				$date = $oA->schvalenotime;
				$hodnoceni = $oA->hodnoceni;$hodnotilo = $oA->hodnotilo;
				$sekce = ss($oA->sekce);$title = $nazev;
				$GLOBAL_description = "název: $title, autor: $autor, sekce: $sekce, anotace: "._htmlspec($anotace);
				$cFound = true;
			}else{
				$title .= ": adresa $slink nenalezena";
			}
			if ($sslink == "stats") {
				$title = "Statistiky | ".$title;
			}
		}
	}else{
		if (isset($_GET['sekce']) && isset($sekceSEO[$_GET['sekce']])) {
			$title = "Výpis sekce ".ss($sekceSEO[$_GET['sekce']]);
		}
	}

	$shortTitle = $title;

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
		$title .= " ($pg_index stran$konc zpět)";
	}
	elseif(isset($_GET['index'])) {
		$title .= " ($time)";
	}
	else {
		$title .= " (1. strana)";
	}

}

$title .= " | Články";

if (!$GLOBAL_description) $GLOBAL_description = $title;

?>