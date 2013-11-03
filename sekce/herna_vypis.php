<?php

$podleH = "j.zalozeno DESC";
//$podleH = "j.spendlik DESC, j.zalozeno DESC";

$podle = "";
$podleOptions = array(''=>'','aktivity'=>'aktivity','zalozeni'=>'zalozeni','vlastnika'=>'vlastnika','nazvu'=>'nazvu');

if (isset($_GET['podle'])) {
	switch ($_GET["podle"]) {
		case "aktivity":
			$podleH = "j.aktivita DESC";
			$podle = "aktivity";
		break;
		case "zalozeni":
			$podleH = "j.zalozeno DESC";
			$podle = "zalozeni";
		break;
		case "vlastnika":
			$podle = "vlastnika";
			$podleH = "u.login_rew ASC, j.nazev_rew ASC";
		break;
		case "nazvu":
			$podle = "nazvu";
			$podleH = "j.nazev_rew ASC";
		break;
		default:
			$podleH = "j.zalozeno DESC";
//			$podleH = "j.spendlik DESC, j.zalozeno DESC";
			$podle = "";
		break;
	}
	$podleOptions[$podle] .= "' selected='selected";
}

$typ_link = "<span class='hide'> | </span> <a href='#' onclick='hide(\"filtr\");return false;' class='permalink' title='Zobrazit jen jeden systém / seřadit jinak / hledat'>Filtrovat</a>";

$typH = "";
$sekce = "";
if (isset($_GET['sekce'])) {
switch ($_GET["sekce"]) {
	case "drd":
		$typH = "AND j.typ = '0'";
		$sekce = "drd";
	break;
	case "orp":
		$sekce = "orp";
		$typH = "AND j.typ = '1'";
 	break;
 	default:
 		$typH = "";
 		$sekce = "";
 	break;
}
}

if (!isSet($_GET["index"])){
  $index = 1;
}else{
  $index = (int) ($_GET["index"]);
  if ($index < 1) {
  	$index = 1;
	}
}

$searchSQL = "";
$relevancySQL = "";
if (isset($_GET['search'])){
	$q = ltrim(str_replace("%", "*", trim($_GET['search'])), '%_');
	if (mb_strlen($q) > 3) {
		$q = sprintf("%s", mysql_real_escape_string($q));
		$searchSQL = " MATCH (j.keywords, j.nazev, j.popis, j.hraci_hleda) AGAINST ('".$q."' IN BOOLEAN MODE) ";
//		$relevancySQL = $searchSQL." AS relevancy, ";
		$searchSQL = " AND ".$searchSQL;
//		$podleH = ' relevancy DESC, '.$podleH;
	}
	elseif (mb_strlen($q) > 1) {
		$q = sprintf("%s", mysql_real_escape_string(ltrim(str_replace("*", "%", trim($_GET['search'])), '_')));
		$searchSQL = " (j.keywords LIKE '$q%' OR j.nazev LIKE '$q%' OR j.popis LIKE '$q%' OR j.hraci_hleda LIKE '$q%') ";
		$searchSQL = " AND ".$searchSQL;
	}
}

$kolik = $hernaPC;
$od = ($index - 1) * $kolik; //od kolikate polozky zobrazit
$qq = $hiddenSQL = $filterCavesSQL = "";
$filterCaves = $hiddenCaves = $favesCaves = array();
$filter_caves = array();
$hiddenCavesDiv = "";

if ($LogedIn) {
	$get_hidden_caves = mysql_query("SELECT serialized FROM 3_users_settings WHERE uid = $_SESSION[uid]");
	if ($get_hidden_caves && mysql_num_rows($get_hidden_caves) > 0) {
		$hidden_caves_json = json_decode(array_pop(mysql_fetch_row($get_hidden_caves)), true);
		if (!isset($hidden_caves_json['game-hide'])) {
			$hidden_caves = array();
		}
		else {
			$hidden_caves = array_values($hidden_caves_json['game-hide']);
		}

		if (!isset($hidden_caves_json['game-filter'])) {
			$filter_caves = array();
		}
		else {
			$filter_caves = array_values($hidden_caves_json['game-filter']);
		}

//		if (!isset($hidden_caves['game-fav'])) {
			$faves_caves = array();
//		}
//		else {
//			$faves_caves = array_values($hidden_caves['game-fav']);
//		}

		$caves_to_ids = array_values(array_merge($faves_caves, $hidden_caves));

		if (count($caves_to_ids) > 0) {
			$hiddenSQL = addslashes(join(",", $caves_to_ids));
			$hidden_cave_names = mysql_query("SELECT id, nazev, nazev_rew FROM 3_herna_all WHERE id IN ($hiddenSQL) ORDER BY nazev ASC");
			if ($hidden_cave_names && mysql_num_rows($hidden_cave_names) > 0) {
				while($one_hidden_cave = mysql_fetch_object($hidden_cave_names)) {
//					if (in_array($one_hidden_cave->id, $faves_caves)) {
//						$favesCaves[] = $one_hidden_cave;
//					}
//					else {
						$hiddenCaves[] = $one_hidden_cave;
//					}
				}
			}
			$hiddenSQL = " AND j.id NOT IN (".$hiddenSQL.")";
		}
		if (count($filter_caves) > 0) {
			foreach($filter_caves as $k) {
				$filterCaves[] = " NOT MATCH (j.nazev, j.popis, j.hraci_hleda, j.keywords) AGAINST ('".addslashes($k)."' IN BOOLEAN MODE)";
//				$filterCaves[] = " (j.keywords IS NULL OR j.keywords NOT LIKE '%".addslashes($k)."%') ";
//				$filterCaves[] = " (j.popis IS NULL OR j.popis NOT LIKE '%".addslashes($k)."%') ";
//				$filterCaves[] = " (j.hraci_hleda IS NULL OR j.hraci_hleda NOT LIKE '%".addslashes($k)."%') ";
//				$filterCaves[] = " (j.nazev IS NULL OR j.nazev NOT LIKE '%".addslashes($k)."%') ";
			}
			$filterCavesSQL = " AND ".join(" AND ", $filterCaves);
		}
	}
}

// echo "<!-- ".$hiddenSQL." -->"; // debug
$sqlCount = "SELECT count(*) FROM 3_herna_all AS j WHERE j.schvaleno = '1' $filterCavesSQL $hiddenSQL $searchSQL $typH";
// echo "<!-- ".$sqlCount." -->"; // debug

$aZ = mysql_query($sqlCount);
$aS = mysql_fetch_row($aZ);
$aC = $aS[0];

if ($typH != "") {
	echo "<h2 class=\"h2-head\"><a href=\"/herna/\" title=\"$titleHerna\">$titleHerna</a></h2><h3><a href='/herna/' title='Na výpis jeskyní'>Herna". ($sekce == "drd"? " - Dračí doupě" : ($sekce == "orp" ? " - Systém <acronym title='Open Role Play - Volný hrací svět' xml:lang='cs'>ORP</acronym>" : "" )); echo "</a></h3>\n";
}
else {
	$h3Addon = $h3Prepend = "";
	if ($searchSQL != ""){
		$h3Prepend = "Hledání - ";
	}
	if ($H3Add) {
		$h3Addon = $titlePodle;
	}
	echo "<h2 class=\"h2-head\"><a href=\"/herna/\" title=\"$titleHerna\">$titleHerna</a></h2><h3><a href='/herna/' title='Výpis jeskyní'>".$h3Prepend."Výpis jeskyní".$h3Addon."</a></h3>\n";
}

	echo "<p class='submenu'><a href='/herna/new/' class='permalink' title='Založit novou jeskyni'>Nová jeskyně</a>";

if ($LogedIn == true) {
		echo "<span class='hide'> | </span>
	<a href='/herna/my/' class='permalink' title='Na výpis mých jeskyní a postav'>Moje hry</a>";

	if ($_SESSION['lvl'] >= 2 && (count($hiddenCaves) > 0 || count($filterCaves) > 0)) {
		echo "<span class='hide'> | </span>
	<a href='#' onclick='hide(\"hiddenCaves\");return false;' class='permalink' title='Seznam skrytých jeskyní z výpisu Herny'>Skryté</a>";
		$hiddenCavesDiv = "<div id='hiddenCaves' class='hide'><div class='text'><p><strong>Jeskyně, které se nezobrazí ve výpisu Herny:</strong></p><ul>";
		$qq = "";
		foreach($filter_caves as $qa){
			$hiddenCavesDiv .= "<li>filtrovat: <strong>"._htmlspec($qa)."</strong></li>";
			$qq = $qa;
		}
		foreach($hiddenCaves as $c){
			$hiddenCavesDiv .= "<li><a href='#' class='ajaxlink game-unhide' title='Odebrat'>x</a> <a href='/$link/$c->nazev_rew/'>$c->nazev</a></li>";
		}
		$hiddenCavesDiv .= "</ul>";
		$hiddenCavesDiv .= "";
		$hiddenCavesDiv .= "</div></div>";
	}
}

	echo "$typ_link</p>

<div id='filtr'" . (isset($_GET['search']) ? "" : " class='hide'") . "><div class='f-top'></div>
<div class='f-middle'>
	<form action='/herna/' name='txt' method='get' class='f'>
		<fieldset>
		<legend>Filtrovat výpis Herny <a href=\"#\" onclick=\"hide('filtr');return false;\" class='permalink flink' title='Zavřít'>Zavřít</a></legend>
		<label><span>Systém</span><select name='sekce'><option value='vse'>- - - - -</option><option value='drd'".($sekce=="drd"?" selected='selected'":"").">DrD - Dračí doupě</option><option value='orp'>ORP - Open Role Play</option></select></label>
		<label><span class='helper' title='Nejméně 4 znaky, fungují spec. znaky (* - +) a uvozovky pro spojení konkrétních slov'>Hledat</span><input name='search' value='"._htmlspec(isset($_GET['search']) ? $_GET['search'] : '')."'></label>
		<label><span>Setřídit podle</span><select name='podle'><option value='".$podleOptions['zalozeni']."'>Datumu založení</option><option value='".$podleOptions['aktivity']."'>Poslední aktivity</option><option value='".$podleOptions['nazvu']."'>Názvu jeskyně</option><option value='".$podleOptions['vlastnika']."'>Vlastníka jeskyně</option></select></label>
		<input class='button' type='submit' value='Zobrazit' />
		</fieldset>
	</form>
";

if ($LogedIn && $_SESSION['lvl'] >= 2) {
	echo "<form action='/herna/?akce=herna-filter' name='filtering-form' method='post' class='f'><fieldset><legend>Trvalý filtr Herny</legend><label><span class='helper' title='Text k filtrování'>Filtrovat: </span><input name='filter' value='"._htmlspec($qq)."'></label><input class='button' type='submit' value='Uložit filtr' /></fieldset></form>\n";
}

echo "
	</div>
<div class='f-bottom'></div>
</div>
$hiddenCavesDiv

	<p class='strankovani'>";
	$pagination = make_pages($aC, $kolik, $index);
	echo $pagination;
	echo "</p>\n";

	if ($vypisMsg) {
		infow($vypisMsg);
	}

ob_flush();

$herna_moje = array();
if ($LogedIn) {
	$herna_myPJ = mysql_query("SELECT id FROM 3_herna_all WHERE uid = $_SESSION[uid] AND schvaleno = '1'");
	while ( $hPJ = mysql_fetch_object($herna_myPJ) ) {
		$herna_moje[$hPJ->id] = 2;
	}
	$herna_myG1 = mysql_query("SELECT cid FROM 3_herna_postava_drd WHERE uid = $_SESSION[uid]");
	while ( $hG1 = mysql_fetch_object($herna_myG1) ) {
		$herna_moje[$hG1->cid] = 1;
	}
	$herna_myG2 = mysql_query("SELECT cid FROM 3_herna_postava_orp WHERE uid = $_SESSION[uid]");
	while ( $hG2 = mysql_fetch_object($herna_myG2) ) {
		$herna_moje[$hG2->cid] = 1;
	}
}

echo "<div class='herna-vypis'>\n";

if (!$LogedIn) {
	$sql = "SELECT $relevancySQL j.id, j.typ, j.povolreg, j.nazev, j.nazev_rew, j.popis, j.typ, j.aktivita, j.hraci_pocet, j.hraci_hleda, j.zalozeno, u.login AS vlastnik, u.login_rew AS vlastnik_rew, 0 AS unread_comms,
(SELECT COUNT(*) FROM 3_comm_4 AS cm WHERE cm.aid = j.id AND (cm.whispering = '' OR cm.whispering IS NULL)) AS all_comms,
0 AS v_uid
FROM 3_herna_all AS j, 3_users AS u 
WHERE u.id = j.uid $filterCavesSQL $hiddenSQL $typH $searchSQL AND j.schvaleno = '1' 
ORDER BY $podleH 
LIMIT $od,$kolik";
}
else {
	$sql = "SELECT $relevancySQL j.id, j.typ, j.povolreg, j.nazev, j.nazev_rew, j.popis, j.typ, j.aktivita, j.hraci_pocet, j.hraci_hleda, j.zalozeno, u.login AS vlastnik, u.login_rew AS vlastnik_rew,
(SELECT COUNT(*) FROM 3_comm_4 AS cm WHERE cm.aid = j.id AND (cm.whispering LIKE '%#$_SESSION[uid]#%' OR cm.whispering = '' OR cm.whispering IS NULL OR cm.uid = $_SESSION[uid])) AS all_comms,
(SELECT COUNT(*) FROM 3_visited_4 AS v, 3_comm_4 AS cm WHERE v.aid = j.id AND v.uid = ".$_SESSION['uid']." AND cm.aid = j.id AND cm.id > v.lastid  AND (cm.whispering LIKE '%#$_SESSION[uid]#%' OR cm.whispering = '' OR cm.whispering IS NULL OR cm.uid = $_SESSION[uid])) AS unread_comms,
(SELECT COUNT(*) FROM 3_visited_4 AS vv WHERE vv.aid = j.id AND vv.uid = ".$_SESSION['uid'].") AS v_uid
FROM 3_herna_all AS j, 3_users AS u 
WHERE u.id = j.uid $filterCavesSQL $hiddenSQL $typH $searchSQL AND j.schvaleno = '1' 
ORDER BY $podleH 
LIMIT $od,$kolik";
}

//  echo "<!-- ".$sql." -->"; // debug

$herna_all = mysql_query($sql);

if (strlen($filterCavesSQL) > 2) {
	infow('Je zapnuté trvalé filtrování herny.');
}

if (isset($_GET['error']) && $_GET['error'] == 15) {
	infow('Filtrování potřebuje slova delší než 4 znaky včetně.');
}

if (mysql_num_rows($herna_all)==0) {
	echo "<div class='art'><p>V herně není žádná aktivní a schválená jeskyně vyhovující nastaveným parametrům hledání.</p></div>\n";
}

$i = 0;

while ($hItem = mysql_fetch_object($herna_all)) {
$i++;
if (isSet($herna_moje[$hItem->id])) {
	if ($herna_moje[$hItem->id] == 2) {
		$hMy = " | <span class='hdnes'>Má hra</span>";
	}
	elseif ($herna_moje[$hItem->id] == 1) {
		$hMy = " | <span class='hdnes'>Má hra</span>";
	}
}
else {
	$hMy = "";
}
	$hItem->nazev				= _htmlspec(odhtml(stripslashes($hItem->nazev)));
	$hItem->popis = _htmlspec(mb_strimwidth(odhtml(stripslashes($hItem->popis)), 0, 150, "…", "UTF-8"));
	$hItem->hraci_hleda = _htmlspec(mb_strimwidth(odhtml(stripslashes($hItem->hraci_hleda)), 0, 150, "…", "UTF-8"));
	$hItem->aktivita = $time-$hItem->aktivita;

	$dayZ = date("d",$hItem->zalozeno);
	$monthZ = date("m",$hItem->zalozeno);
	$yearZ = date("Y",$hItem->zalozeno);
	$secBetween = mktime(1,1,1,$datum["mesic"],$datum["den"],$datum["rok"])-mktime(1,1,1,$monthZ,$dayZ,$yearZ);
	if ($secBetween == 0){
		$hItem->zalozeno = "<span class='hdnes'>dnes</span>";
	}
	elseif ($secBetween <= (24*60*60)) {
		$hItem->zalozeno = "<span class='hvcera'>včera</span>";
	}
	else {
		$hItem->zalozeno = date("j. n. Y", $hItem->zalozeno);
	}

	$days	= floor($hItem->aktivita / 86400);
	$hours= floor(($hItem->aktivita%(86400))/3600);
	$mins	= floor(($hItem->aktivita%3600)/60);
	$secs	= $hItem->aktivita%60;
	$hideLink = "";

	if ($LogedIn && $_SESSION['lvl'] >= 2) {
		$hideLink = "<small class='dblock'><a title='Skryje jeskyni z výpisu' href='/herna/$hItem->nazev_rew/' class='ajaxlink game-hide hcas'>skrýt</a></small> ";
	}

	$hItem->aktivita = ($days>0?$days."d ":"").($hours>0?($hours<10?"0".$hours:$hours)."h&nbsp;":"").($mins<10?"0".$mins:$mins)."m&nbsp;".($secs<10?"0".$secs:$secs)."s";
	echo "	<table class='text h".($hItem->typ==0?"drd":"orp")."' cellspacing='0' cellpadding='0'>
		<tr><td class='hvps'><h4><a href='/herna/$hItem->nazev_rew/' title='Detaily jeskyně $hItem->nazev' class='permalink2'>$hItem->nazev</a></h4></td>
		<td rowspan='3' class='her'>$hideLink<span class='hfst'>".($hItem->typ==0?"<span>D</span>rD":"<span>O</span>RP")."</span><br /><span title='Čas od poslední aktivity' class='hcas'>".$hItem->aktivita."</span></td></tr>
		<tr><td class='habout'><p>$hItem->popis</p><p class='hhleda'>$hItem->hraci_hleda</p></td></tr>
		<tr><td class='hinfo'>PJ: <a href='/uzivatele/$hItem->vlastnik_rew/' class='permalink2' title='PJ jeskyně'>$hItem->vlastnik</a>";
		if (in_array($hItem->vlastnik_rew,$usersOnline)) {
			echo " <em>(online)</em>";
		}
	echo " | <acronym title='Založeno' lang='cs'>zal.</acronym> ".$hItem->zalozeno." | ".($hItem->povolreg ? '<span class="helper" title="Je povolena registrace nových postav">&#10004;</span>' : '<span class="helper" title="Není povolena registrace nových postav">&#10008;</span>')." | Příspěvky: ".getComm($hItem->id,4,true,$hItem->unread_comms,$hItem->all_comms,$hItem->v_uid)."$hMy</td></tr>
	</table>
<hr class='hide' />\n";

	if ($i%4 == 0)
		ob_flush();
}

echo "</div>\n";

echo "	<p class='strankovani'>";
echo $pagination;
echo "	</p>\n";

?>
<script type="text/javascript">
/* <![CDATA[ */
	function game_hide_unhide(a, doUnhide) {
		var q;
		if (!doUnhide) {
			doUnhide = a.get('href').split('/').erase("").pop();
			span = new Element('em', {'text': 'skrývám...'}).inject(a, 'after');
			new Request({'url':'/ajaxing.php', 'method':'get', onSuccess: function(responseText){
				if (responseText > 0) {
					this.set('text', 'skryto');
				}
				else {
					this.set('text', 'chyba :(');
				}
			}.bind(span)}).send({'data':{'do':'game-hide', 'cave': doUnhide}});
			a.dispose();
		}
		else {
			doUnhide = a.getNext().get('href').split('/').erase("").pop();
			span = new Element('span', {'text': 'provádím...'}).inject(a, 'after');
			new Request({'url':'/ajaxing.php', 'method':'get', onSuccess: function(responseText){
				if (responseText > 0) {
					this.getParent().dispose();
				}
				else {
					this.set('text', 'chyba :(');
				}
			}.bind(span)}).send({'data':{'do':'game-unhide', 'cave': doUnhide}});
			a.dispose();
		}
	}

  document.addEvent('domready', function(){
  	$$('div.herna-vypis a.ajaxlink, #hiddenCaves a.ajaxlink').addEvent('click', function(){
  		if (this.className.indexOf('game-unhide') > -1) {
				game_hide_unhide(this, 1);
			}
			else {
				game_hide_unhide(this, 0);
			}
  		return false;
		});
	});
/* ]]> */
</script>