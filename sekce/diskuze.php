<?php
$doTglrScript = false;
$diskuzeTitle = $itIsApril ? 'Krafárna' : 'Diskuze';

if (isset($error) || isset($ok)) {
}
else {
	$error = "";
	$ok = "";
}

$my_topics = "";
if ($LogedIn) {
	$my_topics = "<span class='hide'> | </span><a class='permalink' href='/diskuze/my/' title='Moje diskuze = ty, které vlastním'>Moje diskuze</a>";
}

if ($slink == "new" && !$LogedIn) {
  include("./sekce/zakaz2.php");
}
elseif ($slink == "ad" && $LogedIn == false){
  echo "<h2 class='h2-head'><a href='/diskuze/' title='$diskuzeTitle'>$diskuzeTitle</a></h2>\n";
	echo "<h3>Administrace diskuzních témat</h3>\n";
	info("Tato sekce je vyhrazena <a href='/admins/' title='Administrátoři Aragorn.cz' class='permalink2'>Administrátorům</a> a Moderátorům diskuzí.");
	echo "<p class='submenu'><a href='/diskuze/' title='Zpět na výpis diskuzních oblastí'>Zpět na výpis oblastí</a></p>\n";
}
elseif ($slink == "") {

	$admin_link = "";
	if ($LogedIn == true && $_SESSION['lvl'] > 2) {
		$dPravaSrc = mysql_query("SELECT COUNT(*) FROM 3_diskuze_prava WHERE id_user='$_SESSION[uid]' AND prava='admin' AND id_dis='0'");
		$dPadm = mysql_fetch_row($dPravaSrc);
		if ($dPadm[0]>0){
			$adNeSch = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM 3_diskuze_topics WHERE schvaleno = '0'"));
			$admin_link = "<span class='hide'> | </span><a class='permalink' href='/diskuze/ad/' title='Administrace TOP-LEVEL'>Administrace ($adNeSch[0])</a>";
		}
	}
// podle okruhu radit ... kdyz jen jeden, tak jen jeden... proto vnoreny while...
	if ($okruh != ""){
		$add_sql = "AND d.okruh = '$okruh' ";
		$okruh_link = "?oblast=$okruh";
		$okruh_back = "<a href='/$link/' class='permalink' title='Na výpis všech diskuzních oblastí'>Oblasti</a><span class='hide'> | </span>";
		if ($oItem) {
			$okruh_name = "Oblast ".stripslashes($oItem->nazev);
		}
		else {
			$okruh_src0 = mysql_query("SELECT nazev FROM 3_diskuze_groups WHERE id = '$okruh'");
			$okruh_name = mysql_fetch_row($okruh_src0);
			$okruh_name = "Oblast ".stripslashes($okruh_name[0]);
		}
		$okruh_src = mysql_query("SELECT nazev_rew, nazev, id, popis FROM 3_diskuze_groups WHERE id = '$okruh'");
	}
	else {
		$add_sql = "";
		$okruh_link = $okruh_back = "";
		$okruh_name = "Výpis oblastí";
		$okruh_src = mysql_query("SELECT nazev_rew, nazev, id, popis FROM 3_diskuze_groups ORDER BY nazev ASC");
	}

    echo "<h2 class='h2-head'><a href='/diskuze/' title='$diskuzeTitle'>$diskuzeTitle</a></h2>\n";
	echo "<h3><a href='/$link/$okruh_link' title='Výpis okruhů'>$okruh_name</a></h3>\n";
	if ($LogedIn){
	 echo "<p class='submenu'>".$okruh_back."<a href='/$link/new/$okruh_link' class='permalink' title='Založení nového tématu'>Nové téma</a>".$my_topics.$admin_link."</p>\n";
  }

	while($oItem = mysql_fetch_object($okruh_src)){
	if ($okruh_link != "") {
		if ($LogedIn) {
			$sql = "SELECT t.id, t.nazev, t.prava_guest, t.prava_reg, t.nazev_rew, t.okruh, t.schvalenotime,
			3_visited_3.news AS unread_comms, 3_visited_3.uid AS v_uid, 
    	(SELECT COUNT(*) FROM 3_comm_3 WHERE 3_comm_3.aid = t.id) AS all_comms
    	FROM 3_diskuze_topics AS t
			LEFT JOIN 3_visited_3 ON 3_visited_3.aid = t.id AND 3_visited_3.uid = $_SESSION[uid] 
      WHERE t.okruh = $oItem->id AND t.schvaleno = 1 
	    ORDER BY t.nazev_rew ASC";
		}
		else {
			$sql = "SELECT t.id, t.nazev, t.prava_guest, t.prava_reg, t.nazev_rew, t.okruh, t.schvalenotime,
			0 AS unread_comms, 0 AS v_uid,
			(SELECT COUNT(*) FROM 3_comm_3 WHERE 3_comm_3.aid = t.id) AS all_comms
			FROM 3_diskuze_topics AS t  
			WHERE t.okruh = $oItem->id AND t.schvaleno = 1 
			ORDER BY t.nazev_rew ASC";
		}
  }else{
      $sql = "SELECT id, nazev, popis FROM 3_diskuze_groups ORDER BY nazev ASC";
  }

// echo "<!--$sql-->"; // DEBUG

		$dS = mysql_query($sql);

		if ($dS && mysql_num_rows($dS) > 0) {
?>
<hr class='hide' />
<div class='highlight-top'></div>
<div class='highlight-mid'>
	<div>
		<div class='diskuze-okruh'>
			<a href='/<?php echo $link;?>/?oblast=<?php echo $oItem->id;?>' class='permalinkb' title='Zobrazit oblast <?php echo stripslashes($oItem->nazev);?>'><?php echo stripslashes($oItem->nazev);?></a>
		</div>
		<div id='diskuze-okruh-<?php echo $oItem->id;?>'>
			<div class='oblast-popis'><?php echo _htmlspec(stripslashes($oItem->popis));?></div>
<?php
		if ($okruh_link != "") {
?>
			<div class='diskuze-vypis'>
				<table width='100%'>
<?php
	$g_UID = (isSet($_SESSION['uid']))?$_SESSION['uid']:0;
	while($dItem = mysql_fetch_object($dS)){ $dItem->uid = $g_UID;?>  
					<tr><td class='dis-name'><a href='/<?php echo "$link/$dItem->nazev_rew/";?>' class='permalinkb2' title='<?php echo _htmlspec(mb_strimwidth(stripslashes($dItem->popis), 0, 60, "...","UTF-8"));?>'><?php echo _htmlspec(mb_strimwidth(stripslashes($dItem->nazev), 0, 40, "...","UTF-8"));?></a><?php echo ($dItem->prava_guest == 'hide' ? (($dItem->prava_reg == 'hide') ? ' <span class="helper" title="soukromá diskuze">&#9888;</span>' : ' <span class="helper" title="jen pro přihlášené">&#9888;</span>') : '');?></td><td><?php echo getComm($dItem->id, 3,true,$dItem->unread_comms,$dItem->all_comms,$dItem->v_uid);?></td></tr>
<?php
			}
?>
				</table>
			</div>
<?php
		}
?>
		</div>
	</div>
</div>
<div class='highlight-bot'></div>
<?php
		}
		
	}
}
elseif ($slink == "my") {
	if ($LogedIn) {
	  echo "<h2 class='h2-head'><a href='/diskuze/' title='$diskuzeTitle'>$diskuzeTitle</a></h2>\n";
		echo "<h3><a href='/diskuze/my/' title='Mé diskuze'>Diskuze, které vlastním</a></h3>\n";
		echo "<p class='submenu'><a href='/diskuze/' title='Zpět na výpis diskuzních oblastí' class='permalink'>Zpět na oblasti</a></p>\n";
		$okruhySrc = mysql_query("SELECT nazev, id FROM 3_diskuze_groups ORDER BY nazev ASC");
		$okruhy = array ();
?>
<div class='highlight-top'></div>
<div class='highlight-mid'>
<?php
		while ($okruhyItem = mysql_fetch_object($okruhySrc)) {
			$okruhy[$okruhyItem->id] = _htmlspec(stripslashes($okruhyItem->nazev));
		}
		$disSrc = mysql_query("SELECT id,nazev,nazev_rew,schvaleno,okruh FROM 3_diskuze_topics WHERE owner = $_SESSION[uid] ORDER BY okruh ASC, nazev ASC");
		if (mysql_num_rows($disSrc)>0) {
			echo "	<div class='art'>\n";
			while ($disItem = mysql_fetch_object($disSrc)) {
				if ($disItem->schvaleno == '1') {
				  $disItem->nazev = _htmlspec(stripslashes($disItem->nazev));
					$disItem->nazev = "<a href='/diskuze/$disItem->nazev_rew/' title='".$disItem->nazev."'>".$disItem->nazev."</a>";
				}
				else {
					$disItem->nazev = _htmlspec(stripslashes($disItem->nazev));
				}
				echo "		<p>".$okruhy[$disItem->okruh]." :: ".$disItem->nazev."</p>\n";
			}
			echo "	</div>\n";
		}
		else {
			echo "	<p class='t-a-c text art'>Nevlastníte žádná diskuzní témata.</p>\n";
		}
		echo "</div>\n";
		echo "<div class='highlight-bot'></div>\n";
	}
	else {
	  include("./sekce/zakaz2.php");
	}
}
elseif (($slink == "new") && ($LogedIn==true)) {
  echo "<h2 class='h2-head'><a href='/diskuze/' title='$diskuzeTitle'>$diskuzeTitle</a></h2>\n";
	echo "<h3><a href='/$link/new/' title='Založit nové téma'>Nové téma</a></h3>
<p class='submenu'><a href='/$link/' class='permalink' title='Zpět na výpis oblastí'>Zpět na oblasti</a></p>";

	if ($error>0){
		switch ($error){
		case 1:
			$error = "Název je příliš krátký. Minimální délka názvu diskuze jsou <strong class='warning'>3 písmenné znaky</strong>.";
			info($error);$error=1;
		break;
		case 2:
			$error = "Diskuze s  <acronym title='Přesněji, jeho SEO verze již v diskuzních tématech existuje.' xml:lang='cs'>podobným názvem</acronym> již existuje. Doporučujeme ho nějak pozměnit.";
			info($error);$error=2;
		break;
		case 3:
			$error = "Nebyla zvolena diskuzní oblast.";
			info($error);$error=3;
		break;
		case 4:
			$error = "Název diskuzního tématu byl příliš dlouhý nebo krátký. Minimum jsou 4 znaky a nejvyšší povolená délka je 40 znaků.";
			info($error);$error=4;
		break;
		case 5:
			$error = "Můžete vlastnit maximálně 2 neschválená diskuzní témata.";
			info($error);$error=5;
		break;
		}
	}
	elseif ($ok>0){
		switch ($ok){
		case 1:
			$ok = "Téma bylo v pořádku odesláno ke schválení.";
			ok($ok);
		break;
		}
	}

	if (isSet($_GET['oblast'])) {
		$okruh = addslashes(strip_tags($_GET['oblast']));
	}else {
		$okruh = 0;
	}
	$okruhySrc = mysql_query("SELECT nazev, id FROM 3_diskuze_groups ORDER BY nazev ASC");
	$okruhy = array ();
	$a = 0;
	while ($okruhyItem = mysql_fetch_object($okruhySrc)) {
		$a++;
		$okruhy[$a] = "value='$okruhyItem->id'>"._htmlspec(stripslashes($okruhyItem->nazev));
		if ($okruhyItem->id == $_GET['oblast']){
			$okruhy[$a] = "selected='selected' ".$okruhy[$a];
		}
	}
	$okruhy_select = join("</option><option ",$okruhy);
	$okruhy_select = "<label><span>Diskuzní oblast</span><select name='oblast' style='width: 152px;'><option> - - - - - </option><option ".$okruhy_select."</option></select></label>";

?>
	<div class='f-top'></div>
	<div class='f-middle'>
		<form action='/diskuze/new/?akce=diskuze-new' method='post' class='f' name='form_for_new' id='form_for_new' onsubmit="return checkForNew('diskuze','nazev_tematu',['nazev','popis','oblast'],false);">
		<fieldset>
		<legend>Přidání nového tématu</legend>
		<label><span>Název</span><input type='text' id='nazev_tematu' maxlength='40' name='nazev' size='20' value='' /></label>
		<label><span>Popis</span><input type='text' name='popis' maxlength='255' size='20' value='' /></label>
		<?php echo $okruhy_select;?>
		<input class='button' type='submit' value='Odeslat a nahrát' />
		</fieldset>
	</form>
	</div>
	<div class='f-bottom'></div>
<?php
}
elseif ($slink == "ad" && $LogedIn) {
	if (isSet($_GET['info']) && $error == "" && $ok == "") {
		switch ($_GET['info']){

		case 10:
			$inf = "Diskusní téma bylo smazáno.";
			ok($inf);
		break;
		}
	}
	$dS = mysql_query ("SELECT id FROM 3_diskuze_prava WHERE id_dis = 0 AND id_user = $_SESSION[uid] AND prava = 'admin'");
	$dC = mysql_num_rows($dS);

	if ($dC < 1){
	  echo "<h2 class='h2-head'><a href='/diskuze/' title='$diskuzeTitle'>$diskuzeTitle</a></h2>\n";
		echo "<h3><a href='/diskuze/' title='Zpět na výpis oblastí'>Administrace diskuzí - TOP LEVEL</a></h3>\n";
		info("Tato sekce je vyhrazena <a href='/admins/' title='Administrátoři Aragorn.cz' class='permalink2'>Administrátorům</a> a Moderátorům diskuzí.");
	}
	else {
		$diskuzeNs = mysql_query ("SELECT t.nazev_rew,t.nazev,t.popis,g.nazev AS nazev_okruhu,u.login FROM 3_diskuze_topics AS t, 3_diskuze_groups AS g, 3_users AS u WHERE t.schvaleno = '0' AND t.okruh = g.id AND t.owner = u.id ORDER BY t.okruh ASC, nazev_rew ASC");
		$okruhy_src = mysql_query("SELECT nazev_rew, nazev, id, popis FROM 3_diskuze_groups ORDER BY nazev ASC");

		if (mysql_num_rows($okruhy_src)>0) {
			while($oItem = mysql_fetch_object($okruhy_src)){
				$oblastiArr[] = "<option value='$oItem->nazev_rew'>přesun do "._htmlspec(stripslashes($oItem->nazev))."</option>";
				$okruhyArr[] = "<tr><td><input class='checkbox' type='radio' name='d_oblast' value='$oItem->nazev_rew' /></td><td>"._htmlspec(stripslashes($oItem->nazev))."</td><td>"._htmlspec(stripslashes($oItem->popis))."</td>";
			}
			$oblastiArr = join("\n\t\t\t\t",$oblastiArr);
			$okruhyArr = "<table border='0' cellspacing='0' cellpadding='0'>
						<thead><th></th><th width='40%'>Název</th><th>Popis</th></thead>
						<tbody>
							".join("\n\t\t\t\t\t\t\t\t",$okruhyArr)."
						</tbody>
				</table>\n";
		}
		else {
			$okruhyArr = "";
		}
    echo "<h2 class='h2-head'><a href='/diskuze/' title='$diskuzeTitle'>$diskuzeTitle</a></h2>\n";
		echo "<h3><a href='/diskuze/ad/' title='Administrace - schvalování, mazáni, přesun neschvalených témat'>Administrace diskuzí - TOP LEVEL</a></h3>
<p class='submenu'><a href='/diskuze/' title='Zpět na výpis diskuzních oblastí'>Zpět na výpis oblastí</a></p>\n";
	if (isSet($_GET['info'])) {
		switch ($_GET['info']){
		case 1:
			$inf = "Název je příliš krátký. Minimální délka názvu diskuze jsou <strong class='warning'>3 písmenné znaky</strong>.";
			info($inf);
		break;
		case 2:
			$inf = "Příkazy z Administrátorského rozhraní v pořádku vykonány.";
			ok($inf);
		break;
		case 3:
			$inf = "Již existuje diskuze s <acronym title='Přesněji jeho SEO verze již v diskuzních tématech existuje.' xml:lang='cs'>podobným názvem</acronym>. Doporučujeme ho nějak pozměnit.";
			info($inf);
		break;
		case 4:
			$inf = "Zadaný název diskuzního tématu byl příliš dlouhý nebo krátký. Minimum jsou 4 znaky a nejvyšší povolená délka je 40 znaků. <acronym title='Netýká se klasické latinky, numerických znaků a interpunkce' xml:lang='cs'>Jiné znaky</acronym> mohou mít sami o sobě délku 2 znaků normálních.";
			info($inf);
		break;
		case 5:
			$inf = "Zadaný popis diskuzního tématu byl příliš dlouhý. Nejvyšší povolená délka je 255 znaků. <acronym title='Netýká se klasické latinky, numerických znaků a interpunkce' xml:lang='cs'>Jiné znaky</acronym> mohou mít sami o sobě délku 2 znaků normálních.";
			info($inf);
		break;
		case 6:
			$inf = "Obecné údaje o tématu byly upraveny.";
			ok($inf);
		break;
		case 7:
			$inf = "Správcovství bylo změněno.";
			ok($inf);
		break;
		case 8:
			$inf = "Přístupová práva byla upravena.";
			ok($inf);
		break;
		case 9:
			$inf = "U diskuzního tématu byl uložen nový vlastník.";
			ok($inf);
		break;
		case 10:
			$inf = "Diskusní téma bylo smazáno.";
			ok($inf);
		break;
		}
	}
	$doTglrScript = true;
	echo "
<div class='f-top'></div>
<div class='f-middle'>
  <form action='/diskuze/ad/?akce=diskuze-administrace' method='post' class='f'>
		<fieldset>
			<legend class='tglr'>Administrace diskuzí</legend>
			<div class='tgld'>
";
		$diskuzeArrN = array();
		if (mysql_num_rows($diskuzeNs)>0) {
			while($disItem = mysql_fetch_object($diskuzeNs)){
				$diskuzeArrN[] = "<tr><td>$disItem->nazev_okruhu:</td><td><input class='checkbox' type='checkbox' name='d_tema[]' value='$disItem->nazev_rew' /></td><td>"._htmlspec(stripslashes($disItem->login))."</td><td>"._htmlspec(stripslashes($disItem->nazev))."</td><td>"._htmlspec(stripslashes($disItem->popis))."</td></tr>";
			}
			$diskuzeArrN = "
				<div><p>Pro každé téma je vhodné nejdříve zkusit použít vyhledávač (<a href='http://www.google.cz/' target='_blank'>Google</a>, <a href='http://www.atlas.cz/' target='_blank'>Atlas</a>, <a href='http://www.centrum.cz/' target='_blank'>Centrum</a>, <a href='http://www.jzxo.cz/' target='_blank'>Jyxo</a>), aby nedocházelo ke zdvojování témat.</p></div>
				<table class='edttbl' border='0' cellspacing='5' cellpadding='1'>
					<thead><th>Oblast</th><th></th><th>Autor</th><th>Název</th><th>Popis</th></thead>
						<tbody>
							".join("\n\t\t\t\t\t\t\t",$diskuzeArrN)."
					</tbody>
				</table>\n";
		}
		else {
			$diskuzeArrN = "";
		}
		echo $diskuzeArrN;
		echo "				<label for='akce_tema'><span>Akce</span><select id='akce_tema' name='akce_tema'><option value=''> - - - - - </option><option value='schvalit'>Schválit</option><option value='' disabled='disabled'></option>$oblastiArr<option value='' disabled='disabled'></option><option value='smazat'>Smazat téma</option></select></label>
				<label for='text_postolka'><span>Důvod</span><input id='text_postolka' type='text' name='text_postolka' maxlength='255' size='20' value='' /></label>
				<label for='sendInfo'><span>INFO-poštolku?</span><input type='checkbox' value='yes' name='sendInfo' id='sendInfo' /></label>
				<input class='button' type='submit' value='Provést' />
			</div>
		</fieldset>
	</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
	<form action='/diskuze/ad/?akce=diskuze-administrace2' method='post' class='f'>
		<fieldset>
			<legend class='tglr'>Administrace oblastí</legend>
			<div class='tgld'>
				$okruhyArr
				<label for='nazev_oblast'><span>Název</span><input id='nazev_oblast' type='text' maxlength='40' name='nazev_oblast' size='20' value='' /></label>
				<label for='popis_oblast'><span>Popis</span><input id='popis_oblast' type='text' name='popis_oblast' maxlength='255' size='20' value='' /></label>
				<label for='akce_oblast'><span>Akce</span><select id='akce_oblast' name='akce_oblast'><option value=''> - - - - - </option><option value='upravit'>Upravit</option><option value='' disabled='disabled'></option><option value='zalozit'>Založit novou oblast</option></select></label>
				<input class='button' type='submit' value='Provést změny' />
			</div>
		</fieldset>
	</form>
</div>
<div class='f-bottom'></div>
\n";
	}
}
else {
	$dS = mysql_query ("SELECT d.*, u.login AS vlastnik, u.login_rew AS vlastnik_rew FROM 3_diskuze_topics AS d, 3_users AS u WHERE u.id = d.owner AND d.id = '$id' AND schvaleno = '1'");
	$dC = mysql_num_rows($dS);

	if ($dC < 1){
	  echo "<h2 class='h2-head'><a href='/diskuze/' title='$diskuzeTitle'>$diskuzeTitle</a></h2>\n";
		echo "<h3><a href='/diskuze/'>Chyba - diskuze nenalezena</a></h3>\n";
		info("Diskuzi, kterou hledáte, bohužel nebylo možno nalézt. Možná ji vlastník přejmenoval nebo zrušil.");
		echo "<div class='art'><p><a href='/diskuze/' title='Výpis diskuzních oblastí' class='permalink'>Zpět na výpis všech oblastí</a></p></div>\n";

	}
	else {
		$dItem = mysql_fetch_object($dS);
		if ($dItem->nastenka_compressed) $dItem->nastenka = gzuncompress($dItem->nastenka);
		$id = $dItem->id;
		$AllowedTo = GetPravaHere($dItem->id, $dItem->owner, $dItem->prava_reg, $dItem->prava_guest, $LogedIn);

  //kontrola zalozky
$sB = chBook();
    echo "<h2 class='h2-head'><a href='/diskuze/' title='$diskuzeTitle'>$diskuzeTitle</a></h2>\n";
		echo "<h3><a href='/diskuze/$dItem->nazev_rew/' title='"._htmlspec(stripslashes($dItem->nazev))."'>"._htmlspec(stripslashes($dItem->nazev))."</a></h3>
<p class='submenu'><a class='permalink' title='Zpět na výpis témat ze stejné diskuzní oblasti' href='/diskuze/?oblast=$dItem->okruh'>Výpis této oblasti</a><span class='hide'> | </span>$sB<span class='hide'> | </span><a class='permalink' href='/diskuze/$slink/ankety/' title='Ankety diskuze a jejich výsledky'>Ankety</a>";
		if ($LogedIn) echo "<span class='hide'> | </span><a class='permalink' href='/diskuze/$slink/stats/' title='Jednoduché statistiky návštěvnosti diskuze'>Statistiky</a>";
		if (($AllowedTo == "superall" || $AllowedTo == "all") && $_GET['sslink'] == "admin") {
			echo "<span class='hide'> | </span><a class='permalink' href='/$link/$slink/' title='Zpět na téma "._htmlspec(stripslashes($dItem->nazev))."'>Zpět na téma</a>";
		}
		elseif ($AllowedTo == "superall" || $AllowedTo == "all") {
			echo "<span class='hide'> | </span><a class='permalink' href='/$link/$slink/admin/' title='Administrace tématu "._htmlspec(stripslashes($dItem->nazev))."'>Administrace</a>";
		}
		echo "</p>\n";

		// zakladni prava pro skupiny uzivatelu
		$pR1 = $pR2 = $pR3 = $pG1 = $pG2 = $part1 = $part2 = "";  
		if ($dItem->prava_reg=="write"){
			$part1 = "registrovaní uživatelé mají právo <strong>číst i zapisovat</strong>";
			$pR1 = " selected='selected'";
		}elseif ($dItem->prava_reg=="read"){
			$part1 = "registrovaní uživatelé mohou <strong>jen číst</strong>";
			$pR2 = " selected='selected'";
		}else {
			$part1 = "registrovaní uživatelé <strong>nemají přístup</strong> k tématu";
			$pR3 = " selected='selected'";
		}
	
		if ($dItem->prava_guest=="read"){
			$part2 = "neregistrovaní mohou <strong>jen číst</strong>";
			$pG1 = " selected='selected'";
		}else {
			$part2 = "neregistrovaní <strong>nemají přístup</strong> k tématu";
			$pG2 = " selected='selected'";
		}


if (isSet($_GET['error'])){

switch ($_GET['error']){

case 15:
  $error = "Záložka nemohla být vytvořena.";
break;

case 16:
  $error = "Překročen limit $zalozkyOmezeniCount povolených záložek.";
break;

case 17:
  $error = "Záložka nebyla odebrána.";
break;

}

info($error);
}elseif (isSet($_GET['ok'])){

switch ($_GET['ok']){

case 20:
  $ok = "Promazáno.";
break;

case 15:
  $ok = "Záložka vytvořena.";
break;

case 16:
  $ok = "Záložka odebrána.";
break;

}

ok($ok);

}
		if (!isSet($_GET['info']) && $ok == "" && $error == ""){
			if ($AllowedTo == "nothing") {
					if (!$LogedIn){
						info("Do tohoto diskuzního tématu nemají neregistrovaní uživatelé přístup.");
					}
					else {
						infow("Nemáte oprávnění číst tuto diskuzi.");
					}
				}
				elseif ($AllowedTo == "read"){
					if ($LogedIn == true) {
						inf("V tomto diskuzním tématu máte práva jen na čtení příspěvků.");
					}
				}
/*				elseif ($AllowedTo == "write"){
				}
				elseif ($AllowedTo == "all"){
					okw("Máte práva na přidávání a mazání příspěvků a komentářů a také práva na změnu nástěnky, názvu či popisu diskuze a přístupových práv.");
				}
				elseif ($AllowedTo == "superall"){
					inf("Zde máte neomezená práva. Můžete měnit vše včetně vlastnictví diskuze.");
				}
*/		}

		if ($sslink == "ankety") {
			if ($AllowedTo == "all" || $AllowedTo == "superall") {
				$jeAnketaOne = mysql_query("SELECT * FROM 3_ankety WHERE dis = '$dItem->id' AND aktiv = 1");
				if (mysql_num_rows($jeAnketaOne)>0) {
					$AnketaOne = mysql_fetch_object($jeAnketaOne);
					$moznostiAnk = explode(">", stripslashes($AnketaOne->odpoved));
					$poctyOne = mysql_query("SELECT count(*) AS pocet, hlas FROM 3_ankety_data WHERE ank_id = '$AnketaOne->id' GROUP BY hlas ORDER BY hlas ASC");
					$hlasyOne = array_fill(0, count($moznostiAnk), 0);
					$hlasyAll = 0;
					if (mysql_num_rows($poctyOne)>0) {
						while ($hlasOne = mysql_fetch_object($poctyOne)) {
							$hlasyOne[$hlasOne->hlas] = $hlasOne->pocet;
							$hlasyAll = $hlasyAll + $hlasOne->pocet;
	 					}

					}
					echo "<div class='f-top'></div>
<div class='f-middle'>
	<form action='?akce=anketa&amp;do=edit' method='post' class='edttbl'>
		<table class='ankety-edt'>
			<tr><td><h4>Otázka: </h4></td><td colspan='3'><input type='text' name='otazka' value='"._htmlspec(stripslashes($AnketaOne->otazka))."' /></td></tr>
			<tr><td rowspan='".count($moznostiAnk)."'><h4>Možnosti:&nbsp;</h4></td><td><input type='text' name='moznosti[]' value='$moznostiAnk[0]' /></td><td>$hlasyOne[0]</td><td>".(($hlasyAll > 0) ? round($hlasyOne[0]/$hlasyAll*100,1) : 0)." %</td></tr>
";
for ($i=1;$i<count($moznostiAnk);$i++) {
echo "			<tr><td><input type='text' name='moznosti[]' value='$moznostiAnk[$i]' /></td><td>$hlasyOne[$i]</td><td>".(($hlasyAll > 0) ? round($hlasyOne[$i]/$hlasyAll*100,1) : 0)."%</td></tr>\n";
}
echo "		</table>
		<input type='submit' value='Upravit' /> &nbsp; &nbsp; <input type='button' onclick='javascript:window.location.href=\"/diskuze/$slink/ankety/?akce=anketa&amp;do=end\"' value='Ukončit hlasování' /> &nbsp; &nbsp; <input type='button' onclick='javascript:window.location.href=\"/diskuze/$slink/ankety/?akce=anketa&amp;do=null\"' value='Anulovat hlasování' />
	</form>
</div>
<div class='f-bottom'></div>
";
				}
				else {
					echo "	<p class='art text'>Žádná aktivní anketa.</p>\n";
				}
				echo "<div class='f-top'></div>
<div class='f-middle'>
	<script type='text/javascript'>
citac=2;
ukaz=true;
function add_field(){
	var e;
	if(!document.createElement || !document.appendChild) {
		alert('Omlouvame se, ale vas prohlizec nepodporuje pridavani polozek.');
		return false;
	}
	if(citac>=50)
		alert ('50 odpovedi je maximum.');
	else{
		if(citac==10 && !confirm('Skutecne potrebujes vice nez deset odpovedi?'))
			return false;
		citac++;
		e = document.createElement('div');
		e.id = 'm-a-'+citac+'-lay';
		part=e.innerHTML = \"<input type='text' name='new_moznost[]'>\";
		document.getElementById('anketa-new-opts').appendChild(e);
	}
		return false;
}
</script>
	<form action='?akce=anketa&amp;do=new' method='post' class='edttbl'>
		<table class='edttbl' id='anketa-create'>
			<tr><td><h4>Nová otázka: </h4></td><td><input type='text' name='new_otazka' /></td></tr>
			<tr><td> </td><td><a href='#' onclick='javascript: add_field();'>Přidat další možnost</a></td></tr>
			<tr><td><h4>Možnosti: </h4></td><td id=\"anketa-new-opts\"><div><input type='text' name='new_moznost[]' /></div><div><input type='text' name='new_moznost[]' /></div>";
/*for ($a=2;$a<=10;$a++) {
	echo "<div id='m-a-$a-lay'></div>";
}*/
echo "</td></tr>
			<tr><td></td><td><input type='submit' value='Založit novou anketu' /></td></tr>
		</table>
	</form>
</div>
<div class='f-bottom'></div>
";
			}
			if ($AllowedTo != "nothing") {
				$anketyOldS = mysql_query("SELECT * FROM 3_ankety WHERE dis = '$dItem->id' AND aktiv = 0 ORDER BY id DESC");
				echo "<div class='highlight-top'></div>
<div class='highlight-mid'>\n";
				if (mysql_num_rows($anketyOldS)>0) {
					$oldIDS = array();
					$aOld = array();
					while ($anketaOld = mysql_fetch_object($anketyOldS)) {
						$oldIDS[] = $anketaOld->id;
						$aOld[$anketaOld->id]['moznosti'] = explode(">", $anketaOld->odpoved);
						$aOld[$anketaOld->id]['otazka'] = $anketaOld->otazka;
						$aOld[$anketaOld->id]['hlasy'] = explode(">",$anketaOld->counts);
					}
					$oldIDsTXT = join(",", $oldIDS);
					while ($hOldItem = mysql_fetch_object($hlasyOldS)) {
						$aOld[$hOldItem->ank_id]['hlasy'][$hOldItem->hlas] = $hOldItem->pocet;
					}
					for ($i=0;$i<count($oldIDS);$i++) {
						echo "	<table class='diskuze-one m5 edttbl text'>\n		<tr><td><h4>Otázka: </h4></td><td>".$aOld[$oldIDS[$i]]['otazka']."</td>";
						if ($AllowedTo == "superall" || $AllowedTo == "all") {
							echo "<td><p><a href='?akce=anketa&amp;do=smazat&amp;anketa=$oldIDS[$i]'>Smazat</a></p></td>";
						}
						else {
							echo "<td></td>";
						}
						echo "</tr>\n";
						for ($a=0;$a<count($aOld[$oldIDS[$i]]['moznosti']);$a++){
							echo "		<tr><td></td><td>".$aOld[$oldIDS[$i]]['moznosti'][$a]."</td><td>".$aOld[$oldIDS[$i]]['hlasy'][$a]."</td></tr>\n";
						}

						echo "	</table>\n";
					}
				}
				else {
					echo "	<p class='art text'>Žádné starší ankety</p>\n";
				}
				echo "</div>\n";
				echo "<div class='highlight-bot'></div>\n";
			}
			echo "<p class='art text t-a-c'><a href='/$link/$slink/' class='permalink2'>Zavřít Ankety</a></p>\n";
		}
		elseif ($sslink == "stats") {
			echo "<div class='highlight-top'></div>\n<div class='highlight-mid'>\n";
			echo "	<table cellspacing='0' cellpadding='0' border='0' class='edttbl'>\n";
			$statS = mysql_query("SELECT u.login,v.time,v.bookmark FROM 3_visited_3 AS v, 3_users AS u WHERE u.id = v.uid AND v.aid = $dItem->id ORDER BY $statsOrder");
			$statSleduje = mysql_num_rows($statS);
			if ($statSleduje>0) {
			  echo "		<tr><td>Nick</td><td>$statsTime</td><td>Záložka</td></tr>\n";
	      $statZalozkyCnt = 0;
			  while($stat = mysql_fetch_row($statS)){
		      $maZalozku = "ne";
			    if ($stat[2] == "1") {
			      $statZalozkyCnt++;
			      $maZalozku = "ano";
					}
			    echo "		<tr><td>$stat[0]</td><td>".date("d.m.Y v H:i:s",$stat[1])."</td><td>$maZalozku</td></tr>\n";
				}
				$konc = "";
				if ($statSleduje < 5 && $statSleduje > 1) $konc = "é";
				elseif ($statSleduje >= 5) $konc = "ů";
				$koncZ = "ka";
				if ($statZalozkyCnt < 5 && $statZalozkyCnt > 1) $koncZ = "ky";
				elseif ($statZalozkyCnt >= 5 || $statZalozkyCnt == 0) $koncZ = "ek";
				echo "		<tr><td colspan='2'>$statSleduje uživatel".$konc."</td><td>".$statZalozkyCnt." zálož".$koncZ."</td></tr>\n";
			}
			else echo "		<tr><td>Diskuzi nikdo nenavštěvuje.</td></tr>\n";
			echo "	</table>\n";
			echo "	<p class='art text t-a-c'><a href='/$link/$slink/' class='permalink2'>Zavřít Statistiky</a></p>\n";
			echo "</div>\n<div class='highlight-bot'></div>\n";
		}
		elseif ((($AllowedTo == "all") || ($AllowedTo == "superall")) && $sslink == "admin") {
				$prava_uzivatele = "<select id='prava_nastav' name='prava_nastav' size='1'><option value=''> - - - - - </option><option value='psat'>Číst i psát</option><option value='cist'>Jen číst</option><option value='zakazat'>Bez přístupu</option><option value='smazat'>Smazat</option></select>";
				$prava_registrovani = "<select id='prava_registrovani' name='registrovani' size='1'><option value='oboje'$pR1>Číst i psát</option><option value='cist'$pR2>Pouze čtení</option><option value='skryt'$pR3>Zakázaný přístup</option></select>";
				$prava_hoste = "<select id='prava_hoste' name='hoste' size='1'><option value='cist'$pG1>Číst</option><option value='skryt'$pG2>Zakázaný přístup</option></select>";

				$spravci = mysql_query ("SELECT u.login, u.login_rew FROM 3_diskuze_prava AS p, 3_users AS u WHERE p.id_dis = '$dItem->id' AND u.id = p.id_user AND p.prava = 'moderator' ORDER BY u.login ASC");
				$spravciArr = array();
				if (mysql_num_rows($spravci)>0) {
					while($sItem = mysql_fetch_object($spravci)){
						$spravciArr[] = "<li><input class='checkbox' type='checkbox' name='spravci_tematu[]' value='$sItem->login_rew' />&nbsp;$sItem->login</li>";
					}
					$spravciArr = "<ul>\n					".join("\n					",$spravciArr)."\n				</ul>\n";
				}
				else {
					$spravciArr = "";
				}

				if ($AllowedTo == "superall") {
					$doTglrScript = true;
					echo "
<div class='f-top'></div>
<div class='f-middle'>
	<form action='/diskuze/$slink/admin/?akce=diskuze-spravci' method='post' class='f'>
		<fieldset>
			<legend class='tglr'>Správci</legend>
			<div class='tgld'>
				$spravciArr
				<label for='new-spravce'><span>Nový správce</span><input id='new-spravce' type='text' name='novy-spravce' maxlength='40' /></label>
				<label for='akce-spravce'><span>Akce</span><select id='akce-spravce' name='akce-spravce'><option value=''> - - - - - </option><option value='pridat'>Přidat</option><option value='smazat'>Smazat</option></select></label>
				<input class='button' type='submit' value='Přidat / Smazat správce' />
			</div>
		</fieldset>
	</form>
</div>
<div class='f-bottom'></div>
\n";

echo "

<div class='f-top'></div>
<div class='f-middle'>
<form action='/diskuze/$slink/$sslink/?akce=diskuze-export&amp;c=".md5("exp-".$dItem->id."-".$dItem->owner."-".$_SESSION['uid'])."' method='post' class='f'>
	<fieldset>
	<legend class='tglr'>Export příspěvků starších než vybrané datum</legend>
	<div class='tgld'>
	<label for='ed-rok'><span>Rok</span><select id='ed-rok' name='export_rok' type='text'><option value=''> - - - - - </option>";
	$old = date("Y");
	$actual = array();
	$actual['year'] = (int)date("Y",$time);
	$actual['day'] = (int)date("j",$time);
	$actual['month'] = (int)date("n",$time);
	do {
	  echo "<option value='$old'".($old==$actual['year']?" selected='selected'":"").">$old</option>";
	  $old--;
	} while ($old >= 2006);
	echo "</select></label>
	<label for='ed-mesic'><span>Měsíc</span><select id='ed-mesic' name='export_mesic' type='text'><option value=''> - - - - - </option>";
	$old = array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");
	for ($i=1;$i<=count($old);$i++) {
	  echo "<option value='$i'".($i==$actual['month']?" selected='selected'":"").">($i) ".$old[$i-1]."</option>";
	}
	echo "</select></label>
	<label for='ed-den'><span>Den</span><select id='ed-den' name='export_den' type='text'><option value=''> - - - - - </option>";
	for ($i=1;$i<=31;$i++) {
	  echo "<option value='$i'".($i==$actual['day']?" selected='selected'":"").">$i</option>";
	}
	echo "</select></label>
	<input class='button' type='submit' value='Provést export' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/diskuze/$slink/$sslink/?akce=diskuze-clear&amp;c=".md5("clear-".$dItem->id."-".$dItem->owner."-".$_SESSION['uid'])."' method='post' class='f'>
	<fieldset>
	<legend class='tglr'>Promazání textů fóra diskuze starších než:</legend>
	<div class='tgld'>
	<label for='d-rok'><span>Rok</span><select id='d-rok' name='clr_rok' type='text'><option value=''> - - - - - </option>";
	$old = date("Y");
	do {
	  echo "<option value='$old'>$old</option>";
	  $old--;
	} while ($old >= 2006);
	echo "</select></label>
	<label for='d-mesic'><span>Měsíc</span><select id='d-mesic' name='clr_mesic' type='text'><option value=''> - - - - - </option>";
	$old = array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");
	for ($i=1;$i<=count($old);$i++) {
	  echo "<option value='$i'>($i) ".$old[$i-1]."</option>";
	}
	echo "</select></label>
	<label for='d-den'><span>Den</span><select id='d-den' name='clr_den' type='text'><option value=''> - - - - - </option>";
	for ($i=1;$i<=31;$i++) {
	  echo "<option value='$i'>$i</option>";
	}
	echo "</select></label>
	<input class='button' type='submit' value='Smazat starší texty' />
	</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
	<form action='/diskuze/$slink/$sslink/?akce=diskuze-vlastnictvi' method='post' class='f'>
		<fieldset>
			<legend class='tglr'>Vlastnictví diskuzního tématu</legend>
			<div class='tgld'>
				<label for='vlastnik'><span>Nový vlastník</span><input id='vlastnik' type='text' name='novy_vlastnik' size='20' maxlength='40' value='$dItem->vlastnik' /></label>
				<label for='vlastnik2'><span>Vlastník znovu</span><input id='vlastnik2' type='text' name='novy_vlastnik2' size='20' maxlength='40' value='' /></label>
				<input class='button' type='submit' value='Předat vlastnictví' />
			</div>
		</fieldset>
	</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/diskuze/$slink/$sslink/?akce=diskuze-smazat' method='post' class='f'>
	<fieldset>
		<legend class='tglr'>Smazání / zamčení diskuze</legend>
		<div class='tgld'>
			<label for='smazat-tema'><span>Akce</span><select id='smazat-tema' name='akce_tema'>
				<option value=''> - - - - - </option>
				<option value='unlock'>Odemknout</option>
				<option value='lock'>Zamknout</option>
				<option value='' disabled='disabled'></option>
				<option value='delete'>Smazat diskuzní téma</option>
			</select></label>
			<input class='button' type='submit' value='Provést' />
		</div>
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>
\n";
				}
				
				$pravaUseru = mysql_query ("SELECT u.login, u.login_rew, u.id, p.prava FROM 3_diskuze_prava AS p, 3_users AS u WHERE p.id_dis = '$dItem->id' AND u.id = p.id_user AND p.prava != 'moderator' ORDER BY u.login ASC, p.prava ASC");
				$uzivatelePrava = array();
				if (mysql_num_rows($pravaUseru)>0) {
					while($pItem = mysql_fetch_object($pravaUseru)){
						switch ($pItem->prava){
							case "writer": $p = "čtení i psaní";
							break;
							case "reader": $p = "jen čtení";
							break;
							case "hide": $p = "bez přístupu";
							break;
							default : $p = "čtení i psaní";
							break;
						}
						$uzivatelePrava[] = "<li><label><input type='checkbox' class='checkbox' name='prava_uzivatel[]' value='$pItem->login_rew' />&nbsp;$pItem->login&nbsp;<em>($p)</em></label></li>";
					}
					$uzivatelePrava = "<ul>\n					".join("\n					",$uzivatelePrava)."\n				</ul>";
				}
				else {
					$uzivatelePrava = "";
				}

				$doTglrScript = true;
				echo "
<div class='f-top'></div>
<div class='f-middle'>
	<form action='/diskuze/$slink/admin/?akce=diskuze-prava' method='post' class='f'>
		<fieldset>
			<legend class='tglr'>Přístupová práva k tématu</legend>
			<div class='tgld'>
				$uzivatelePrava
				<label for='new-user'><span>Nový uživatel</span><input type='text' id='new-user' name='novy_uzivatel' /></label>
				<label for='prava_nastav'><span>Práva</span>$prava_uzivatele</label>
				<input class='button' type='submit' value='Změnit / Přidat práva' />
			</div>
		</fieldset>
	</form>
</div>
<div class='f-bottom'></div>
\n";
				echo "
<div class='f-top'></div>
<div class='f-middle'>
	<form action='/diskuze/$slink/$sslink/?akce=diskuze-obecne' method='post' class='f clearer'>
		<fieldset>
			<legend class='tglr'>Obecné vlastnosti diskuzního tématu</legend>
			<div class='tgld'>";
				if ($AllowedTo === "superall" && $_SESSION['lvl'] >= 3) { // jen admin diskuznich temat muze menit nazev diskuze
					echo "				<label for='nazev'><span>Název</span><input id='nazev' type='text' name='nazev' size='20' maxlength='40' value='"._htmlspec(stripslashes($dItem->nazev))."' /></label>";
				}
				else {
					echo "				<label for='nazev'><span>Název</span><input readonly='readonly' id='nazev' type='text' name='nazev' size='20' maxlength='40' value='"._htmlspec(stripslashes($dItem->nazev))."' /></label>";
					echo "<ul><li>Název diskuze může měnit jen <a href='/admins'>administrátor</a> (moderátor) diskuzí.</li></ul>";
				}
				echo "
				<label for='popis'><span>Popis</span><input id='popis' type='text' name='popis' size='20' maxlength='255' value='"._htmlspec(stripslashes($dItem->popis))."' /></label>
				<label for='prava_registrovani'><span>Práva reg.uživ.</span>$prava_registrovani</label>
				<label for='prava_hoste'><span>Práva hosté</span>$prava_hoste</label>\n";
if ($AllowedTo == "superall" && $_SESSION['lvl'] >= 3) {
	$oblastiSql = mysql_query("SELECT id, nazev FROM 3_diskuze_groups ORDER BY nazev ASC");
	if ($oblastiSql && mysql_num_rows($oblastiSql)) {
		echo "				<label for='dis_oblast'><span>Diskuzní oblast</span><select id='dis_oblast' name='oblast'>";
		$oblastiSelected = array_fill(0, mysql_num_rows($oblastiSql)+2, "");
		$oblastiSelected[$dItem->okruh] = " selected='selected'";
		while ($oblast = mysql_fetch_row($oblastiSql)) {
			echo "<option value='".$oblast[0]."'".$oblastiSelected[$oblast[0]].">".$oblast[1]."</option>";
		}
		echo "</select></label>\n";
	}
}
echo "				<label for='nastenka'><span>Nástěnka</span><textarea id='nastenka' name='nastenka' rows='20'>"._htmlspec($dItem->nastenka)."</textarea></label>
				<input class='button' type='submit' value='Změnit údaje' />
			</div>
		</fieldset>
	</form>
</div>
<div class='f-bottom'></div>
\n";
	}

	$moderatori = mysql_query ("SELECT u.login, u.login_rew, p.id_user FROM 3_diskuze_prava AS p, 3_users AS u WHERE p.id_dis = '$dItem->id' AND p.prava = 'moderator' AND u.id = p.id_user ORDER BY u.login ASC");
	$spravciSeznam = ""; $spravciSeznam = array();
	$spravciSeznam[] = "<a href='/uzivatele/$dItem->vlastnik_rew/' class='permalink2' title='Profil vlastníka diskuzního tématu'>$dItem->vlastnik</a>";
	if (mysql_num_rows($moderatori)>0) {
		while($pItem = mysql_fetch_object($moderatori)){
			$spravciSeznam[] = "<a href='/uzivatele/$pItem->login_rew/' class='permalink2' title='Profil správce "._htmlspec($pItem->login)."'>$pItem->login</a>";
		}
	}

		echo "<div class='highlight-top'></div>
<div class='highlight-mid'>
	<table class='diskuze-one'>
		<tr><td class='diskuze-one-prvni'>Správci:</td><td>".join(", ",$spravciSeznam)."</td></tr>
		<tr><td>Popis:</td><td>";
		if (mb_strlen($dItem->popis,"UTF-8")==0){
			echo "<em>diskuze je bez popisu...</em>";
		}
		else {
			echo _htmlspec(stripslashes($dItem->popis));
		}
		echo "</td></tr>
		<tr><td colspan='2'><div>$part1</div><div>$part2</div></td></tr>
		<tr><td colspan='2'>diskuze je <em>"; if($dItem->closed=="0"){echo "odemčená";}else{echo "zamčená";} echo "</em></td></tr>
";
		if($AllowedTo != "nothing") {
			include "./add/anketa_inc.php";
		}
echo "		<tr><td colspan='2'>";
		if ($AllowedTo != "nothing"){
			echo "Nástěnka: <div class='diskuze-nastenka' style='white-space:pre-wrap;'>".spit($dItem->nastenka,1)."</div>";
		}

  	echo "</td></tr>
	</table>
</div>
<div class='highlight-bot'></div>\n";

  }

if ($dFound && strlen($dItem->vlastnik) > 1){
  //modul pro diskuzi
  if ($AllowedTo != "nothing"){
	  include "./add/dis.php";
	}
	else {
		echo '<h3 class="h3-middle"><a href="#kom">'.$diskuzeTitle.'</a></h3>
';
		echo "<a name='kom' id='kom' title='Diskuze'></a>\n";
	}
}
}

if ($doTglrScript) {
?>
<script type="text/javascript">var Accord;window.addEvent("domready",function(){Accord=new Accordion($$('.tglr').setStyle('cursor','pointer'),$$('.tgld'),{show:0,display:0,height:true,width:false,opacity:false});});</script>
<?php
}

?>
