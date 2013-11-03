<?php

$menuLinks['ban'] = "Banáni";
$requireRights['ban'] = true;

function ban_head($rub) {
global $time,$dbCnt;
switch ($_GET['op']) {
	case 1:
		if ( mb_strlen($_POST['jmeno']) > 1 && isset($_POST['reason']) && mb_strlen($_POST['reason']) > 5 && isset($_POST['cas']) && $_POST['cas'] > 0) {
			$USERname = do_seo($_POST['jmeno']);
			$cas = $_POST['cas']*60*60;
			$userSrc = mysql_query("SELECT id,level,ip,login_rew FROM 3_users WHERE login_rew = '$USERname'");
			$dbCnt++;
			if (mysql_num_rows($userSrc)>0) {
				$user = mysql_fetch_object($userSrc);
				if ($user->level<3) {
					$reason = addslashes(htmlspecialchars($_POST['reason'],ENT_QUOTES,"UTF-8"));
		      mysql_query ("INSERT INTO 3_ban (uid, fid, time, assignedin, reason, ipe) VALUES ('$user->id', '$_SESSION[uid]', '$cas', '$time', '$reason', '$user->ip')");
					$dbCnt++;
    		  mysql_query ("UPDATE 3_users SET online = '0', timestamp = '0' WHERE id = '$user->id'"); //odhlasi ho
					$dbCnt++;
					mysql_query("DELETE FROM 3_long_login WHERE nick = '$user->login_rew'");
					mysql_query("DELETE FROM 3_chat_users WHERE uid = '$user->id'");
					mysql_query("DELETE FROM 3_cave_users WHERE uid = '$user->id'");
					$info = 1;
				}else {
					$info = 5;
				}
			}else {
				$info = 5;
			}
		}elseif (mb_strlen($_POST['reason'])<=5) {
			$info = 3;
		}else {
			$info = 4;
		}
	break;
	case 2:
		$id = addslashes($_GET['id']);
		$uid = addslashes($_GET['uid']);
		$countEr = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_ban WHERE id = '$id' AND uid = '$uid'"));
		$dbCnt++;
		if ($countEr[0]>0) {
			mysql_query("DELETE FROM 3_ban WHERE id = '$id'");
			$dbCnt++;
			$info = 2;
		}else {
			$info = 6;
		}
	break;
}
Header ("Location: /rs/$rub/?info=$info");
exit;
}

function ban_body() {
global $time,$dbCnt;
if ($_GET['id'] > 0){
	$fId = mysql_query ("SELECT a.*, u.login, u.ip, v.login AS adlogin FROM 3_ban AS a LEFT JOIN 3_users AS u ON a.uid = u.id LEFT JOIN 3_users AS v ON v.id = a.fid WHERE a.id = $_GET[id]");
	$dbCnt++;
	if (mysql_num_rows($fId) > 0){
		$oI = mysql_fetch_object($fId);
		$others = mysql_query("SELECT login FROM 3_users WHERE ip = '$oI->ip'");
		$dbCnt++;
		$other = "žádní";
		if (mysql_num_rows($others)>0) {
			$other = array();
			while ( $oth = mysql_fetch_row($others) ) {
				$other[]=$oth[0];
			}
			$other = join(', ',$other);
		}
		switch ($_GET['action']){
			case "view":
			echo "<h2>Zobrazení banánu</h2>";
			echo "<form action='/rs/ban/?op=2&amp;id=$_GET[id]&amp;uid=$oI->uid' method='post'>";
				echo "<table width='80%'>";
				echo "<tr><td width='20%'>Uživatel</td><td>".stripslashes($oI->login)."</td></tr>\n";
				echo "<tr><td width='20%'>Důvod</td><td>".stripslashes($oI->reason)."</td></tr>\n";
				echo "<tr><td width='20%'>Udělil</td><td>".stripslashes($oI->adlogin)."</td></tr>\n";
				echo "<tr><td width='20%'>IP</td><td>".$oI->ip." / $oI->ipe</td></tr>\n";
				echo "<tr><td width='20%'>Uživatelé se stejnou IP</td><td>$other</td></tr>\n";
				echo "<tr><td width='20%'>Časově</td><td>od ".date("d.m.Y H:i",$oI->assignedin)."&nbsp;&nbsp;&nbsp;do ".date("d.m.Y H:i",$oI->time+$oI->assignedin)."</td></tr>\n";
				echo "<tr><td colspan='2' align='center'><input type='submit' value='Zrušit banán' onclick='return confirm(\"Smazat banán?\");' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/ban/'\" /></td></tr>\n";
				echo "</table>\n";
			echo "</form>";
			break;
		}
	}
	else {
	  echo "<span class='error'>Chyba: Id nenalezeno</span>";
	}
}elseif ($_GET['action'] == "new"){
  echo "<h2>Nový banán</h2>";
  echo "<form action='/rs/ban/?op=1' method='post'>";
    echo "<table width='80%'>";
        echo "<tr><td width='20%'>Uživatel</td><td><input type='text' value='' size='40' name='jmeno' /></td></tr>\n";
        echo "<tr><td width='20%'>Důvod</td><td><textarea name='reason' cols='60' rows='3'></textarea></td></tr>\n";
        echo "<tr><td width='20%'>Trvání</td><td>";
$i = 0;
$o = "";
$banani = array(1=>"1 hodina",24=>"1 den",168=>"1 týden",720=>"1 měsíc",8640=>"trvalý");
foreach ($banani as $key => $value) {
	$o .= "<option value='$key'>$value</option>";
}
  echo "<select name='cas'>".$o."</select>";
				echo "</td></tr>\n";
        echo "<tr><td colspan='2' align='center'><input type='submit' value='Udělit banán' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/ban/'\" /></td></tr>\n";
    echo "</table>\n";
  echo "</form>";

}
switch($_GET['info']){
  case 1:
    echo "<span class='ok'>Ok: Bánán udělen</span>";
  break;
  case 2:
    echo "<span class='ok'>Ok: Banán smazán</span>";
  break;
  case 3:
    echo "<span class='error'>Chyba: Důvod udělení banánu byl příliš krátký</span>";
  break;
  case 4:
    echo "<span class='error'>Chyba: Jméno uživatele příliš krátké, nebo prázdné nebo špatně udílený banán</span>";
  break;
  case 5:
    echo "<span class='error'>Chyba: Neexistující uživatel nebo uživatel s administrátorskými právy!</span>";
  break;
  case 6:
    echo "<span class='error'>Chyba: Špatně mazaný banán</span>";
  break;
}
$art = mysql_query ("SELECT a.*, u.login, u.ip, v.login AS adlogin FROM 3_ban AS a LEFT JOIN 3_users AS u ON u.id = a.uid LEFT JOIN 3_users AS v ON v.id = a.fid ORDER BY u.login_rew ASC");
$dbCnt++;
$count = mysql_num_rows($art);
if ($count > 0){
	echo "<p><a href='/rs/ban/?action=new'>Přidat nový banán</a></p>\n";
  echo "<table class='list autolayout'>\n";
  echo "<tr><th>Oběť</th><th>Střelec</th><th>Datum udělení</th><th>Vyprší</th><th>Akce</th><th>IP</th></tr>\n";
	$i = 1;

while ($s = mysql_fetch_object($art)){

	$jmeno = stripslashes($s->login);
	$strelec = stripslashes($s->adlogin);
	$kdy = date("d.m.Y H:i",$s->assignedin);
	$delka = date("d.m.Y H:i",$s->time+$s->assignedin);

	echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
	echo "<td>$jmeno</td>";
	echo "<td>$strelec</td>";
	echo "<td>$kdy</td>\n";
	echo "<td>$delka</td>\n";
	echo "<td><a href='/rs/ban/?action=view&id=$s->id' title='Zobrazit detaily / Smazat'>Zobrazit/Smazat</a></td>\n";
	echo "<td><small>$s->ip <br /> $s->ipe</small></td>\n";
	echo "</tr>\n";
	$i++;

}
  echo "</table>";
}else{
  echo "<p>Žádné banány.</p><p><a href='/rs/ban/?action=new'>Přidat nový banán</a></p>";
}
// END BODY FUNC
}
?>
