<?php
$noOutputBuffer = true;
include_once "./db/conn.php";
$id = addslashes($_GET['slink']);
$nazev = "";
$b = mysql_query("SELECT nazev FROM 3_herna_all WHERE nazev_rew = '$id' AND schvaleno = '1'");
if (mysql_num_rows($b)>0) {
	$cave = mysql_fetch_row($b);
	$nazev = " - ".stripslashes($cave[0]);
}
else {
	die("<html><title>Error | Aragorn.cz</title></head><body><p><big>Hledana jeskyne neexistuje. Aragorn Vas nemohl prihlasit na jeskynni chat.</big></p>
<p><big>Pokracovat muzete na <a href='/' title='Uvodni stranka serveru Aragorn.cz'>hlavni strance</a> serveru Aragorn.cz.</big></p>
</body></html>");
}
// ěščřžýáíé
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>AraCave<?php echo $nazev; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $inc;?>/favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-28138327-1']);_gaq.push(['_setDomainName', 'aragorn.cz']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();</script>
</head>
<frameset border="0" framespacing="0" cols="*,145" frameborder="0">
	<frameset rows="*,100">
		<frame name="game" src="/cave-c/game.php?<?php echo "id=$id"; ?>" noresize="noresize" frameborer="0">
		<frame name="play" src="/cave-c/play.php?<?php echo "id=$id"; ?>" noresize="noresize" frameborer="0">
	</frameset>
	<frame name="menu" src="/cave-c/menu.php?<?php echo "id=$id"; ?>" noresize="noresize" scrolling="no" frameborer="0">
	<noframes>
	Tato stranka obsahuje framy, ktere vas prohlizec bohuzel nepodporuje.
	</noframes>
</frameset>
</html>
