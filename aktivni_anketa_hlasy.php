<?php
$anketa = 0;
session_start();

if (session_is_registered("uid") && isset($_GET['diskuze'])) {
	$diskuze = addslashes(intval($_GET['diskuze']));
	include "./db/conn.php";
	$diskuze = addslashes($_GET['diskuze']);
	$moznosti = array();
	$jeAnketa = mysql_query("SELECT a.*, t.nazev AS diskuzenazev FROM 3_ankety AS a, 3_diskuze_topics AS t WHERE a.dis = t.id AND a.aktiv = '1' AND t.nazev_rew = '$diskuze'");
	if (mysql_num_rows($jeAnketa)>0) {
		$anketaO = mysql_fetch_object($jeAnketa);
		$anketa = $anketaO->id;
		mysql_free_result($jeAnketa);
		$moznosti = explode(">", $anketaO->odpoved);
		$hlasy = array();
		foreach ($moznosti as $k=>$val) {
			$hlasy[$k] = array();
		}
	}
}

if ($anketa == 0 && isset($_SESSION['lvl']) && $_SESSION['lvl'] > 2) {
	include "./db/conn.php";
	$aktivniAnkety = mysql_query("SELECT t.nazev_rew, t.nazev, a.otazka FROM 3_ankety AS a, 3_diskuze_topics AS t WHERE t.id = a.dis AND a.aktiv = '1' ORDER BY 1 ASC");
	if (mysql_num_rows($aktivniAnkety) > 0) {
	echo "<html>
<head>
<title>Aktivní anketky :)</title>
</head>
<style type='text/css'>
/* <![CDATA[ */
	html,body {padding:0;margin:0;border:none;color:#000;background:#fff;font-size:70%;}
  body {margin-top:10px;font-size:100%;font-family:Tahoma,Arial,sans-serif;}
  ul{font-size:1.0em;line-height:1.2em;}
  ul li {padding-top:2px;padding-bottom:2px;}
  a,a:visited{color:#666;text-decoration:underline;}
  a:hover{color:#333;}
  .fl{float:left;width:33%;}
  .nowrap{white-space:nowrap;}
/* ]]> */
</style>
<body>
<div class='fl'><ul>\n";
		$cnt = 0;
		$delic = ceil(mysql_num_rows($aktivniAnkety) / 3);
		while ($aktivniAnketa = mysql_fetch_row($aktivniAnkety)) {
			$cnt++;
			echo "<li><a href='/aktivni_anketa_hlasy.php?diskuze=$aktivniAnketa[0]'>$aktivniAnketa[2]</a> :: diskuze&nbsp;<a class='nowrap' href='/diskuze/$aktivniAnketa[0]/'>$aktivniAnketa[1]</a></li>\n";
			if ($cnt > $delic) {
				echo "</ul></div>\n<div class='fl'><ul>\n";
				$cnt = 0;
			}
		}
		echo "</ul></div>\n";
	}
}
else if ($anketa > 0) {
	echo "<html>
<head>
<title>Průběžné výsledky - jak kdo hlasoval - v aktuálně aktivní anketě diskuze $anketaO->diskuzenazev</title>
<style type='text/css'>
/* <![CDATA[ */
  body {margin:40px;color:#000;background:#fff;font-size:12px;font-family:Tahoma,Arial,sans-serif;}
/* ]]> */
</style>
</head>
<body style='margin:40px;color:#000;background:#fff'>
<h2>Jak kdo hlasoval v aktuálně aktivní anketě diskuze $anketaO->diskuzenazev</h2>
<p>Otázka: <strong>$anketaO->otazka</strong></p>\n";
	$pocty = mysql_query("SELECT u.login, a.hlas FROM 3_ankety_data AS a LEFT JOIN 3_users AS u ON u.id = a.uid WHERE a.ank_id = '$anketa' ORDER BY 1 ASC");
	$hlasyAll = 0;
	if (mysql_num_rows($pocty)>0) {
		while ($hlasOne = mysql_fetch_object($pocty)) {
			$hlasy[$hlasOne->hlas][] = $hlasOne->login;
		}
		mysql_free_result($pocty);
		echo "<ul>\n";
		for ($i=0,$count=count($hlasy);$i<$count;$i++) {
			echo "<li><strong>$moznosti[$i]</strong>\n";
			if (count($hlasy[$i]) > 0) echo "<ol><li>".join("</li>\n<li>",$hlasy[$i])."</li>\n</ol>\n";
			else echo "<em>nikdo nehlasoval</em>\n";
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	else {
		echo "<p>V této anketě nikdo nehlasoval!</p>\n";
	}
}
else {
	echo "<html>
<head>
<title>Žádná aktivní anketa nenalezena - chyba ".time()."</title>
<style type='text/css'>
/* <![CDATA[ */
  body {margin:40px;color:#000;background:#fff;font-size:12px;font-family:Tahoma,Arial,sans-serif;}
/* ]]> */
</style>
</head>
<body style='margin:40px;color:#000;background:#fff'>
<h2>V diskuzi <em>$anketaO->diskuzenazev</em> není aktivní anketa. Žádné průběžné náhledy či výsledky.</h2>\n";
}
echo "</body>\n</html>\n";
?>
