<?php

if ($_SESSION['lvl'] > 3) {
	$menuLinks['prava'] = "Práva adminů";
}

function prava_head($rub) {
	global $dbCnt;
if ($_SESSION['lvl']<4) return;

	$typPrav = array("clanky"=>"Články","chat"=>"Administrace chatu","galerie"=>"Galerie","herna"=>"Herna","hernaneakt"=>"Neaktivní jeskyně","blog"=>"Admin blog","post"=>"Systémová pošta","ban"=>"Banáni","doplnky"=>"Doplňky","ankety"=>"Ankety","reklamy"=>"Reklamy na chatu","redaktorina"=>"Redaktořina");

switch ($_GET['op']) {
	case 1:
		$id = addslashes($_GET['uid']);
		$countEr = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = '$id'"));
		$dbCnt++;
		if ($countEr[0]>0) {
			if (mb_strlen($_POST['jmeno'])>1) {
				$USERname = addslashes($_POST['jmeno']);
				$userSrc = mysql_query("SELECT id,level FROM 3_users WHERE login_rew = '$USERname'");
				$dbCnt++;
				if (mysql_num_rows($userSrc)>0) {
					$user = mysql_fetch_object($userSrc);
					if ($user->level==3) {
						$o = array("uid='$user->id'");
						foreach ($typPrav as $k=>$v) {
							if (isset($_POST['prava-'.$k])){
								if ($k != "redaktorina") {
									$o[] = "$k='1'";
								}
								else {
									$o[] = "$k='".addslashes(serialize(array_map("intval",$_POST['prava-'.$k])))."'";
								}
							}
							else {
								if ($k == "redaktorina") {
									$o[] = "$k=''";
								}
								else {
									$o[] = "$k='0'";
								}
							}
						}
						$o = join(",",$o);
						$i = mysql_query("UPDATE 3_admin_prava SET $o WHERE uid = '$id'");
						$dbCnt++;
						if (mysql_affected_rows()) $info = 1;
						else $info = 6;//6
					}else $info = 6;// 6
				}else $info = 6;// 6
			}else $info = 4;// 4
		}else $info = 8;
	break;
	case 2:
		if (mb_strlen($_POST['jmeno'])>1) {
			$USERname = addslashes($_POST['jmeno']);
			$userSrc = mysql_query("SELECT id,level FROM 3_users WHERE login_rew = '$USERname'");
			$dbCnt++;
			if (mysql_num_rows($userSrc)>0) {
				$user = mysql_fetch_object($userSrc);
				if ($user->level==3) {
					$o = array("uid"=>"uid");
					$w = array("uid"=>"$user->id");
					foreach($typPrav as $k=>$v) {
						if (isset($_POST['prava-'.$k])) {
							if ($k == "redaktorina") {
								$w[$k] = addslashes(serialize(array_map("intval",$_POST['prava-'.$k])));
							}
							else {
								$w[$k] = "1";
							}
							$o[$k] = $k;
						}
					}
					$o = join(", ",$o); // klice
					$w = join(", ",$w); // hodnoty
					$i = mysql_query("INSERT INTO 3_admin_prava ($o) VALUES ($w)");
					$dbCnt++;
					if ($i) $info = 3;
					else $info = 6;
				}else $info = 6;
			}else $info = 6;
		}else $info = 4;
	break;
	case 3:
		$id = addslashes($_GET['uid']);
		$countEr = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = '$id'"));
		$dbCnt++;
		if ($countEr[0]>0) {
			mysql_query("DELETE FROM 3_admin_prava WHERE uid = $id");
			$dbCnt++;
			$info = 2;
		}else {
			$info = 8;
		}
	break;
}
Header ("Location: /rs/$rub/?info=$info");
exit;
}

function prava_body() {
	global $dbCnt;
	if ($_SESSION['lvl']<4) return;

	$typPrav = array("clanky"=>"Články","chat"=>"Administrace chatu","galerie"=>"Galerie","herna"=>"Herna","hernaneakt"=>"Neaktivní jeskyně","blog"=>"Admin blog","post"=>"Systémová pošta","ban"=>"Banáni","doplnky"=>"Doplňky","ankety"=>"Ankety","reklamy"=>"Reklamy na chatu","redaktorina"=>"Redaktořina");

	if (!isSet($_GET['index'])){
		$index = 1;
	}else{
		$index = (int) ($_GET['index']);
	}

	if ($_GET['uid'] > 0){
		$fId = mysql_query ("SELECT a.*, u.login, u.login_rew FROM 3_admin_prava AS a, 3_users AS u WHERE a.uid = '".addslashes($_GET['uid'])."' AND a.uid = u.id AND a.uid != 0");
		$dbCnt++;
		if (mysql_num_rows($fId) > 0){
			$oI = mysql_fetch_object($fId);
			switch ($_GET['action']){
				case "edit":
					echo "<h2>Editace práv admina</h2>\n";
					echo "<form action='/rs/prava/?op=1&amp;uid=$_GET[uid]' method='post'>\n";
					echo "<table width='80%'>\n";
					echo "<tr><td width='20%'>Admin</td><td>$oI->login <input type='hidden' value='".stripslashes($oI->login_rew)."' size='100' name='jmeno' /></td></tr>\n";
					echo "<tr><td width='20%'>Práva</td><td>\n";
					foreach ($typPrav as $k=>$v){
						if ($k != "redaktorina") {
							echo "<label for='prava-$k'><input type='checkbox' id='prava-$k' name='prava-$k' value='1'".(($oI->$k == 1)? " checked='checked'" : '')." /> $v</label><br />\n";
						}
						else {
							$vars = getSekceAll();
							echo "Redakce (vyžaduje nastavená práva <strong>Články</strong>)<br />";
							$oI->$k = ($oI->$k ? unserialize($oI->$k) : array());
							$a = $oI->$k;
							foreach($vars as $kk=>$vv) {
								echo "<label for='prava-$k-$kk'><input type='checkbox' id='prava-$k-$kk' name='prava-".$k."[".$kk."]' onclick='$(\"prava-clanky\").set(\"checked\", 1);' value='1'".((isset($a[$kk]) && $a[$kk])? " checked='checked'" : '')." />Články: $vv</label><br />\n";
							}
						}
					}
					echo "</td></tr>\n";
					echo "<tr><td colspan='2' align='center'><input type='submit' value='Provést editaci' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/prava/'\" /></td></tr>\n";
					echo "</table>\n";
					echo "</form>";
				break;
			}
		}
		else {
			echo "<span class='error'>Chyba: Id nenalezeno</span>";
		}
	}elseif ($_GET['action'] == "new"){
		echo "<h2>Nová práva admina</h2>\n";
		$adminS = mysql_query("SELECT u.login,u.login_rew FROM 3_users AS u LEFT JOIN 3_admin_prava AS p ON p.uid = u.id WHERE u.level = 3 AND u.id > 1 AND p.clanky IS NULL ORDER BY u.login_rew ASC");
		$dbCnt++;
		if ($adminS && mysql_num_rows($adminS) > 0) {
			echo "<form action='/rs/prava/?op=2' method='post'>\n";
			echo "<table width='40%'>\n";
			echo "<tr><td width='20%'>Nick admina</td><td><select name='jmeno'>\n";
			while ($aIt = mysql_fetch_object($adminS)) {
				echo "<option value='$aIt->login_rew'>$aIt->login</option>\n";
			}
			echo "</select></td></tr>\n";
			echo "<tr><td width='20%'>Práva</td><td>\n";
			foreach ($typPrav as $k=>$v){
				if ($k != "redaktorina") {
					echo "<label for='prava-$k'><input type='checkbox' id='prava-$k' name='prava-$k' value='1' /> $v</label><br />\n";
				}
				else {
					$vars = getSekceAll();
					echo "Redakce (vyžaduje nastavená práva <strong>Články</strong>)<br />";
					foreach($vars as $kk=>$vv) {
						echo "<label for='prava-$k-$kk'><input type='checkbox' id='prava-$k-$kk' name='prava-".$k."[".$kk."]' onclick='$(\"prava-clanky\").set(\"checked\", 1);' value='1' />Články - $vv</label><br />\n";
					}
				}
			}
			echo "</td></tr>\n";
			echo "<tr><td colspan='2' align='center'><input type='submit' value='Přidat práva' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/prava/'\" /></td></tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else {
			echo "<p><span class='error'>Všichni administrátoři již mají přidělená práva.</span></p>\n";
		}
	}
switch($_GET['info']){
	case 8:
		echo "<span class='error'>Error: Hledané ID neexistuje</span>";
	break;
	case 1:
		echo "<span class='ok'>Ok: Práva v pořádku editována</span>";
	break;
	case 2:
		echo "<span class='ok'>Ok: Práva smazána</span>";
	break;
	case 3:
		echo "<span class='ok'>Ok: Práva přidána</span>";
	break;
	case 4:
		echo "<span class='error'>Chyba: Jméno uživatele příliš krátké, nebo prázdné</span>";
	break;
	case 5:
		echo "<span class='error'>Chyba: Nastala chyba při mazání</span>";
	break;
	case 6:
		echo "<span class='error'>Chyba: Neexistující uživatel, uživatel bez administrátorských práv, nebo admin již má nastavená práva!</span>";
	break;
	case 7:
		echo "<span class='error'>Chyba: Špatně odesílaná práva</span>";
	break;
}
$art = mysql_query ("SELECT a.*, u.login FROM 3_admin_prava AS a, 3_users AS u WHERE a.uid = u.id AND a.uid != 0 ORDER BY u.login_rew ASC");
$dbCnt++;
$count = mysql_num_rows($art);
if ($count > 0){
	echo "<p><a href='/rs/prava/?action=new'>Přidat nová práva</a></p>\n";
	echo "<table class='list'>\n";
	echo "<tr><th>Admin</th><th width='50%'>Práva</th><th>Akce</th></tr>\n";
$i = 1;

while ($s = mysql_fetch_object($art)){

	$acc = array();
	foreach($typPrav as $k=>$v){
		if ($k == "redaktorina") {
			$vars = getSekceAll();
			$s->$k = ($s->$k ? unserialize($s->$k) : array());
			if (count($s->$k) > 0) {
				$acc[] = "(".join(" + ", array_intersect_key($vars, $s->$k)).")";
			}
		}
		else {
			if ($s->$k > 0) $acc[] = "$k";
			else $acc[] = "<del>$k</del>";
		}
	}
	$acc = join(" &nbsp; ",$acc);
	echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
	echo "<td>$s->login</td>";
	echo "<td>$acc</td>\n";
	echo "<td><a href='/rs/prava/?action=edit&uid=$s->uid' title='Editace'>Upravit</a> | <a href=\"javascript: conf('/rs/prava/?op=3&uid=$s->uid')\">Smazat</a></td>\n";
	echo "</tr>\n";
	$i++;

}
	echo "</table>";

$art = mysql_query ("SELECT u.login FROM 3_users AS u WHERE u.id > 1 AND u.level = 4 ORDER BY u.login_rew ASC");
echo "<table class='list'>\n<tr>\n<th>Plná práva = Vše + Bonus + Práva adminů</th><th>Moderace diskuzí (extra tabulka)</th>\n</tr>\n<tr>\n<td>\n<ul>\n";
while ($s = mysql_fetch_object($art)){
	echo "<li>$s->login</li>\n";
}
echo "</ul>\n</td>\n";
$art = mysql_query ("SELECT u.login FROM 3_diskuze_prava AS p, 3_users AS u WHERE u.id > 1 AND u.id = p.id_user AND p.prava = 'admin' ORDER BY u.login_rew ASC");
echo "<td>\n<ul>\n";
while ($s = mysql_fetch_object($art)){
	echo "<li>$s->login</li>\n";
}
echo "</ul>\n</td>\n</tr>\n</table>";
}else{
	echo "<p>Žádná nastavená práva.</p><p><a href='/rs/prava/?action=new'>Přidat nová práva</a></p>";
}
// END BODY FUNC
}
?>
