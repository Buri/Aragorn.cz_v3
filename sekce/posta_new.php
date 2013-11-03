<?php
if (true){
$shortVypisCnt = 5;
$display = "hide";
if (isset($_GET['to']) && $_GET['to'] != "") $display = "";
?>
<h2 class='h2-head'><a href='/<?php echo $link;?>/' title='Poštolka - Aragornská pošta verze 3.81'><?php echo $itIsApril ? 'Psaníčka': 'Poštolka';?></a></h2>
<?php

if (!$LogedIn) {
	echo "<h3>Aragornská pošta</h3>
	<div id='postolka-all'>
";
	info("Tato sekce je přístupná pouze registrovaným a přihlášeným uživatelům.");
}
else {

$userForSearch = false;
if ($slink === 'konverzace' && isset($sslink) && $sslink != '' && $sslink != $_SESSION['login_rew']) {
	$srch = addslashes(trim($sslink));
	$userForSearchS = mysql_query("SELECT id, login, login_rew, level, ico FROM 3_users WHERE login_rew = '$srch' AND id > 1");
	if ($userForSearchS && mysql_num_rows($userForSearchS) > 0) {
		$userForSearch = mysql_fetch_object($userForSearchS);
	}
}

if (!function_exists("_check_num")) {
	function _check_num($r,$x){
		$r = str_rot13($r);
		$r = base_convert($r,35,10);
		if(strlen($r)>(2*strlen($x)))return true;
		return false;
	}
}

if (!function_exists("_encode_num")) {
	function _encode_num($r,$x){
		$n = mt_rand(pow(10,strlen($x)-1),pow(10,strlen($x))-1);
		$r = base_convert($n.$r.$n,10,35);
		$r = str_rot13($r);
		return $r;
	}
}

if (!function_exists("_decode_num")) {
	function _decode_num($r,$x){
		$r = str_rot13($r);
		$r = base_convert($r,35,10);
		$r = substr($r, strlen($x), -strlen($x));
		return $r;
	}
}

if (!function_exists("_postolka_read")) {
	function _postolka_read($n){
	  global $AragornCache;
		@mysql_query("UPDATE 3_post_new SET stavto='1' WHERE stavto!='3' AND id='$n->id' AND tid='$_SESSION[uid]'");

		$AragornCache->delVal("post-unread:$_SESSION[uid]");

		if ($n->parent > 0) {
		  $parentS = mysql_query("SELECT * FROM 3_post_new WHERE id='$n->parent'");
		  if ($parentS && mysql_num_rows($parentS)>0) {
				$parent = mysql_fetch_object($parentS);
				mysql_free_result($parentS);
				if ($parent->whis != "") {
					$users = explode(",",$parent->whis);
					$pozice = array_search($_SESSION['uid'], $users);
					$whisNew = substr_replace($parent->whisstav,'1',$pozice,1);
					mysql_query("UPDATE 3_post_new SET whisstav='$whisNew' WHERE id='$n->parent'");
				}
			}
		}
	}
}

if ($userForSearch !== false) {
	$sqlWithUser = "(
SELECT p.id AS id, p.fid, p.tid, p.cas, p.mid, p.stavto, p.stavfrom, p.whis, p.whisstav, t.content AS text, t.compressed
	FROM 3_post_new AS p
	LEFT JOIN 3_post_text AS t ON t.id = p.mid
	WHERE p.fid = $_SESSION[uid] AND p.tid = $userForSearch->id AND p.stavfrom = '1'
)
UNION
(
SELECT p.id AS id, p.fid, p.tid, p.cas, p.mid, p.stavto, p.stavfrom, p.whis, p.whisstav, t.content AS text, t.compressed
	FROM 3_post_new AS p
	LEFT JOIN 3_post_text AS t ON t.id = p.mid
	WHERE p.tid = $_SESSION[uid] AND p.fid = $userForSearch->id AND p.stavto <> '3'
)
	ORDER BY 1 DESC";

	$sqlWithUserCount = "SELECT COUNT(distinct(mid)) FROM 3_post_new WHERE (fid = $_SESSION[uid] AND tid = $userForSearch->id AND stavfrom = '1') OR (tid = $_SESSION[uid] AND fid = $userForSearch->id AND (stavto = '0' OR stavto = '1'))";
//	echo "<!-- $sqlWithUser --> "; // debug
}

$sqlToMe = "SELECT p.id, u.login, u.login_rew, u.level, p.fid, p.tid, p.cas, p.mid, p.stavto, p.stavfrom, p.whis, p.whisstav, t.content AS text, t.compressed
FROM 3_post_new AS p
LEFT JOIN 3_post_text AS t ON t.id = p.mid
LEFT JOIN 3_users AS u ON u.id = p.fid
WHERE (p.stavto = '0' OR p.stavto = '1') AND p.tid = '$_SESSION[uid]'
ORDER BY p.id DESC";

$sqlToMeCount = "SELECT COUNT(*) FROM 3_post_new WHERE (stavto = '0' OR stavto = '1') AND tid = '$_SESSION[uid]'";

$sqlFromMe = "SELECT p.id, u.login, u.login_rew, u.level, p.fid, p.tid, p.cas, p.mid, p.stavto, p.stavfrom, p.whis, p.whisstav, t.content AS text, t.compressed
FROM 3_post_new AS p
LEFT JOIN 3_post_text AS t ON t.id = p.mid
LEFT JOIN 3_users AS u ON u.id = p.tid
WHERE p.stavfrom = '1' AND p.fid = '$_SESSION[uid]'
ORDER BY p.id DESC";

$sqlFromMeCount = "SELECT COUNT(*) FROM 3_post_new WHERE stavfrom = '1' AND fid = '$_SESSION[uid]'";

$new_mess_form = "
<div id='zprava' class='$display'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/$link/?akce=posta-send-new' name='txt' id='form_for_new' method='post' class='f fd' onsubmit=\"return checkForNew('posta','to',['us','mess'],false);\">
<fieldset>
<legend>Nová zpráva <a href=\"#\" onclick=\"hide('zprava');return false;\" class='permalinkb flink' title='Zavřít'>Zavřít</a></legend>
<label><span>Komu (více uživatelů oddělte čárkou)</span><input type='text' name='us' id='to' value='".((isset($_GET['to']))?_htmlspec(stripslashes($_GET['to'])):"")."' size='20' maxlength='200' /></label>";

$friendsListS = mysql_query("SELECT u.login, u.login_rew FROM 3_friends AS f, 3_users AS u WHERE u.id = f.fid AND f.uid = $_SESSION[uid] ORDER BY login ASC");
if ($friendsListS && mysql_num_rows($friendsListS)>0) {
	$new_mess_form .= "<div><a href=\"#\" onclick=\"hide('friends-list');return false;\" title=\"Zobrazit/skrýt seznam přátel\">Přátelé</a><div id='friends-list' class='hide'>\n";
	$friends = array();
	while ($friend = mysql_fetch_row($friendsListS)) {
		$friends[] = "<a href=\"/uzivatele/$friend[1]/\" onclick=\"return rep2(this)\">$friend[0]</a>";
	}
	mysql_free_result($friendsListS);
	$new_mess_form .= join(" | ", $friends);
	$new_mess_form .= "</div></div>";
}
$new_mess_form .= "<label><span>Zpráva</span><textarea rows='10' cols='40' name='mess' id='km'></textarea><span><a href='javascript: vloz_tag(\"b\")'><img src='/system/editor/bold.jpg' alt='Tučně' title='Tučně' /></a> <a href='javascript: vloz_tag(\"i\")'><img src='/system/editor/kur.jpg' alt='Kurzívou' title='Kurzívou' /></a> <a href='javascript: vloz_tag(\"u\")' title='Podtrhnout'><img src='/system/editor/und.jpg' alt='Podtrhnout' /></a> <a href='javascript: editor(4)' title='Odkaz'><img alt='Odkaz' src='/system/editor/link.jpg' /></a> <a href='javascript: editor(5)' title='Obrázek'><img alt='Obrázek' src='/system/editor/pict.jpg' /></a> <a href='javascript: vloz_tag(\"color1\")' class='hlight1'>Barva 1</a> <a href='javascript: vloz_tag(\"color2\")' class='hlight2'>Barva 2</a> <a href='javascript: vloz_tag(\"color3\")' class='hlight3'>Barva 3</a></span></label>
<input class='button' type='button' onclick='do_previewP(\"km\");return false;' value='Náhled zprávy' /><br /><br /><input class='button' type='submit' id='btn_odeslat_zpravu' value='Odeslat zprávu' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>\n";

/*
if ($slink == "all" || $slink == "vse" || $slink == "inbox") {
	// vypis vsech zprav - narocne na DB !!! (beta)
}
else
*/
if ($slink == "prijata" || $slink == "prijate" || $slink == "in" || $slink == "prichozi") {
	// prijata posta - vypis
	$do = "in";
	$h3 = "Přijatá pošta" . aprilovyZertik($lastRandomNumber);
	$h3Link = $slink."/";
	echo "	<h3><a href='/$link/$h3Link' title='Poštolka - $h3'>$h3</a></h3>
	<p class='submenu'><a href=\"#\" onclick=\"hide('zprava');return false;\" class='permalink' title='Nová zpráva'>Nová zpráva</a><span class='hide'> | </span><span class='hide'> | </span><a href='/$link/' class='permalink' title='Na výpis složek'>Hnízdo</a><span class='hide'> | </span><a href='/$link/out/' class='permalink' title='Odchozí a odeslané zprávy, směr out'>Odeslané</a><span class='hide'> | </span><a href=\"javascript: post_del()\" class='permalink' title='Smazat označené'>Smazat označené</a></p>
	<div id='postolka-all'>
";

	echo $new_mess_form;

	if ($sslink != "" && ctype_alnum($sslink)) {
	  if (_check_num($sslink,$_SESSION['uid'])) {
		  $num = addslashes(_decode_num($sslink,$_SESSION['uid']));
		  $messS = mysql_query("SELECT p.*,t.content AS text,t.compressed,u.login,u.login_rew,u.level,u.ico FROM 3_post_new AS p LEFT JOIN 3_post_text AS t ON t.id = p.mid LEFT JOIN 3_users AS u ON u.id = p.fid WHERE p.id='$num' AND p.tid='$_SESSION[uid]' AND (p.stavto='0' OR p.stavto='1')");
		  if ($messS && mysql_num_rows($messS)>0) {
		    $pT = mysql_fetch_object($messS);
				if ($pT->compressed) $pT->text = gzuncompress($pT->text);
		    mysql_free_result($messS);
			  $rep = "";
			  $ico = "<img src='http://s1.aragorn.cz/i/$pT->ico' />";
			  $normalKomu = "<span".sl($pT->level, 2).">";
			  if ($pT->fid > 1) {
			    $ico = "<a href='/uzivatele/$pT->login_rew/' title='Profil uživatele'>".$ico."</a>";
				  $rep = "- <a class=\"rl\" href=\"#\" onclick=\"return rep('"._htmlspec($pT->login)."')\" title='Odepsat'>RE</a>";
				  $normalKomu .= "<a href='/uzivatele/$pT->login_rew/' title='Profil uživatele "._htmlspec($pT->login)."'>".mb_strimwidth($pT->login, 0, 40, "…")."</a>";
				}
				else {
				  $normalKomu .= mb_strimwidth($pT->login, 0, 40, "…");
				}
			  $normalKomu .= "</span> uživateli ";
?>
<div id="dis-module-x">
	<table class='commtb' cellspacing='0' cellpadding='0'>
<?php
	$doRow = false;
	$inRow = array(-1=>"<a onclick='javascript:history.go(-1);return false;' href='#'>Zpět</a>","<del>Starší &darr;</del>","<del>&uarr; Novější</del>");
	$previousPostS = mysql_query("SELECT id FROM 3_post_new WHERE id < '$num' AND tid = '$_SESSION[uid]' AND (stavto = '0' OR stavto = '1') ORDER BY id DESC LIMIT 1");
	if ($previousPostS && mysql_num_rows($previousPostS)>0){
		$previousPost = mysql_fetch_row($previousPostS);
		mysql_free_result($previousPostS);
		$inRow[0] = "<a href='/posta/in/"._encode_num($previousPost[0],$_SESSION['uid'])."/' title='Zobrazit starší příchozí poštolku'>Starší &darr;</a>";
		$doRow = true;
	}
	$nextPostS = mysql_query("SELECT id FROM 3_post_new WHERE id > '$num' AND tid = '$_SESSION[uid]' AND (stavto = '0' OR stavto = '1') ORDER BY id ASC LIMIT 1");
	if ($nextPostS && mysql_num_rows($nextPostS)>0){
		$nextPost = mysql_fetch_row($nextPostS);
		mysql_free_result($nextPostS);	
		$inRow[1] = "<a href='/posta/in/"._encode_num($nextPost[0],$_SESSION['uid'])."/' title='Zobrazit novější příchozí poštolku'>&uarr; Novější</a>";
		$doRow = true;
	}
	if ($doRow){
?>
		<tr><td class="c1 t-a-c" colspan="2"><b><?php echo join(" | ",$inRow);?></b></td></tr>
<?php
	}
?>
		<tr><td class='c1' colspan='2' ><?php echo $normalKomu;?> <span<?php echo sl($_SESSION['lvl'],2);?>><a href='/uzivatele/<?php echo $_SESSION['login_rew'];?>/'><?php echo $_SESSION['login'];?></a></span> <?php echo $rep." - ".sdh($pT->cas);?><input type='checkbox' name='d[]' value='<?php echo $pT->id; ?>' /> <em class="ar" href="#" onclick="hide('<?php echo "h_$sslink";?>');return false;" title='Schovat'></em></td></tr>
		<tr id='h_<?php echo $sslink;?>'><td class='c2'><?php echo $ico;?></td>
			<td class='c3'>
			<p class='c4'>
<?php echo spit($pT->text, 1)."\n";
?>
			</p>
			</td>
		</tr>
	</table>
<?php
			if ($pT->stavto == '0') {
				_postolka_read($pT);
			}
echo "</div>\n";
			}
		}
	}

	if (!isSet($_GET['index'])){
		$index = 1;
	}else{
		$index = (int)$_GET['index'];
	}
	$from = ($index - 1) * $postPC; //od kolikate polozky zobrazit

	$aCS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_post_new WHERE tid=$_SESSION[uid] AND (stavto='0' OR stavto='1')"));
	$aC = $aCS[0];

	echo "<p class='strankovani'>".make_pages($aC, $postPC, $index)."</p>\n";

	$messToMeS = mysql_query($sqlToMe." LIMIT $from, $postPC");
	$messToMeC = mysql_num_rows($messToMeS);
echo "
<div id='vypis-all'>
<div class='highlight-top'></div>
<div class='highlight-mid'>
	<div class='diskuze-okruh'><a href=\"#\" onclick=\"hide('vypis-out');return false;\" class='diskuze-toggle' title='Zobrazit / skrýt'>(x)</a> <span class='permalinkb'>Přijaté (".($messToMeC>0?((($index-1)*$postPC)+1):0).":".($messToMeC>0?((($index-1)*$postPC)+$messToMeC):0)." / $aC celkem)</span></div>
	<div id='vypis-out'>\n";
	if ($messToMeC<1) {
		echo "		<div class='oblast-popis'>Žádné odchozí zprávy.</div>";
	}
	else {
			echo "		<div class='diskuze-vypis'>
			<table border='0' class='post-tbl' cellpadding='0' cellspacing='0'>\n";

			$addIndexUrl = "";
			if ($index>1) $addIndexUrl = "?index=".$index;

			echo "				<tr><td>&nbsp;</td><td><a href='#' onclick='zaskrtnout(\"vypis-out\");return false;' class='dblock' title='Nevybrané zaškrtné, vybrané odškrtne'>Označit opačně</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
		  while($zprava = mysql_fetch_object($messToMeS)){
				$rep = "";
				if ($zprava->fid > 1) {
				  $rep = "<a href=\"#\" onclick=\"return rep('"._htmlspec($zprava->login)."')\" title='Odepsat'>RE</a>";
				  $zprava->login = "<a href='/$link/konverzace/$zprava->login_rew/' title='Konverzace s "._htmlspec($zprava->login)."'>".$zprava->login."</a>";
				}
				if ($zprava->compressed) $zprava->text = gzuncompress($zprava->text);
				echo "				<tr><td class='pr-$zprava->stavto'>&nbsp;</td><td><span".sl($zprava->level,1).">$zprava->login</span>: <a title='Přečíst zprávu' class='r permalink2' href='/$link/in/"._encode_num($zprava->id,$_SESSION['uid'])."/$addIndexUrl'> ".mb_strimwidth(strip_tags($zprava->text), 0, 20, "…")."</a></td><td>$rep</td><td>".date("H:i:s",$zprava->cas)."</td><td>".date("j.n.",$zprava->cas)."<input type='checkbox' name='d[]' value='$zprava->id' /></td></tr>\n";
			}
			mysql_free_result($messToMeS);
			echo "				<tr><td>&nbsp;</td><td><a href='#' onclick='zaskrtnout(\"vypis-out\");return false;' class='dblock' title='Nevybrané zaškrtné, vybrané odškrtne'>Označit opačně</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

		echo "			</table>
		</div>\n";
	}
	echo "	</div>\n</div>\n<div class='highlight-bot'></div>\n</div>\n";
	echo "<p class='strankovani'>".make_pages($aC, $postPC, $index)."</p>\n";
}
elseif ($slink == "odeslane" || $slink == "odeslana" || $slink == "odchozi" || $slink == "out") {
	// odeslana posta - vypis
	$do = "out";
	$h3Link = $slink."/";
	$h3 = "Odeslaná pošta" . aprilovyZertik($lastRandomNumber);
	echo "	<h3><a href='/$link/$h3Link' title='Poštolka - $h3'>$h3</a></h3>
	<p class='submenu'><a href=\"#\" onclick=\"hide('zprava');return false;\" class='permalink' title='Nová zpráva'>Nová zpráva</a><span class='hide'> | </span><span class='hide'> | </span><a href='/$link/in/' class='permalink' title='Příchozí a přijaté zprávy, směr in'>Přijaté</a><span class='hide'> | </span><a href='/$link/' class='permalink' title='Na výpis složek'>Hnízdo</a><span class='hide'> | </span><a href=\"javascript: post_del()\" class='permalink' title='Smazat označené'>Smazat označené</a></p>
	<div id='postolka-all'>
";

	echo $new_mess_form;

	if ($sslink != "" && ctype_alnum($sslink)) {
	  if (_check_num($sslink,$_SESSION['uid'])) {
		  $num = addslashes(_decode_num($sslink,$_SESSION['uid']));
		  $messS = mysql_query("SELECT p.*,t.content AS text,t.compressed,u.login,u.login_rew,u.level,u.ico FROM 3_post_new AS p LEFT JOIN 3_post_text AS t ON t.id = p.mid LEFT JOIN 3_users AS u ON p.tid = u.id WHERE p.id='$num' AND p.fid='$_SESSION[uid]' AND p.stavfrom='1'");
		  if (mysql_num_rows($messS)>0) {
		    $pT = mysql_fetch_object($messS);
				if ($pT->compressed) $pT->text = gzuncompress($pT->text);
		    mysql_free_result($messS);
				$varR = "";
				if ($pT->tid > 1) {
			    switch ($pT->stavto) {
				    case "0":
				      $varR = "<span>nepřečteno</span>";
				    break;
				    case "1":
				      $varR = "<span>přečteno</span>";
				    break;
				    case "2":
				    case "3":
				      $varR = "<span>smazáno</span>";
				    break;
				    default:
				      $varR = "";
				    break;
					}
					$varR = "<br />".$varR;
				  $cspt = "";
				  $rep = "<a class=\"rl\" href=\"#\" onclick=\"return rep('"._htmlspec($pT->login)."')\" title='Napsat'>RE</a>";
				  $ico = "<a href='/uzivatele/$pT->login_rew/' title='Profil uživatele'><img src='http://s1.aragorn.cz/i/$pT->ico' alt='Profil uživatele' title='Profil uživatele' /></a>";
				  $normalKomu = " uživateli <span".sl($pT->level, 2)."><a href='/uzivatele/$pT->login_rew/' title='Profil uživatele "._htmlspec($pT->login)."'>".mb_strimwidth($pT->login, 0, 40, "…")."</a></span>";
				}
				else {
				  $cspt = $ico = $normalKomu = "";
				  $usersS = mysql_query("SELECT login,login_rew,level,id FROM 3_users WHERE id IN ($pT->whis) ORDER BY login_rew ASC");
				  $userLogins = $longLogins = array();
				  $cc=0;
				  $userIds = explode(",",$pT->whis);
				  while ($loginU = mysql_fetch_row($usersS)){
				    $longLogins[] = $loginU[0];
				    $userLogins[] = "<span class='pr-".substr($pT->whisstav,array_search($loginU[3],$userIds),1)."'></span><span".sl($loginU[2], 2)."><a href='/uzivatele/$loginU[1]/' title='"._htmlspec($loginU[0])."'>".mb_strimwidth($loginU[0], 0, 16, "…")."</a></span>";
				    $cc++;
					}
					mysql_free_result($usersS);
					$rep = "<a class=\"rl\" href=\"#\" onclick=\"return rep('"._htmlspec(join(",",$longLogins))."')\" title='Napsat všem'>RE</a>";
					$cspt = "		<tr><td class='c1' colspan='2'><h4>".join(" ",$userLogins)."</h4></td></tr>\n";
				}
?>
<div id="dis-module-x">
	<table class='commtb' cellpadding='0' cellspacing='0'>
<?php
	$doRow = false;
	$inRow = array(-1=>"<a onclick='javascript:history.go(-1);return false;' href='#'>Zpět</a>","<del>Starší &darr;</del>","<del>&uarr; Novější</del>");
	$previousPostS = mysql_query("SELECT id FROM 3_post_new WHERE id < '$num' AND fid = '$_SESSION[uid]' AND stavfrom = '1' ORDER BY id DESC LIMIT 1");
	if ($previousPostS && mysql_num_rows($previousPostS)>0){
		$previousPost = mysql_fetch_row($previousPostS);
		$inRow[0] = "<a href='/posta/out/"._encode_num($previousPost[0],$_SESSION['uid'])."/' title='Zobrazit starší odchozí poštolku'>Starší &darr;</a>";
		$doRow = true;
		mysql_free_result($previousPostS);
	}
	$nextPostS = mysql_query("SELECT id FROM 3_post_new WHERE id > '$num' AND fid = '$_SESSION[uid]' AND stavfrom = '1' ORDER BY id ASC LIMIT 1");
	if ($nextPostS && mysql_num_rows($nextPostS)>0){
		$nextPost = mysql_fetch_row($nextPostS);
		$inRow[1] = "<a href='/posta/out/"._encode_num($nextPost[0],$_SESSION['uid'])."/' title='Zobrazit novější odchozí poštolku'>&uarr; Novější</a>";
		$doRow = true;
		mysql_free_result($nextPostS);	
	}
	if ($doRow){
?>
		<tr><td class="c1 t-a-c" colspan="2"><?php echo join(" | ",$inRow);?></td></tr>
<?php
	}
?>
		<tr><td class='c1' colspan='2'><span<?php echo sl($_SESSION['lvl'],2);?>><a href='/uzivatele/<?php echo $_SESSION['login_rew'];?>/'><?php echo $_SESSION['login'];?></a></span><?php echo $normalKomu;?> - <?php echo $rep." - ".sdh($pT->cas);?><input type='checkbox' name='d[]' value='<?php echo $pT->id; ?>' /> <em href="#" class="ar" onclick="hide('<?php echo "h$sslink";?>');return false;" title='Schovat'></em></td></tr>
<?php echo $cspt;?>
		<tr id='h<?php echo $sslink;?>'><td class='c2'><?php echo $ico.$varR; ?></td>
			<td class='c3'>
			<p class='c4'>
<?php echo spit($pT->text, 1)."\n";
?>
			</p>
			</td>
		</tr>
	</table>
</div>
<?php
			}
		}
	}

	if (!isSet($_GET['index'])){
		$index = 1;
	}else{
		$index = (int)$_GET['index'];
	}
	$from = ($index - 1) * $postPC; //od kolikate polozky zobrazit

	$aCS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_post_new WHERE fid=$_SESSION[uid] AND stavfrom='1'"));
	$aC = $aCS[0];

?>
<p class='strankovani'><?php echo make_pages($aC, $postPC, $index); ?></p>
<?php
		$messFromMeS = mysql_query($sqlFromMe." LIMIT $from, $postPC");
		$messFromMeC = mysql_num_rows($messFromMeS);
echo "
<div id='vypis-all'>
<div class='highlight-top'></div>
<div class='highlight-mid'>
	<div class='diskuze-okruh'><a href=\"#\" onclick=\"hide('vypis-in');return false;\" class='diskuze-toggle' title='Zobrazit / skrýt'>(x)</a> <span class='permalinkb'>Odeslané (".($messFromMeC>0?((($index-1)*$postPC)+1):0).":".($messFromMeC>0?((($index-1)*$postPC)+$messFromMeC):0)." / $aC celkem)</span></div>
	<div id='vypis-in'>\n";
		if ($messFromMeC<1) {
			echo "		<div class='oblast-popis'>Žádné odchozí zprávy.</div>";
		}
		else {
			echo "		<div class='diskuze-vypis'>\n";
			$txt = array();
			$users = array();
		  while($zprava = mysql_fetch_object($messFromMeS)){
		    $txtOne = array();

				$txtOne['id'] = $zprava->id;
				$addLinker = "";
				if($index>1){
				  $addLinker = "?index=$index";
				}
				$txtOne['link'] = _encode_num($zprava->id,$_SESSION['uid'])."/$addLinker";
				$txtOne['tid'] = $zprava->tid;
				$txtOne['stavto'] = $zprava->stavto;
				if ($zprava->compressed) $zprava->text = gzuncompress($zprava->text);
				$txtOne['text'] = strip_tags($zprava->text);
				$txtOne['cas'] = date("H:i:s",$zprava->cas);
				$txtOne['datum'] = date("j.n.",$zprava->cas);
				$txtOne['level'] = $zprava->level;

				if ($zprava->tid > 1) {
				  $txtOne['rep'] = "<a href=\"#\" onclick=\"return rep('"._htmlspec($zprava->login)."')\" title='Napsat'>RE</a>";
				  $txtOne['login'] = $zprava->login;
				  $txtOne['login_rew'] = $zprava->login_rew;
				}
				elseif ($zprava->whis != "") {
				  $txtOne['rep'] = "";
				  $txtOne['whis'] = $zprava->whis;
				  $users = array_merge($users,explode(",",$zprava->whis));
				  $txtOne['whisstav'] = $zprava->whisstav;
				}
				$txt[] = $txtOne;
			}
			mysql_free_result($messFromMeS);
			if (count($users)>0) {
			  $users = array_unique($users);
			  $uidsS = mysql_query("SELECT id,login,level FROM 3_users WHERE id IN (".join(",",$users).") ORDER BY login_rew ASC");
		    $logins = array();
			  if (mysql_num_rows($uidsS)>0) {
					while($userItem = mysql_fetch_row($uidsS)) {
					  $logins[$userItem[0]] = "<span".sl($userItem[2],1).">".htmlspecialchars(mb_strimwidth($userItem[1],0,20,"…"),ENT_COMPAT,"UTF-8")."</span>";
					}
					mysql_free_result($uidsS);
				}
			}

			echo "			<table border='0' class='post-tbl' cellpadding='0' cellspacing='0'>\n";

			echo "				<tr><td>&nbsp;</td><td><a href='#' onclick='zaskrtnout(\"vypis-in\");return false;' class='dblock' title='Nevybrané zaškrtné, vybrané odškrtne'>Označit opačně</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
			foreach($txt as $mess){
			  if ($mess['tid'] > 1) {
			    $mess['text'] = str_replace(array("\n","\r"), " ", $mess['text']);
					echo "				<tr><td class='pr-$mess[stavto]'>&nbsp;</td><td><span".sl($mess['level'],1)."><a href='/$link/konverzace/$mess[login_rew]/' title='Konverzace s "._htmlspec($mess['login'])."'>".$mess['login']."</a></span>: <a title='Zobrazit zprávu' class='r permalink2' href='/$link/out/$mess[link]'> ".mb_strimwidth($mess['text'], 0, 20, "…")."</a></td><td>$mess[rep]</td><td>$mess[cas]</td><td>$mess[datum]<input type='checkbox' name='d[]' value='$mess[id]' /></td></tr>\n";
				}
				else {
					$usersHere = explode(",",$mess['whis']);
					$stavHere = "";
					if ($mess['whisstav'] == str_pad("",count($usersHere),"0")) {
						$stavHere = " class='pr-0'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"1")) {
						$stavHere = " class='pr-1'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"2")) {
						$stavHere = " class='pr-3'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"3")) {
						$stavHere = " class='pr-3'";
					}
					echo "				<tr><td$stavHere>&nbsp;</td><td>";
					$loginsHere = array();
					for($d=0;$d<count($usersHere);$d++) {
					  switch(substr($mess['whisstav'],$d,1)){
					    case "0":
					      $sAdd = "(nepřečteno)";
					    break;
					    case "1":
					      $sAdd = "(přečteno)";
					    break;
					    case "2":
					    case "3":
					      $sAdd = "(smazáno)";
					    break;
					    default:
					      $sAdd = "";
					    break;
						}
					  $loginsHere[] = "<span class='pr-".substr($mess['whisstav'],$d,1)."'> </span>".$logins[$usersHere[$d]]." $sAdd";
					}
					$mess['rep'] = "<a href=\"#\" onclick=\"return rep('".addslashes(strip_tags(join(",",$logins)))."');\" title='Napsat všem'>RE</a>";
					echo "<a href='/$link/out/$mess[link]' onmouseover=\"ddrivetip('Hromadná zpráva - zobrazit"._htmlspec("<br />".addslashes(join("<br />",$loginsHere)))."')\" onmouseout='hidedrivetip();'>".$logins[$usersHere[0]].", ".$logins[$usersHere[1]].((count($usersHere)>2)?", ... :":" : ")."</a> <a title='Zobrazit zprávu' class='r permalink2' href='/$link/out/$mess[link]'> více &raquo;</a></td><td>$mess[rep]</td><td>$mess[cas]</td><td>$mess[datum]<input type='checkbox' name='d[]' value='$mess[id]' /></td></tr>\n";
				}
			}
			echo "				<tr><td>&nbsp;</td><td><a href='#' onclick='zaskrtnout(\"vypis-in\");return false;' class='dblock' title='Nevybrané zaškrtné, vybrané odškrtne'>Označit opačně</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

			echo "			</table>
		</div>\n";
		}
		echo "	</div>
</div>
<div class='highlight-bot'></div>
</div>\n";
	echo "<p class='strankovani'>".make_pages($aC, $postPC, $index)."</p>\n";

}
elseif ($userForSearch !== false && $slink == 'konverzace' && $sslink != '' && $sslink != $_SESSION['login_rew']) {
	$do = "konverzace";
	$h3 = "Konverzace ~ $userForSearch->login" . aprilovyZertik($lastRandomNumber);
	$h3Link = "";
	echo "	<h3><a href='/$link/$slink/$sslink/' title='Poštolka - $h3'>$h3</a></h3>
	<p class='submenu'><a href=\"#\" onclick=\"hide('zprava');return false;\" class='permalink' title='Nová zpráva'>Nová zpráva</a><span class='hide'> | </span><a href='/$link/in/' class='permalink' title='Příchozí a přijaté zprávy, směr in'>Přijaté</a><span class='hide'> | </span><a href='/$link/out/' class='permalink' title='Odchozí a odeslané zprávy, směr out'>Odeslané</a><span class='hide'> | </span><a href=\"javascript: post_del()\" class='permalink' title='Smazat označené'>Smazat označené</a></p>
	<div id='postolka-all'>
";

echo $new_mess_form;

	if (isset($_GET['p']) && $_GET['p'] != '' && ctype_alnum($_GET['p'])) {
	  if (_check_num($_GET['p'],$_SESSION['uid'])) {
		  $num = addslashes(_decode_num($_GET['p'],$_SESSION['uid']));
		  $messS = mysql_query("SELECT p.*,t.content AS text,t.compressed FROM 3_post_new AS p LEFT JOIN 3_post_text AS t ON t.id = p.mid WHERE p.id='$num' AND ((p.fid = $_SESSION[uid] AND p.stavfrom <> '3') OR (p.tid = $_SESSION[uid] AND p.stavto <> '3'))");
		  $normalKomu = "";
		  if (mysql_num_rows($messS)>0) {
		    $pT = mysql_fetch_object($messS);
				if ($pT->compressed) $pT->text = gzuncompress($pT->text);
		    mysql_free_result($messS);
				$varR = "";
			  $rep = "<a class=\"rl\" href=\"#\" onclick=\"return rep('"._htmlspec($userForSearch->login)."')\" title='Napsat'>RE</a>";
				if ($pT->fid != $_SESSION['uid']) { // prijato SESSION
		      $varR = "<span>přečteno</span>";
				  $ico = "<a href='/uzivatele/$userForSearch->login_rew/' title='Profil uživatele'><img src='http://s1.aragorn.cz/i/$userForSearch->ico' alt='Profil uživatele' title='Profil uživatele' /></a>";
				  $normalKomu = " uživateli <span".sl($_SESSION['lvl'], 2)."><a href='/uzivatele/$_SESSION[login_rew]/' title='Profil uživatele "._htmlspec($_SESSION['login'])."'>".mb_strimwidth($_SESSION['login'], 0, 40, "…")."</a></span>";
				}
				else { // odeslano SESSION
					if ($pT->tid > 1) {
				    switch ($pT->stavto) {
					    case "0":
					      $varR = "<span>nepřečteno</span>";
					    break;
					    case "1":
					      $varR = "<span>přečteno</span>";
					    break;
					    case "2":
					    case "3":
					      $varR = "<span>smazáno</span>";
					    break;
					    default:
					      $varR = "";
					    break;
						}
						$varR = "<br />".$varR;
					  $cspt = "";
					  $ico = "<a href='/uzivatele/$userForSearch->login_rew/' title='Profil uživatele'><img src='http://s1.aragorn.cz/i/$userForSearch->ico' alt='Profil uživatele' title='Profil uživatele' /></a>";
					  if ($pT->tid == $_SESSION['uid']) {
						  $normalKomu = " uživateli <span".sl($_SESSION['lvl'], 2)."><a href='/uzivatele/$_SESSION[login_rew]/' title='Profil uživatele "._htmlspec($_SESSION['login'])."'>".mb_strimwidth($_SESSION['login'], 0, 40, "…")."</a></span>";
						}
						else {
						  $normalKomu = " uživateli <span".sl($userForSearch->level, 2)."><a href='/uzivatele/$userForSearch->login_rew/' title='Profil uživatele "._htmlspec($userForSearch->login)."'>".mb_strimwidth($userForSearch->login, 0, 40, "…")."</a></span>";
						}
					}
					else {
					  $cspt = $ico = $normalKomu = "";
					  $usersS = mysql_query("SELECT login,login_rew,level,id FROM 3_users WHERE id IN ($pT->whis) ORDER BY login_rew ASC");
					  $userLogins = $longLogins = array();
					  $cc=0;
					  $userIds = explode(",",$pT->whis);
					  while ($loginU = mysql_fetch_row($usersS)){
					    $longLogins[] = $loginU[0];
					    $userLogins[] = "<span class='pr-".substr($pT->whisstav,array_search($loginU[3],$userIds),1)."'></span><span".sl($loginU[2], 2)."><a href='/uzivatele/$loginU[1]/' title='"._htmlspec($loginU[0])."'>".mb_strimwidth($loginU[0], 0, 16, "…")."</a></span>";
					    $cc++;
						}
						mysql_free_result($usersS);
						$rep = "<a class=\"rl\" href=\"#\" onclick=\"return rep('"._htmlspec(join(",",$longLogins))."')\" title='Napsat všem'>RE</a>";
						$cspt = "		<tr><td class='c1' colspan='2'><h4>".join(" ",$userLogins)."</h4></td></tr>\n";
					}
				}

				if ($pT->fid == $_SESSION['uid']) {
					$normalFrom = "<span".sl($_SESSION['lvl'],2)."><a href='/uzivatele/$_SESSION[login_rew]/'>$_SESSION[login]</a></span>";
				}
				else {
					$normalFrom = "<span".sl($userForSearch->level,2)."><a href='/uzivatele/$userForSearch->login_rew/'>$userForSearch->login</a></span>";
				}

				$normalKomu = $normalFrom.$normalKomu;
?>
<div id="dis-module-x">
	<table class='commtb' cellpadding='0' cellspacing='0'>
		<tr><td class='c1' colspan='2'><?php echo $normalKomu;?> - <?php echo $rep." - ".sdh($pT->cas);?><input type='checkbox' name='d[]' value='<?php echo $pT->id; ?>' /> <em href="#" class="ar" onclick="hide('<?php echo "h$_GET[p]";?>');return false;" title='Schovat'></em></td></tr>
<?php echo $cspt;?>
		<tr id='h<?php echo $_GET['p'];?>'><td class='c2'><?php echo $ico.$varR; ?></td>
			<td class='c3'>
			<p class='c4'>
<?php echo spit($pT->text, 1)."\n";
?>
			</p>
			</td>
		</tr>
	</table>
</div>
<?php
				if ($pT->stavto == '0' && $pT->tid == $_SESSION['uid']) {
					_postolka_read($pT);
				}
			}
		}
	}

if (!isSet($_GET['index'])){
	$index = 1;
}else{
	$index = (int)$_GET['index'];
}
$from = ($index - 1) * $postPC; //od kolikate polozky zobrazit

$messWithUserC = mysql_fetch_row(mysql_query($sqlWithUserCount));
$messWithUserC = $messWithUserC[0];

if (isSet($_GET['error'])){
	switch ($_GET['error']){
		case 1:
		  $error = "Nebyl vyplněn příjemce zprávy.";
		break;
		case 2:
		  $error = "Nebyla zadána zpráva.";
		break;
		case 3:
		  $error = "Příjemce(i) neexistuje.";
		break;
	}
	info($error);
}elseif (isSet($_GET['ok'])){
	switch ($_GET['ok']){
		case 1:
		  $ok = "Zpráva v pořádku odeslána.";
		break;
		case 2:
		  $ok = "Vybrané zprávy smazány.";
		break;
		case 3:
		  $ok = "Pošta vyprázdněna.";
		break;
	}
	ok($ok);
}

echo "<div id='vypis-all'>\n";

$strankovani = "<p class='strankovani'>".make_pages($messWithUserC, $postPC, $index)."</p>\n";
echo $strankovani;

echo "<div class='highlight-top'></div>
<div class='highlight-mid'>
	<div class='diskuze-okruh'><span class='permalinkb'>Komunikace: $userForSearch->login &#8644; $_SESSION[login]</span></div>
	<div id='vypis-out'>\n";
		if ($messWithUserC < 1) {
			echo "		<div class='oblast-popis'>Žádné zprávy v konverzaci ...</div>";
		}
		else {
		  $c = 0;
			echo "		<div class='diskuze-vypis'>\n";
			$txt = array();
			$users = array();

			$messWithUserS = mysql_query($sqlWithUser." LIMIT $from, $postPC");

		  while($zprava = mysql_fetch_object($messWithUserS)){
		    $txtOne = array();

				$txtOne['id'] = $zprava->id;
				$txtOne['login'] = $userForSearch->login;
				$txtOne['login_rew'] = $userForSearch->login_rew;
				$txtOne['tid'] = $zprava->tid;
				$txtOne['fid'] = $zprava->fid;
				$txtOne['link'] = $slink."/".$sslink."/?p="._encode_num($zprava->id, $_SESSION['uid'])."&amp;index=$index";

				if ($zprava->tid == $_SESSION['uid']) { // zprava pro SESSION / dorucena hromadna
					$txtOne['level'] = $userForSearch->level;
					$txtOne['f_login'] = $_SESSION['login'];
					$txtOne['f_login_rew'] = $_SESSION['login_rew'];
					$txtOne['t_login'] = $userForSearch->login;
					$txtOne['t_login_rew'] = $userForSearch->login_rew;
					$txtOne['stav'] = $zprava->stavto;
				}
				else { // zprava smerovana jinam, nez je SESSION / hromadna ven
					$txtOne['level'] = $_SESSION['lvl'];
					$txtOne['t_login'] = $_SESSION['login'];
					$txtOne['t_login_rew'] = $_SESSION['login_rew'];
					$txtOne['f_login'] = $userForSearch->login;
					$txtOne['f_login_rew'] = $userForSearch->login_rew;
					$txtOne['stav'] = $zprava->stavto;
				}
				if ($zprava->compressed) $zprava->text = gzuncompress($zprava->text);
				$txtOne['text'] = mb_strimwidth(strip_tags($zprava->text), 0, 20, "…");
				$txtOne['cas'] = date("H:i:s",$zprava->cas);
				$txtOne['datum'] = date("j.n.",$zprava->cas);

			  $txtOne['rep'] = "<a href=\"#\" onclick=\"return rep('"._htmlspec($userForSearch->login)."')\" title='Napsat'>RE</a>";
				if ($zprava->tid > 1) {
				}
				elseif ($zprava->whis != "") {
				  $txtOne['rep'] = "";
				  $txtOne['whis'] = $zprava->whis;
				  $users = array_merge($users,explode(",",$zprava->whis));
				  $txtOne['whisstav'] = $zprava->whisstav;
				}

				$txt[] = $txtOne; // add message to board
		    $c++;
			}
			mysql_free_result($messWithUserS);
			if (count($users) > 0) {
			  $users = array_unique($users);
			  $uidsS = mysql_query("SELECT id,login,level FROM 3_users WHERE id IN (".join(",",$users).") ORDER BY login_rew ASC");
		    $logins = array();
			  if (mysql_num_rows($uidsS)>0) {
					while($userItem = mysql_fetch_row($uidsS)) {
					  $logins[$userItem[0]] = "<span".sl($userItem[2],1).">".htmlspecialchars($userItem[1],ENT_COMPAT,"UTF-8")."</span>";
					}
					mysql_free_result($uidsS);
				}
			}

			echo "			<table border='0' class='post-tbl' cellpadding='0' cellspacing='0'>\n";

			echo "				<tr><td>&nbsp;</td><td><a href='#' onclick='zaskrtnout(\"vypis-out\");return false;' class='dblock' title='Nevybrané zaškrtné, vybrané odškrtne'>Označit opačně</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

			foreach($txt as $mess){
			  if ($mess['tid'] > 1) {
			    $mess['text'] = str_replace(array("\n","\r"), " ", $mess['text']);
					echo "				<tr><td class='pr-$mess[stav]'>&nbsp;</td><td><span".sl($mess['level'],1)."><a href='/uzivatele/$mess[t_login_rew]/' title='"._htmlspec($mess['t_login'])." - profil'>".$mess['t_login']."</a></span>: <a title='Zobrazit zprávu' class='r permalink2' href='/$link/$mess[link]'> ".$mess['text']."</a></td><td>$mess[rep]</td><td>$mess[cas]</td><td>$mess[datum]<input type='checkbox' name='d[]' value='$mess[id]' /></td></tr>\n";
				}
				else {
					$usersHere = explode(",",$mess['whis']);
					$stavHere = "";
					if ($mess['whisstav'] == str_pad("",count($usersHere),"0")) {
						$stavHere = " class='pr-0'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"1")) {
						$stavHere = " class='pr-1'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"2")) {
						$stavHere = " class='pr-3'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"3")) {
						$stavHere = " class='pr-3'";
					}
					echo "				<tr><td$stavHere></td><td>";
					$loginsHere = array();
					for($d=0;$d<count($usersHere);$d++) {
					  switch(substr($mess['whisstav'],$d,1)){
					    case "0":
					      $sAdd = "(nepřečteno)";
					    break;
					    case "1":
					      $sAdd = "(přečteno)";
					    break;
					    case "2":
					    case "3":
					      $sAdd = "(smazáno)";
					    break;
					    default:
					      $sAdd = "";
					    break;
						}
					  $loginsHere[] = "<span class='pr-".substr($mess['whisstav'],$d,1)."'></span>".$logins[$usersHere[$d]]." $sAdd";
					}
					$mess['rep'] = "<a href=\"#\" onclick=\"return rep('".strip_tags(join(",",$logins))."');\" title='Napsat všem'>RE</a>";
					echo "<a href='/$link/$mess[link]' onmouseover=\"ddrivetip('Hromadná zpráva - zobrazit"._htmlspec("<br />".addslashes(join("<br />",$loginsHere)))."')\" onmouseout='hidedrivetip();'>".$logins[$usersHere[0]].", ".$logins[$usersHere[1]].((count($usersHere)>2)?", ... :":" : ")."</a> <a title='Zobrazit zprávu' class='r permalink2' href='/$link/$mess[link]'> více &raquo;</a></td><td>$mess[rep]</td><td>$mess[cas]</td><td>$mess[datum]<input type='checkbox' name='d[]' value='$mess[id]' /></td></tr>\n";
				}
			}

			echo "				<tr><td>&nbsp;</td><td><a href='#' onclick='zaskrtnout(\"vypis-out\");return false;' class='dblock' title='Nevybrané zaškrtné, vybrané odškrtne'>Označit opačně</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

			echo "			</table>
		</div>\n";
		}
		echo "	</div>
</div>
<div class='highlight-bot'></div>\n";

		echo $strankovani;
		echo "</div>"; // vypis-all
	echo "";
}
else {
	// vypis slozek
	$do = "all";
	$h3 = "Výpis složek" . aprilovyZertik($lastRandomNumber);
	$h3Link = "";

	echo "	<h3><a href='/$link/' title='Poštolka - $h3'>$h3</a></h3>
	<p class='submenu'><a href=\"#\" onclick=\"hide('zprava');return false;\" class='permalink' title='Nová zpráva'>Nová zpráva</a><span class='hide'> | </span><a href='/$link/in/' class='permalink' title='Příchozí a přijaté zprávy, směr in'>Přijaté</a><span class='hide'> | </span><a href='/$link/out/' class='permalink' title='Odchozí a odeslané zprávy, směr out'>Odeslané</a><span class='hide'> | </span><a href=\"javascript: post_del()\" class='permalink' title='Smazat označené'>Smazat označené</a></p>
	<div id='postolka-all'>
";

if (isSet($_GET['error'])){
	switch ($_GET['error']){
		case 1:
		  $error = "Nebyl vyplněn příjemce zprávy.";
		break;
		case 2:
		  $error = "Nebyla zadána zpráva.";
		break;
		case 3:
		  $error = "Příjemce(i) neexistuje.";
		break;
	}
	info($error);
}elseif (isSet($_GET['ok'])){
	switch ($_GET['ok']){
		case 1:
		  $ok = "Zpráva v pořádku odeslána.";
		break;
		case 2:
		  $ok = "Vybrané zprávy smazány.";
		break;
		case 3:
		  $ok = "Pošta vyprázdněna.";
		break;
	}
	ok($ok);
}

echo $new_mess_form;

echo "<div id='vypis-all'>\n";

		if (isset($AragornCache)) {
			$cachedVal = $AragornCache->getVal("post-unread:$_SESSION[uid]");
			$messToMeUnR = array($cachedVal);
			if ($messToMeUnR[0] === false) {
				$messToMeUnRS = mysql_query("SELECT count(*) FROM 3_post_new WHERE tid=$_SESSION[uid] AND stavto = '0'");
				$messToMeUnR = mysql_fetch_row($messToMeUnRS);
				mysql_free_result($messInUnRS);
				$AragornCache->replaceVal("post-unread:$_SESSION[uid]", intval($messToMeUnR[0]), 900);
			}
		}


		$messFromMeC = mysql_fetch_row(mysql_query($sqlFromMeCount));
		$messFromMeC = $messFromMeC[0];
		$allMessFromMeC = $messFromMeC;

		$messToMeC = mysql_fetch_row(mysql_query($sqlToMeCount));
		$messToMeC = $messToMeC[0];
		$allMessToMeC = $messToMeC;

echo "
<div class='highlight-top'></div>
<div class='highlight-mid'>
	<div class='diskuze-okruh'><a href=\"#\" onclick=\"hide('vypis-in');return false;\" class='diskuze-toggle' title='Zobrazit / skrýt'>(x)</a> <span class='permalinkb'>Přijaté (<span class='m-in-unr'>$messToMeUnR[0]</span>/$messToMeC)</span></div>
	<div id='vypis-in'>\n";
		if ($messToMeC<1) {
			echo "		<div class='oblast-popis'>Žádné příchozí zprávy.</div>";
		}
		else {
		  $c = 0;
			echo "		<div class='diskuze-vypis'>
			<table border='0' class='post-tbl' cellpadding='0' cellspacing='0'>\n";

			$addIndexUrl = "";
			if ($index>1) $addIndexUrl = "?index=".$index;

			$messToMeS = mysql_query($sqlToMe." LIMIT ".($shortVypisCnt+2));

		  while($c<$shortVypisCnt && $zprava = mysql_fetch_object($messToMeS)){
				$rep = "";
				if ($zprava->compressed) $zprava->text = gzuncompress($zprava->text);
				if ($zprava->fid > 1) {
				  $rep = "<a href=\"#\" onclick=\"return rep('"._htmlspec($zprava->login)."')\" title='Odepsat'>RE</a>";
				  $zprava->login = "<a href='/$link/konverzace/$zprava->login_rew/' title='Konverzace s "._htmlspec($zprava->login)."'>".$zprava->login."</a>";
				}
				echo "				<tr><td class='pr-$zprava->stavto'>&nbsp;</td><td><span".sl($zprava->level,1).">$zprava->login</span>: <a title='Přečíst zprávu' class='r permalink2' href='/$link/in/"._encode_num($zprava->id,$_SESSION['uid'])."/$addIndexUrl'> ".mb_strimwidth(strip_tags($zprava->text), 0, 20, "…")."</a></td><td>$rep</td><td>".date("H:i:s",$zprava->cas)."</td><td>".date("j.n.",$zprava->cas)."<input type='checkbox' name='d[]' value='$zprava->id' /></td></tr>\n";
		    $c++;
			}
			mysql_free_result($messToMeS);

			if ($messToMeC > $shortVypisCnt) echo "				<tr><td>&nbsp;</td><td><a href='/$link/in/' class='permalink2' title='Přejít do příchozích zpráv'>&hellip; další</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

			echo "			</table>
		</div>\n";
		}
		echo "	</div>
</div>
<div class='highlight-bot'></div>\n";


echo "<div class='highlight-top'></div>
<div class='highlight-mid'>
	<div class='diskuze-okruh'><a href=\"#\" onclick=\"hide('vypis-out');return false;\" class='diskuze-toggle' title='Zobrazit / skrýt'>(x)</a> <span class='permalinkb'>Odeslané ($messFromMeC)</span></div>
	<div id='vypis-out'>\n";
		if ($messFromMeC<1) {
			echo "		<div class='oblast-popis'>Žádné odchozí zprávy.</div>";
		}
		else {
		  $c = 0;
			echo "		<div class='diskuze-vypis'>\n";
			$txt = array();
			$users = array();

			$messFromMeS = mysql_query($sqlFromMe." LIMIT ".($shortVypisCnt+2));

		  while($c<$shortVypisCnt && $zprava = mysql_fetch_object($messFromMeS)){
		    $txtOne = array();

				$txtOne['id'] = $zprava->id;
				$txtOne['link'] = _encode_num($zprava->id,$_SESSION['uid']);
				$txtOne['tid'] = $zprava->tid;
				$txtOne['stavto'] = $zprava->stavto;
				if ($zprava->compressed) $zprava->text = gzuncompress($zprava->text);
				$txtOne['text'] = strip_tags($zprava->text);
				$txtOne['cas'] = date("H:i:s",$zprava->cas);
				$txtOne['datum'] = date("j.n.",$zprava->cas);
				$txtOne['level'] = $zprava->level;

				if ($zprava->tid > 1) {
				  $txtOne['rep'] = "<a href=\"#\" onclick=\"return rep('"._htmlspec($zprava->login)."')\" title='Napsat'>RE</a>";
				  $txtOne['login'] = $zprava->login;
				  $txtOne['login_rew'] = $zprava->login_rew;
				}
				elseif ($zprava->whis != "") {
				  $txtOne['rep'] = "";
				  $txtOne['whis'] = $zprava->whis;
				  $users = array_merge($users,explode(",",$zprava->whis));
				  $txtOne['whisstav'] = $zprava->whisstav;
				}
				$txt[] = $txtOne;
		    $c++;
			}
			mysql_free_result($messFromMeS);
			if (count($users)>0) {
			  $users = array_unique($users);
			  $uidsS = mysql_query("SELECT id,login,level FROM 3_users WHERE id IN (".join(",",$users).") ORDER BY login_rew ASC");
		    $logins = array();
			  if (mysql_num_rows($uidsS)>0) {
					while($userItem = mysql_fetch_row($uidsS)) {
					  $logins[$userItem[0]] = "<span".sl($userItem[2],1).">".htmlspecialchars($userItem[1],ENT_COMPAT,"UTF-8")."</span>";
					}
					mysql_free_result($uidsS);
				}
			}

			echo "			<table border='0' class='post-tbl' cellpadding='0' cellspacing='0'>\n";

			foreach($txt as $mess){
			  if ($mess['tid'] > 1) {
			    $mess['text'] = str_replace(array("\n","\r"), " ", $mess['text']);
					echo "				<tr><td class='pr-$mess[stavto]'>&nbsp;</td><td><span".sl($mess['level'],1)."><a href='/$link/konverzace/$mess[login_rew]/' title='Konverzace s "._htmlspec($mess['login'])."'>".$mess['login']."</a></span>: <a title='Zobrazit zprávu' class='r permalink2' href='/$link/out/$mess[link]/'> ".mb_strimwidth($mess['text'], 0, 20, "…")."</a></td><td>$mess[rep]</td><td>$mess[cas]</td><td>$mess[datum]<input type='checkbox' name='d[]' value='$mess[id]' /></td></tr>\n";
				}
				else {
					$usersHere = explode(",",$mess['whis']);
					$stavHere = "";
					if ($mess['whisstav'] == str_pad("",count($usersHere),"0")) {
						$stavHere = " class='pr-0'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"1")) {
						$stavHere = " class='pr-1'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"2")) {
						$stavHere = " class='pr-3'";
					}
					elseif ($mess['whisstav'] == str_pad("",count($usersHere),"3")) {
						$stavHere = " class='pr-3'";
					}
					echo "				<tr><td$stavHere></td><td>";
					$loginsHere = array();
					for($d=0;$d<count($usersHere);$d++) {
					  switch(substr($mess['whisstav'],$d,1)){
					    case "0":
					      $sAdd = "(nepřečteno)";
					    break;
					    case "1":
					      $sAdd = "(přečteno)";
					    break;
					    case "2":
					    case "3":
					      $sAdd = "(smazáno)";
					    break;
					    default:
					      $sAdd = "";
					    break;
						}
					  $loginsHere[] = "<span class='pr-".substr($mess['whisstav'],$d,1)."'></span>".$logins[$usersHere[$d]]." $sAdd";
					}
					$mess['rep'] = "<a href=\"#\" onclick=\"return rep('".strip_tags(join(",",$logins))."');\" title='Napsat všem'>RE</a>";
					echo "<a href='/$link/out/$mess[link]/' onmouseover=\"ddrivetip('Hromadná zpráva - zobrazit"._htmlspec("<br />".addslashes(join("<br />",$loginsHere)))."')\" onmouseout='hidedrivetip();'>".$logins[$usersHere[0]].", ".$logins[$usersHere[1]].((count($usersHere)>2)?", ... :":" : ")."</a> <a title='Zobrazit zprávu' class='r permalink2' href='/$link/out/$mess[link]/'> více &raquo;</a></td><td>$mess[rep]</td><td>$mess[cas]</td><td>$mess[datum]<input type='checkbox' name='d[]' value='$mess[id]' /></td></tr>\n";
				}
			}

			if ($messFromMeC > $shortVypisCnt) echo "				<tr><td>&nbsp;</td><td><a href='/$link/out/' class='permalink2' title='Přejít do odchozích zpráv'>&hellip; další</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";

			echo "			</table>
		</div>\n";
		}
		echo "	</div>
</div>
<div class='highlight-bot'></div>\n";

echo "</div>\n";
}

?>
<script charset="utf-8" type="text/javascript">
/* <![CDATA[ */
urlPartPosta = "/<?php echo $link;if ($slink!=''){echo '/'.$slink;}if ($sslink!=''){echo '/'.$sslink;}?>";
window.addEvent('domready',function(){PostolkaMaker()});
var theSender = null;
<?php
//zprava ze pratel
if (isSet($_GET['friend'])){
  echo "rep('"._htmlspec($_GET['friend'])."');\n";
}
?>
  
/* ]]> */
</script>
<?php
}
?>
</div>
<?php
}
else {
	info("Probíhá obnovení tabulky pošty ze zálohy.<br />Omlouváme se za vzniklé potíže.");
}
?>