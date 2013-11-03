<?php
$slink = "";
$link = "";
$sslink = "";
$marked = array_fill(0, 10, "");
$needDHTML = false;

if (isset($_GET['link'])) {
	$link = get_magic_quotes_gpc() ? $_GET["link"] : addslashes($_GET["link"]);
}
if (isset($_GET['slink'])) {
	$slink = get_magic_quotes_gpc() ? $_GET["slink"] : addslashes($_GET["slink"]);
}
if (isset($_GET['sslink'])) {
	$sslink = get_magic_quotes_gpc() ? $_GET["sslink"] : addslashes($_GET["sslink"]);
}

if (isset($_GET['rub']) && isset($_GET['id'])) {
	$rub = get_magic_quotes_gpc() ? $_GET["rub"] : addslashes($_GET["rub"]);
	if ($rub != "") {
		header("Location: http://old.aragorn.cz".$_SERVER["REQUEST_URI"]);
		exit;
	}
	else unset($rub);
}

function bd($string){ //odstrani diakritiku
$trans = array("á"=>"a", "ä"=> "a", "č"=>"c", "ď"=>"d", "é"=>"e", "ě"=>"e", "ë"=>"e", "í"=>"i", "&#239;"=>"i", "ň"=>"n", "ó"=>"o", "ö"=>"o", "ř"=>"r", "š"=>"s", "ť"=>"t", "ú"=>"u", "ů"=>"u", "ü"=>"u", "ý"=>"y", "&#255;"=>"y", "ž"=>"z", "Á"=>"A", "Ä"=>"A", "Č"=>"C", "Ď"=>"D", "É"=>"E", "Ě"=>"E", "Ë"=>"E", "Í"=>"I", "&#207;"=>"I", "Ň"=>"N", "Ó"=>"O", "Ö"=>"O", "Ř"=>"R", "Š"=>"S","Ť"=>"T", "Ú"=>"U", "Ů"=>"U", "Ü"=>"U", "Ý"=>"Y", "&#376;"=>"Y", "Ž"=>"Z");
return strtr($string, $trans);
}

// prevod na rew titulky
function do_seo ($titulek=""){
	return do_seo_advanced($titulek);
}

//distribuce promennych pres rew
if (isset($_SESSION['fresh']) && $_SESSION['fresh'] != false && $_SESSION['fresh'] != "" && isset($_SESSION['login']) && $_SESSION['login'] != "") {
// jedeme nejdriv ukazat uvodniky :)
	$title = $itIsApril ? 'Wo co go???' : "Úvodníky";
	$uvodniky = true;
}
else {
// jedeme ukazat hledanou stranku :)
	$uvodniky = false;

switch ($link){

case "registrace":  // ok
	$marked[0] = " id='marked'";
	$GLOBAL_description = "Jednoduchá a rychlá registrace na server Aragorn.cz.";
	$title = "Registrace";
break;

case "uspesna-registrace": // ok
	$marked[0] = " id='marked'";
	$GLOBAL_description = "Registrace byla zpracována a byl odeslán e-mail s potvrzovacím odkazem.";
	$title = "Úspěšná registrace";
break;

case "potvrzeni-registrace":  // ok
	$marked[0] = " id='marked'";
	$GLOBAL_description = "Potvrzení registrace - pomocí aktivačního odkazu.";
	$title = "Potvrzení registrace";
break;

case "uzivatele": // ok
	$GLOBAL_description = "Uživatelé komunitního serveru Aragorn.cz. Vyhledávání a konkrétní profily. Konkrétní jeden profil obsahuje diskuzní témata, obrázky z galerie, autorské textů v článcích, hry a postavy v herně, přátele uživatele, další texty a také komentáře uživatelů.";
	include "./sekce/uzivatele_head.php";
break;

case "nastaveni": // ok
	$title2 = $itIsApril ? "Poštelovat" : "Nastavení";
	$marked[0] = " id='marked'";
	switch ($slink){
		case "osobni":
			$title = "Osobní - Nastavení";
		break;
		case "systemove":
			$title = "Systémové - Nastavení";
		break;
		case "chat":
			$title = "Chat - Nastavení";
		break;
		default:
			$title = $itIsApril ? "Poštelováníčko" : "Nastavení";
		break;
	}
break;

case "bonus": // ok
	$title = "Bonus";
break;

case "timeout": // ok
	$title = "Bezpečnostní odhlášení";
break;

case "admins":  // ok
case "administratori":  // ok
	$title = $itIsApril ? "Těžkooděnci" : "Administrátoři";
	$GLOBAL_description = "Výpis současných a bývalých administrátorů komunitního serveru Aragorn.cz";
break;

case "posta": // ok
	$title = $itIsApril ? "Pošli to dál" : "Poštolka";
	$needDHTML = true;
break;

case "posta-new": // ok
	$needDHTML = true;
	$title = $itIsApril ? "Pošli to dál" : "Poštolka";
break;

case "zalozky":
	$title = $title2 = "Skiny &amp; Záložky";
break;

case "herna": // ok
	$needDHTML = true;
	$marked[1] = " id='marked'";
	$title = $itIsApril ? "Xbox / PS3" : "Herna";
	$GLOBAL_description = "Herna. Hry, jeskyně, kroniky, ORP a DrD systém. Jeden ze základních kamenů serveru Aragorn.cz";
	include "./sekce/herna_head.php";
break;

case "diskuze": // ok
case "diskuse": // ok
	$link = "diskuze";
	$marked[2] = " id='marked'";
	$title = $itIsApril ? "Krafárna" : "Diskuze";
	$GLOBAL_description = "Diskuzní oblasti, jednotlivá témata, ankety, statistiky. Vážná i odlehčená témata.";
	include "./sekce/diskuze_head.php";
break;

case "galerie": // ok
	$marked[4] = " id='marked'";
	$title = $itIsApril ? "Omalovánky" : "Galerie";
	$GLOBAL_description = "Autorská tvorba uživatelů registrovaných na serveru Aragorn.cz. Malby, kresby, digitální grafika, počítačové 3D modely.";
	include "./sekce/galerie_head.php";
break;

case "napoveda":  // ok
	$marked[5] = " id='marked'";
	$GLOBAL_description = "Odpovědi na nejčastější otázky. Jak Aragorn.cz funguje a kde se co dá nalézt. Informace takřka k čemukoliv.";
	$title = $itIsApril ? "Když nevíš" : "Nápověda";
break;

case "clanky":  // ok
case "clanky-test": //ok
	$marked[3] = " id='marked'";
	$GLOBAL_description = "Texty, jejichž autoři jsou na komunitním serveru Aragorn.cz. Kritika, hodnocení, od sekcí poezie až po recenze a záznámy z her.";
	include "./sekce/clanky_head.php";
break;

case "chybny-login":  // ok
	$title = "Chybný login";
break;

case "chat":  // ok
	$title2 = "Chat";
	$GLOBAL_description = "Instantní komunikace mezi uživateli = chat. Včetně unikátního Rozcestí, které je kompromisem mezi on-line hrou na hrdiny v jeskyni a obyčejným chatem..";
	include "./sekce/chat_head.php";
break;

case "room":  // ok
	$id = $_GET['slink'];
	include "./chat/chat.php";
	exit;
break;

case "ajax_room": // ok
	$id = $_GET['slink'];
	include "./ajax_chat/chat.php";
	exit;
break;

case "rs":  // ok
	if (isset($_SESSION['uid']) && $_SESSION['lvl'] > 2){
		include "./rs/index2.php";
		exit;
	}
break;

case "cave":  // ok
	$id = $_GET['slink'];
	include "./cave-c/cave.php";
	exit;
break;

case "rss":  // ok
	include "./rss.php";
	exit;
break;

default:  // ok
  $title = $itIsApril ? "Wo co go???" : "Úvodníky";
  $sslink = "";
  $slink = "";
	$GLOBAL_description = "Aktuální úvodníky které obsahují novinky. Nejnovější články a naposledy schválené hry z herny. Náhodný obrázek z galerie.";

  if ($link != "logout") $link = "";
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
	elseif (isSet($_GET['index']) && $pg_index  == 1) {
		$title .= " (1. strana)";
	}
break;

}

}
?>