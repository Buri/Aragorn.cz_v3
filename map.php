<?php
ob_start("ob_gzhandler");

$slink = "";
$link = "";
$sslink = "";

if (isset($_GET['link'])) {
	$link = $_GET['link'];
}
if (isset($_GET['slink'])) {
	$slink = $_GET['slink'];
}
if (isset($_GET['sslink'])) {
	$sslink = $_GET['sslink'];
}

$time = time();
mb_internal_encoding("UTF-8");

$map_found = false;
$our_map = false;
$caveID = 0;
$prava = 0;
$hFound = false;
$map_col = 24;
$map_bg = "map_chodby";

function map_povrchy($post="") {
	global $map_col,$map_bg;
	$ar = array();
	switch ($post) {
		case "map_chodby":
			$ar[0] = $map_col = 24;
			$ar[1] = $map_bg = "map_chodby";
		break;
		case "map_world":
			$ar[0] = $map_col = 32;
			$ar[1] = $map_bg = "map_world";
		break;
		default:
			$ar[0] = $map_col = 24;
			$ar[1] = $map_bg = "map_chodby";
		break;
	}
	return $ar;
}

function do_map($nazev,$data) {
	global $map_col,$map_bg;

	$map_sit = "MapEdMapA";
	$first_col = ceil($map_col/2);
	$all_col = $map_col;

	$txt = "<"."?xml version=\"1.0\" encoding=\"utf-8\">
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"cs\" xml:lang=\"cs\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<title>Aragorn &brvbar; Map Editor</title>
<link href=\"/system/mapeditor.css\" rel=\"stylesheet\" type=\"text/css\" />
<style type=\"text/css\">
.mapEdMapa td { background-image: url('system/lay/$map_bg.gif'); }
</style>
<script type=\"text/javascript\" src=\"/js/mapeditor.js\">
</script>
<script type=\"text/javascript\">
 FIRST_COL = $first_col;
 ALL_COL = $all_col;
 MAP_POVRCH_SRC = \"/system/lay/$map_bg.gif\";
 MAP_NET = \"$map_sit\";
 MAP_ACTIONS = \"\";
 w2s = \"map-show-here\";
</script>
</head>
<body onload=\"setTimeout('mapInit();VykresliSit(w2s);VykresliMapu();mapLoaded();',150);\" class=\"showMe\"><div id=\"map-loaded\">Nahrávám mapu</div><div id=\"check4js\">Váš prohlížeč nepodporuje Javascript. MapEditor jej však potřebuje ke své funkci.</div><script type=\"text/javascript\">checkJS();</script>
<h3>".htmlspecialchars(stripslashes($nazev),ENT_QUOTES,"UTF-8")."</h3>\n<div id='map-show-here'>\n";

	$datas = explode("|",$data);
	$rozmerY = $datas[0];
	$rozmerX = $datas[1];
	
	if (($rozmerY > 40) || ($rozmerX > 60) || ($rozmerX < 5) || ($rozmerY < 5) || (count($datas)!= 3)) {
		header ("Location: /map.php?error=1");
		exit;
	}
	$txt .= "</div><input type=\"hidden\" name=\"mapEdSource\" id=\"mapEdSource\" value=\"".$data."\" />
</body>
</html>";

return $txt;
}

function map_top_menu($do_new=true) {
	$t = "<a href=\"#\" onclick=\"mapInit();mapEdPovrchyTypyShow();VykresliSit('mapEdPJ');VykresliMapu();MM_findObj('form2do').style.display='block';return false;\">Načíst data</a>";
	if ($do_new == true) {
		$t = "<a href=\"#\" onclick=\"baseMap();VykresliSit('mapEdPJ');mapEdPovrchyTypyShow();VygenerujMapu();MM_findObj('form2do').style.display='block';return false;\">Nová mapa</a>";
	}
	return "
		<table id=\"menuout\">
			<tr><td>Mapa</td>
			<td class=\"menuin\">$t</td>
			<td>Řádek</td>
			<td class=\"menuin\">
				<a href=\"#\" onclick=\"Pridej('row',MAP_NET);return false;\">Přidat řádek na konec</a><br />
				<a href=\"#\" onclick=\"Uber('row',MAP_NET);return false;\">Smazat poslední řádek</a>
			</td>
			<td>Sloupec</td>
			<td class=\"menuin\">
				<a href=\"#\" onclick=\"Pridej('col',MAP_NET);return false;\">Přidat sloupec na konec</a><br />
				<a href=\"#\" onclick=\"Uber('col',MAP_NET);return false;\">Smazat poslední sloupec</a>
			</td>
			<td>Posuny</td>
			<td class=\"menuin2\">
				<a href=\"#\" onclick=\"Posun('up');return false;\">Nahoru</a><br />
				<a href=\"#\" onclick=\"Posun('left');return false;\">Vlevo</a> <a href=\"#\" onclick=\"Posun('right');return false;\">Vpravo</a><br />
				<a href=\"#\" onclick=\"Posun('down');return false;\">Dolu</a>
			</td>
			</tr>
		</table>
";
}

function make_map($do_new,$nazev,$cave,$cavename,$bg,$col,$data="",$id="") {
	$map_sit = "MapEdMapA";
	$first_col = ceil($col/2);
	$all_col = $col;
	if ($do_new) {
		$akce = "make";
		$akce_name = "Uložit";
		$nazev = htmlspecialchars(stripslashes($nazev),ENT_QUOTES,"UTF-8");
	}
	else {
		$nazev = htmlspecialchars(stripslashes($nazev),ENT_QUOTES,"UTF-8");
		$akce = "save&amp;id=$id";
		$akce_name = "Upravit";
	}

	$txt = "<"."?xml version=\"1.0\" encoding=\"utf-8\">
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"cs\" xml:lang=\"cs\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<title>Aragorn &brvbar; Map Editor</title>
<link href=\"/system/mapeditor.css\" rel=\"stylesheet\" type=\"text/css\" />
<style type=\"text/css\">
	.mapEdMapa td { background-image: url('system/lay/$bg.gif'); }
</style>
<script type=\"text/javascript\" src=\"/js/mapeditor.js\">
</script>
<script type=\"text/javascript\">
 FIRST_COL = $first_col;
 ALL_COL = $all_col;
 MAP_POVRCH_SRC = \"/system/lay/$bg.gif\";
 MAP_NET = \"$map_sit\";
</script>
</head>

<body class=\"showMe\"><div id=\"check4js\">Váš prohlížeč nepodporuje Javascript. MapEditor jej však potřebuje ke své funkci.</div><script type=\"text/javascript\">checkJS();</script>
<div id=\"page\">
	<div id=\"content\">".map_top_menu($do_new)."
		<div class='mapBG'>
			<div id=\"mapEdPJ\"></div>
			<form id=\"mapEdForm\" name=\"mapEdForm\"><div id=\"mapEdPovrchy\"></div></form>
		</div>
	</div>
<form action=\"map.php?do=$akce&amp;cave=$cave&amp;povrch=$bg\" id=\"form2do\" method=\"post\" style=\"display: none; clear:both\" onsubmit=\"VygenerujMapu();\">
	<input type=\"hidden\" name=\"mapEdSource\" id=\"mapEdSource\" value=\"$data\" />
	Název mapy: <input type=\"text\" size=\"40\" name=\"nazev_js_map\" value=\"$nazev\" /> |
	<input type=\"submit\" value=\"$akce_name mapu\" class=\"button\" /><br />
	Jeskyně: <strong>$cavename</strong>
</form>
</div>
</body>
</html>";

return $txt;
}

/* ------------------------ 
    End functions for maps
   ------------------------
*/

if (isset($_POST['povrch_typ'])) {
	$map_conf = map_povrchy($_POST['povrch_typ']);
}
else {
	$map_conf = map_povrchy();
}
$map_col = $map_conf[0];
$map_bg = $map_conf[1];

session_start();

include_once "./db/conn.php";
include_once "./add/auth.php";

if ($LogedIn) {
	if (isSet($_GET['cave'])) {
		$cave = addslashes($_GET['cave']);
		$jeskyneS = mysql_query("SELECT id,uid,typ,nazev,nazev_rew FROM 3_herna_all WHERE nazev_rew = '$cave' AND schvaleno = '1'");
		if (mysql_num_rows($jeskyneS)>0) {
			$hFound = true;
			$hItem = mysql_fetch_object($jeskyneS);
			$vypis_map = "/herna/$hItem->nazev_rew/mapy/";
			if ($LogedIn) {
				if ($_SESSION['uid'] == $hItem->uid) {
					$allow = "pj";
					$prava = 1;
				}
				else {
					$pjs = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_pj WHERE uid = '$_SESSION[uid]' AND cid = '$hItem->id' AND mapy = '1' AND schvaleno = '1'"));
					$prava = $pjs[0];
					if ($prava == 0) {
						if ($hItem->typ == "0") {
						 $jTypString = "drd";
						}
						else {
							$jTypString = "orp";
						}
						$pravas = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE cid = '$hItem->id' AND uid = '$_SESSION[uid]' AND schvaleno = '1'"));
						$prava = $pravas[0];
					}
				}
			}
		}
	}
}

if (isSet($_GET['id'])) {
	$idm = addslashes($_GET['id']);
	$mapSrc = mysql_query("SELECT * FROM 3_herna_maps WHERE id = '$idm'");
	if (mysql_num_rows($mapSrc) > 0) {
		$map = mysql_fetch_object($mapSrc);
		if ($map->soubor == "js") {
			$map_found = true;
			$map_bg = $map->povrch;
			if (!isset($_GET['cave']) && $LogedIn) {
				$jeskyneS = mysql_query("SELECT id,typ,uid,nazev_rew,nazev FROM 3_herna_all WHERE id = '$map->cid' AND schvaleno='1'");
				if (mysql_num_rows($jeskyneS)>0) {
					$hFound = true;
					$hItem = mysql_fetch_object($jeskyneS);
					$vypis_map = "/herna/$hItem->nazev_rew/mapy/";
					if ($LogedIn) {
						if ($_SESSION['uid'] == $hItem->uid) {
							$prava = 1;
						}
						else {
							$pjs = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_pj WHERE uid = '$_SESSION[uid]' AND cid = '$hItem->id' AND mapy = '1' AND schvaleno = '1'"));
							$prava = $pjs[0];
							if ($prava == 0) {
								if ($hItem->typ == "0") {
								 $jTypString = "drd";
								}
								else {
									$jTypString = "orp";
								}
								$pravas = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE cid = '$hItem->id' AND uid = '$_SESSION[uid]' AND schvaleno = '1'"));
								$prava = $pravas[0];
							}
						}
					}
				}
			}
		}
		else {
			echo "<"."?xml version=\"1.0\" encoding=\"utf-8\">
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"cs\" xml:lang=\"cs\">
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
	<meta http-equiv='pragma' content='no-cache' />
	<title>Mapa :: ".htmlspecialchars($map->nazev,ENT_QUOTES,"UTF-8")."</title>
	<style type=\"text/css\"> body { color: #aaa; background: #333; } img { border: none; vertical-align:text-top; } </style>
</head>

<body>
<h3>".htmlspecialchars($map->nazev,ENT_QUOTES,"UTF-8")."</h3>\n".
"<img src=\"/system/mapy/$map->datas\" title=\"Mapa - ".htmlspecialchars($map->nazev,ENT_COMPAT,"UTF-8")."\" />\n".
"</body>
</html>";
			exit;
		}
	}
	else {
		die("<html>
	<head>
		<title>Error - Mapa s ID '$_GET[id]' nebyla nalezena.</title>
	</head>
	<body>
		<big>Mapa s ID '$_GET[id]' nebyla nalezena.</big>
	</body>
</html>");
		exit;
	}
}

if (isSet($_GET['do'])) {
	switch ($_GET['do']) {
		case "edit":
			if (isSet($_POST['povrch_typ']) && $prava>0) {
				$map_conf = map_povrchy($_POST['povrch_typ']);
				$map_col = $map_conf[0];
				$map_bg = $map_conf[1];
			}
			if ($map_found && $hFound && $map->cid == $hItem->id) {
				echo make_map(false,$map->nazev,$hItem->nazev_rew,$hItem->nazev,$map_bg,$map_col,$map->datas,$map->id);
				exit;
			}
		break;
		case "save":
			if ($map_found && $prava>0 && $hFound && $map->cid == $hItem->id && isset($_POST['mapEdSource']) && isset($_POST['nazev_js_map'])) {
				if (mb_strlen($_POST['mapEdSource'])>5 && mb_strlen($_POST['nazev_js_map'])>1) {
					$datas = addslashes($_POST['mapEdSource']);
					$sizee = mb_strlen($datas, "ISO-8859-1");
					$name = addslashes($_POST['nazev_js_map']);
					$sql = mysql_query("UPDATE 3_herna_maps SET nazev='$name',datas='$datas',size='$sizee' WHERE id = '$map->id'");
				}
				header("Location: /map.php?id=$map->id&do=load");
				exit;
			}
		break;
		case "load":
			if ($map_found) {
				echo do_map($map->nazev,$map->datas);
				exit;
			}
		break;
		case "make":
			if (!$LogedIn || $prava == 0) {
				die("<html>
	<head>
		<title>Error</title>
	</head>
	<body>
		<big>Mapu muze vytvorit jen aktivni hrac nebo PJ jeskyne.</big>
	</body>
</html>");
				exit;
			}
			elseif ($LogedIn && isset($_GET['povrch'],$_POST['mapEdSource'],$_POST['nazev_js_map']) && $hFound && $prava>0) {
				$map_conf = map_povrchy($_GET['povrch']);
				$map_col = $map_conf[0];
				$map_bg = $map_conf[1];
				$src = addslashes($_POST['mapEdSource']);
				$src_nazev = addslashes($_POST['nazev_js_map']);
				$src_size = mb_strlen($src, "ISO-8859-1");
				$sql = mysql_query("INSERT INTO 3_herna_maps (cid,nazev,soubor,datas,povrch,size) VALUES ('$hItem->id','$src_nazev','js','$src','$map_bg',$src_size)");
				$map_id = mysql_insert_id();
				header("Location: /map.php?id=$map_id&do=load");
				exit;
			}
		break;
		case "new":
			if ($_GET['typ']=="js" && $hFound && $prava>0 && isset($_POST['povrch_typ'], $_POST['nazev_mapy_js'])) {
				$map_conf = map_povrchy($_POST['povrch_typ']);
				$map_col = $map_conf[0];
				$map_bg = $map_conf[1];
				echo make_map(true,$_POST['nazev_mapy_js'],$hItem->nazev_rew,$hItem->nazev,$map_bg,$map_col,"","");
				exit;
			}
			else {
				die("<html>
	<head>
		<title>Error</title>
	</head>
	<body>
		<big>Mapu muze vytvorit jen aktivni hrac nebo PJ jeskyne.</big>
	</body>
</html>");
				exit;
			}
		break;
		default:
			header ("Location: $vypis_map");
			exit;
		break;
	}
}
elseif ($map_found) {
	echo do_map($map->nazev,$map->datas);
	exit;
}
else {
	header ("Location: /");
	exit;
}

header ("Location: /");
exit;
?>