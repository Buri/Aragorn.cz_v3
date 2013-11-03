<?php
$noOutputBuffer = true;
include_once "./db/conn.php";
$id = addslashes($_GET['slink']);
$nazev = "";
$b = mysql_query("SELECT nazev FROM 3_chat_rooms WHERE id = '$id' AND id != '1'");
if (mysql_num_rows($b)>0) {
	$cave = mysql_fetch_row($b);
	$nazev = " - ".stripslashes($cave[0]);
}
else {
	die("<html><title>Error | Aragorn.cz</title></head><body><p><big>Hledana mistnost neexistuje. Aragorn Vas nemohl prihlasit na chat.</big></p>
<p><big>Pokracovat muzete na <a href='/' title='Uvodni stranka serveru Aragorn.cz'>hlavni strance</a> serveru Aragorn.cz.</big></p>
</body></html>");
}
// ěščřžýáíé
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>Arachat<?php echo $nazev; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $inc;?>/favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-28138327-1']);_gaq.push(['_setDomainName', 'aragorn.cz']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();</script>
</head>
<frameset border="0" framespacing="0" cols="*,145" frameborder="0" style="width:100%;height:100%;overflow:auto;">
  <frameset rows="*,100" border="0" framespacing="0" frameborder="0">
    <frame name="game" src="/chat/game.php?<?php echo "id=$id"; ?>" border="0" framespacing="0" noresize="noresize" scrolling="auto" style="overflow:auto;" frameborer="0">
	  <frame name="play" src="/chat/play.php?<?php echo "id=$id"; ?>" border="0" framespacing="0" noresize="noresize" frameborer="0">
  </frameset>
  <frame name="menu" src="/chat/menu.php?<?php echo "id=$id"; ?>" border="0" framespacing="0" noresize="noresize" scrolling="no" frameborer="0">
  <noframes>
  Tato stranka obsahuje framy, ktere vas prohlizec bohuzel nepodporuje.
  </noframes>
</frameset>
</html>
