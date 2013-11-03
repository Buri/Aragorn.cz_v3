<?php
include(__DIR__ . "/db/conn.php");
include(__DIR__ . "/add/funkce.php");

$time = time();

$bonusTop = $time + 60*60*24*14;
$bonusBot = $time + 60*60*24*13;

$users2out = mysql_query("SELECT id,bonus_expired,login FROM 3_users WHERE informed = 0 AND level = 2 AND bonus_expired < $bonusTop");

/*
$users2out = mysql_query("SELECT id,bonus_expired FROM 3_users WHERE level = 2 AND
bonus_expired < $bonusTop AND
bonus_expired >= $bonusBot");
*/

$usersToInform = array();
$usersToInformLogins = array();

$updA = mysql_num_rows($users2out);
if ($updA > 0) {
	while ($user2 = mysql_fetch_row($users2out)) {
		$text = "Toto je pouze informativní zpráva od automatizovaného systému Aragornu.cz.<br />
<strong>Váš bonus vyprší ".date("j.n.Y",$user2[1])."</strong>.<br />
Pokud jej chcete prodloužit, stačí poslat částku (minimálně 50,- Kč) na účet <strong>$cisloUctuAragornu</strong> s variabilním symbolem <strong>$user2[0]</strong>.<br />
Jakmile peníze dorazí na účet, bude Vám bonus prodloužen.<br />
V opačném případě Vám bude bonus po vypršení odebrán a vy tak ztratíte veškeré výhody s ním spojené.";
		$usersToInform[] = $user2[0];
		$usersToInformLogins[] = $user2[2];
		sysPost($user2[0],addslashes($text));
	}
	mysql_query("UPDATE 3_users SET informed = 1 WHERE id IN (".join(",",$usersToInform).")");
}
echo $updA." send...".join(", ",$usersToInformLogins)."<br />";

$users1out = mysql_query("SELECT id,bonus_expired FROM 3_users WHERE level = 2 AND bonus_expired < $time AND informed = 1");
$updB = mysql_num_rows($users1out);
if ($updB > 0) {
while ($user1 = mysql_fetch_row($users1out)) {
	$text = "<strong>Váš bonus vypršel ".date("j.n.Y",$user1[1])."</strong>.
	Pokud jej chcete obnovit, stačí poslat částku (minimálně 50,- Kč)	na účet číslo <strong>$cisloUctuAragornu</strong> s variabilním symbolem <strong>$user1[0]</strong>.<br />
	Jakmile peníze dorazí na účet, bude Vám bonus obnoven.";
	sysPost($user1[0],addslashes($text));
}
}
echo $updB." DE-bonused...<br />";

mysql_query("UPDATE 3_users SET informed = 0, bonus_created = 0, bonus_expired = 0, level = 1 WHERE bonus_expired < $time AND level = 2");
$updN = mysql_affected_rows();
echo (($updN > 0) ? $updN : 0)." updated...";

// Truncate logs
@fclose(fopen("/home/domeny/aragorn.cz/web/log/access.log", "w"));
@fclose(fopen("/home/domeny/aragorn.cz/web/log/error.log", "w"));

?>
