<?php
// if (time() < mktime(23, 59, 59, 1, 18, 2009)) {
date_default_timezone_set('Europe/Prague');
if (time() > mktime(21, 50, 00, 3, 30, 2011) && $_SERVER['SERVER_ADDR'] == '89.187.146.13') {
header('Content-Type: text/html; charset=windows-1250');
?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html lang="cs" xml:lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=windows-1250' />
<title>Přesun | Aragorn.cz - Draci Doupe (DrD), RPG a dalsi fantasy online obsah</title>
<meta http-equiv='pragma' content='no-cache' />
<meta name="robots" content="noindex, nofollow" />
<script type="text/javascript">var _gaq = _gaq || [];  _gaq.push(['_setAccount', 'UA-28138327-1']);  _gaq.push(['_setDomainName', 'aragorn.cz']);  _gaq.push(['_trackPageview']);  (function() {    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  })();</script>
</head>
<body style="position:absolute;top:0;left:0;width:100%;height:100%;margin:0;padding:0;background:white;overflow:hidden;text-align:center;">
<h1 style="position:absolute;top:50%;width:100%;padding:0;font-family:lucida,sans-serif;display:block;text-align:center;color:#E2F0FF;font-size:60px;left:0;margin-top:-50px;">Upozornění!</h1>
<p style="text-align:left;font-size:14pt;width:600px;color:#4682B4;margin:-100px -300px;position:absolute;top:50%;left:50%;">
Právě probíhá plánovaný upgrade serveru Aragorn.cz (budeme bezpečnější, rychlejší, lepší ;-D). Aragorn.cz bude dostupný ihned, jakmile bude vše dokončeno a proběhne upravení záznamů o&nbsp;změně DNS u&nbsp;všech poskytovatelů připojení.<br /><br />
Děkujeme za pochopení<br />
<small style="padding:5px 0 0 0;display:block;font-size:75%;color:#00CC00">Přesun byl zahájen 30.3.2011 v 21:55 SELČ (aktuální čas: <?php echo date("j.n.Y H:i:s");?>, ip: <?php echo $_SERVER['SERVER_ADDR'];?>).</small></p>
</body>
</html>
<?php
}
else include "index_o.php";
?>
