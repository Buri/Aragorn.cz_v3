<?php

	session_start();

	include "./db/conn.php";
	@header("Content-type: text/html; charset=\""."UTF-8". "\"",true);

	if (!isset($_SESSION['uid']) && !isset($_COOKIE['overrideSipek'])) {
	  die('Log-in at Aragorn.cz required!');
	  exit;
	}

	if (isset($_COOKIE['overrideSipek']) || $_SESSION['lvl']>2) {
		if (isset($_COOKIE['overrideSipek']) || $_SESSION['lvl'] > 3) $cPrava = array(1,1);
		else {
			$selS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_admin_prava WHERE uid = $_SESSION[uid] AND chat = 1"));
			$cPrava = $selS;
		}
	}
	else {
		$cPrava = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_admin WHERE uid = '$_SESSION[uid]'"));
	}
	
	if ($cPrava[0] < 1) {
	  die('Access not granted! Insufficient role or rights to continue.');
	}

?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' />
<meta http-equiv='pragma' content='no-cache' />
<meta name='description' content='Aragorn.cz, herna nejen Drd' />
<meta name='keywords' content='Aragorn, Dračí doupě, drd, herna, povídky, články, poezie, galerie, úvahy, jeskyně, chat, diskuze, fórum, systém, orp, open role, play, online' lang='cs' />
<title>Search by IP in Users | Aragorn.cz - Dračí Doupě, RPG a fantasy online</title>
<style type="text/css">
ul,ol {
padding: 0;
margin: 0 0 0 40px;
}
ul li {
	padding: 0;
	margin: 0 0 0 20px;
}
ol li {
	padding: 0;
	margin: 0 0 0 20px;
}
ul em {
font-weight: bold;
font-style: normal;
}
</style>
</head>

<body>
<div style="float:left">
<h2>Search IP in Users</h2>
<form method="post" action="_sipek.php">
	Nickname: <input type="text" value="" name="user2search" /><br />
	<input type="submit" value="Najdi další uživatele s IP jako tento nick" />
</form>
<form method="post" action="_sipek.php">
	IP: <input type="text" value="" name="ip2search" /><br />
	<input type="submit" value="Najdi uživatele s touto IP " />
</form>
<?php
	if (isset($_POST['user2search']) && ($_POST['user2search'] != "")) {
		$user2search = addslashes($_POST['user2search']);
		$search = mysql_query("SELECT ip, login, id FROM 3_users WHERE login = '$user2search'");
		if (mysql_num_rows($search)>0) {
			$user = mysql_fetch_row($search);
			echo "<p>\n";
			echo "Uživatel <strong>" . $user[1]. "</strong> :: IP adresa: <strong>" . $user[0] . "</strong>\n";
			echo "</p>\n";
			$search2 = mysql_query("SELECT login, login_rew FROM 3_users WHERE ip = '$user[0]' AND id != $user[2]");
			if (mysql_num_rows($search2)>0) {
				echo "<h4>Uživatelé se stejnou IP adresou, jako ".$user[1]."</h4>\n";
				echo "<ol>\n";
				while ($user2 = mysql_fetch_row($search2)) {
					echo "	<li><a href='/uzivatele/$user2[1]/'>". $user2[0] ."</a></li>\n";
				}
				echo "</ol>\n";
			}
			else {
				echo "<p>Nebyli nalezeni žádní uživatelé se stejnou IP adresou</p>\n";
			}
		}
		else {
			echo "<p>Uživatel " . $_POST['user2search']. " nenalezen.</p>\n";
		}
	}
	elseif (isset($_POST['ip2search']) && ($_POST['ip2search'] != "")) {
		$ip2search = addslashes($_POST['ip2search']);
		$search = mysql_query("SELECT login, login_rew FROM 3_users WHERE ip LIKE '%$ip2search%'");
		if (mysql_num_rows($search)>0) {
			echo "<h4>Uživatelé s IP adresou: $ip2search</h4>\n";
			echo "<ol>\n";
			while ($user = mysql_fetch_row($search)) {
				echo "	<li><a href='/uzivatele/$user[1]/'>". $user[0] ."</a></li>\n";
			}
			echo "</ol>\n";
		}
		else {
			echo "<p>Nebyli nalezeni žádní uživatelé s IP adresou $ip2search</p>\n";
		}
	}
?>
</div>
<div style="float:left"><?php
unset($user);
	$chater = mysql_query("SELECT distinct(c.uid),u.ip,u.login FROM 3_chat_users AS c, 3_users AS u WHERE u.id = c.uid ORDER by u.login ASC");
	if (mysql_num_rows($chater)>0) {
		echo "<ul>";
		while ($user = mysql_fetch_row($chater)) {
			echo "<li>".$user[1]." : <em>".$user[2]."</em>";
			$search3 = mysql_query("SELECT login, reg_code FROM 3_users WHERE ip = '$user[1]' AND id != $user[0] ORDER BY login ASC");
			if (mysql_num_rows($search3)>0) {
				echo "\n<ol>";
				while ($clone = mysql_fetch_row($search3)) {
					echo "<li>$clone[0]".(!!$clone[1] ? " (N)" : "")."</li>";
				}
				echo "</ol>";
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	else {
		echo "Nikdo není online na chatu.";
	}
?>
</div>
</body>
</html>
