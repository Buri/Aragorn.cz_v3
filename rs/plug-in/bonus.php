<?php

if ($_SESSION['lvl']>3) {
	$menuLinks['bonus'] = "Bonus (".getBonus().")";
}
$requireRights['bonus'] = true;

function bonus_head($rub) {
global $time,$dbCnt;
switch ($_GET['op']) {
	case 1:
		$sel = mysql_fetch_row(mysql_query("SELECT bonus_created, id, bonus_expired FROM 3_users WHERE id = $_GET[id]"));
		$dbCnt++;
		if ($sel[1] < 1){
			$info = 2;
		}elseif($_POST['cash'] < 50){
			$info = 3;
		}else{
			if ($sel[0] > 0){
				$expirace = $sel[2] + cashForTime($_POST['cash'], 1);
				$sql = "informed = 0, bonus_expired = $expirace";
				$mess = "Váš bonus byl prodloužen administrátorem $_SESSION[login].\n\nVáš bonus vyprší dne <b>";
			}else{
				$expirace = cashForTime($_POST['cash'], 0);
				$sql = "informed = 0, level = 2, bonus_created = $time, bonus_expired = $expirace";
				$mess = "Byl Vám aktivován bonus administrátorem $_SESSION[login].\nS okamžitou platností získáváte všechny výhody s ním spojené.\n\nVáš bonus vyprší dne <b>";
			}
			mysql_query ("UPDATE 3_users SET $sql WHERE id = $_GET[id]");
			$dbCnt++;
			$mess .= date("d.m.Y", $expirace)."</b>";
			sysPost($_GET['id'], addslashes($mess));
			$info = 1;
		}
	break;
	case 2:
		$rub = "bonus";
		$sel = mysql_fetch_row(mysql_query("SELECT id FROM 3_users WHERE id = $_GET[id]"));
		$dbCnt++;
		if ($sel[0] < 1){
			$info = 2;
		}else{
			$mess = "Byl Vám odebrán bonus/zrušena žádost o bonus administrátorem $_SESSION[login].\n\n$_POST[reason]";
			sysPost($_GET['id'], $mess);
			mysql_query ("UPDATE 3_users SET informed = 0, level = 0, bonus_created = 0, bonus_expired = 0 WHERE id = $_GET[id]");
			$dbCnt++;
			$info = 4;  
		}
	break;
}

	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}

	Header ("Location: /rs/$rub/?info=$info&index=$index");
	exit;
}

function bonus_body(){
	global $time,$dbCnt;
	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}

$from = ($index - 1) * ARTICLE_COUNT; //od kolikate polozky zobrazit

if ($_GET['id'] > 0){
$fId = mysql_query ("SELECT * FROM 3_users WHERE id = $_GET[id]");
$dbCnt++;

if (mysql_num_rows($fId) > 0){

$oI = mysql_fetch_object($fId);

switch ($_GET['action']){

  case "pridelit":
  echo "<h2>Přidělit/Prodloužit bonus</h2>";
  echo "<form action='/rs/bonus/?op=1&amp;id=$_GET[id]&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
    $showButton = false;
    if ($oI->level == 2) {
    	$uz = "<strong class='ok'>již bonus má</strong> :: od ".date("d.m.Y", $oI->bonus_created)." do ".date("d.m.Y", $oI->bonus_expired);
    	$showButton = true;
		}
		else {
			if ($oI->level > 2) {
				$uz = "je <span class='error'>admin</span>!";
				$showButton = false;
			}
			else if ($oI->level < 1) {
				$uz = "o bonus nezažádal(a)";
			}
			else {
				$uz = "bonus nemá";
				$showButton = true;
			}
		}
        echo "<tr><td width='20%'>Uživatel</td><td>".stripslashes($oI->login)." - $uz</td></tr>";
        if ($showButton) { echo "<tr><td width='20%'>Zaplatil (Kč)</td><td><input type='text' size='10' value='0' name='cash' maxlength='4' /></td></tr>"; }
        echo "<tr><td colspan='2' align='center'>";
				if ($showButton) { echo "<input type='submit' value='Provést' /> ";} echo "<input type='button' value='Zavřít' onClick=\"window.location.href='/rs/bonus/?index=$index'\" /></td></tr>";
    echo "</table>";
  echo "</form>";
  break;

  case "odebrat":
  echo "<h2>Odebrat bonus/Zrušit žádost</h2>";
  echo "<form action='/rs/bonus/?op=2&amp;id=$_GET[id]&amp;index=$index' method='post'>";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Uživatel</td><td>".stripslashes($oI->login)."</td></tr>";
        echo "<tr><td width='20%' valign='top'>Důvod</td><td><textarea rows='10' cols='40' name='reason'>".stripslashes($oI->text)."</textarea></td></tr>";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Odebrat' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/bonus/?index=$index'\" /></td></tr>";
    echo "</table>";
  echo "</form>";
  break;
}
}else{
  echo "<span class='error'>Chyba: Id nenalezeno</span>";
}
}

switch($_GET['info']){
  case 1:
    echo "<span class='ok'>Bonus přidělen/prodloužen</span>";
  break;
  case 2:
    echo "<span class='error'>Chyba: žadatel neexistuje</span>";
  break;
  case 3:
    echo "<span class='error'>Chyba: částka musí být alespoň 50 Kč</span>";
  break;
  case 4:
    echo "<span class='ok'>Bonus odebrán/Žádost o bonus zrušena</span>";
  break;
}
$artc = mysql_query ("SELECT count(*) FROM 3_users WHERE level > 0");
$dbCnt++;
$art = mysql_query ("SELECT id, login, level, bonus_created, bonus_expired, ico FROM 3_users WHERE level > 0 ORDER BY level, login  LIMIT $from, ".ARTICLE_COUNT);
$dbCnt++;
$count = array_shift(mysql_fetch_row($artc));

?>
<form action='/rs/bonus/' method='get'>
	<input type="hidden" name="action" value="pridelit" /><input type="hidden" name="index" value="<?php echo $index;?>" />
	<p>Variabilní symbol platby (ID uživatele): <input type='text' value='' size='10' name='id' /> <input type='submit' value='Najít' /></p>
</form>

<?php

if ($count > 0){
  echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";
  echo "<table class='list autolayout'>\n";
  echo "<tr><th>Uživatel</th><th>ID</th><th>Ikona</th><th>Expirace</th><th>Stav</th><th>Akce</th></tr>\n";
	$i = 1;

	while ($s = mysql_fetch_object($art)){
		$nazev = stripslashes($s->nazev);
		$anotace = stripslashes($s->anotace);
		if ($s->schvaleno < 1){
			$time = date("d.m." ,$s->odeslanotime);
			$acc = "<a href=\"javascript: conf('/rs/clanky/?op=3&id=$s->id&index=$index')\" title='smazat'>schválit</a> - ";
		}else{
			$time = date("d.m." ,$s->schvalenotime);
			$acc = "";
		}
		
		//expirace
		$bonAdm = "bonus";
		if ($s->level == 2 && $s->bonus_created > 0){
			$fromBon = date("d.m.Y", $s->bonus_created)." - ";
			$toBon = date("d.m.Y", $s->bonus_expired);
		}
		elseif ($s->level>2) {
			$bonAdm = "admin";
		}
			echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
			echo "<td><a href='/rs/bonus/?action=view&id=$s->id&amp;index=$index' title='Editace'>$s->login</a></td>\n";
			echo "<td width='60'>$s->id</td>\n";
			echo "<td align='center'><img src='/system/icos/$s->ico' alt='ID $s->id' title='ID $s->id' /></td>";
			echo "<td>$fromBon$toBon</td>\n";
			echo "<td class='bg".(($s->level < 2) ? 3 : 4)."'>".(($s->level < 2)? "žadatel" : "$bonAdm")."</td>\n";
			echo "<td>".(($s->level == 1) ? "<a href='/rs/bonus/?action=pridelit&id=$s->id&amp;index=$index' title='přidělit bonus'>přidělit bonus</a> <a href=\"javascript: conf('/rs/bonus/?action=odebrat&amp;id=$s->id&amp;index=$index')\" title='zrušit žádost'>zrušit žádost</a>" : "").(($s->level == 2) ? "<a href='/rs/bonus/?action=pridelit&amp;id=$s->id&amp;index=$index' title='prodloužit bonus'>prodloužit bonus</a> <a href=\"javascript: conf('/rs/bonus/?action=odebrat&amp;id=$s->id&amp;index=$index')\" title='odebrat bonus'>odebrat bonus</a>" : "");
			echo "</tr>\n";
		$i++;
		$fromBon = ""; $toBon = "";
	}

	echo "</table>";
	echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";
}else{
  echo "Nikdo nežádá o bonus.";
}
// END BODY FUNC
}
?>
