<?php
$dbCnt = 0;

if (isset($_GET['refresh'])) {
?><!DOCTYPE html>
<html><head><meta http-equiv="Refresh" content="120;URL=/rs/?refresh" /></head>
<body></body></html>
<?php
	exit;
}

include "./rs/conf/config.inc.php";
include "./rs/add/funkce.php";
updateTimestamp();

$bodyIncluder = "";
$requireRights = array();

$handle = opendir('./rs/plug-in');

while (false!==($file = readdir($handle))) {
	if ($file != "." && $file != "..") {
		$includee = "./rs/plug-in/".$file;
		if (is_file($includee)) include ($includee);
	}
}
closedir($handle);

	$rub_in = $_GET['slink'];
	$body_func = $rub_in."_body";
	$head_func = $rub_in."_head";

	if ($_SESSION['lvl']<4) {
		$canDoThat = mysql_fetch_object(mysql_query("SELECT * FROM 3_admin_prava WHERE uid = '$_SESSION[uid]'"));
	}
	else {
		$canDoThat = mysql_fetch_object(mysql_query("SELECT * FROM 3_admin_prava WHERE uid = 0"));
	}

	if($_GET['op'] > 0 && function_exists($head_func)) {
		if ($_SESSION['lvl']<4) {
			$canDoThis = $canDoThat->$rub_in;
		}
		else $canDoThis = 1;
		if ($canDoThis || !$requireRights[$rub_in]) call_user_func($head_func,$rub_in);
	}

/*
-----------------------------------------------
	function sekce_body()
	{
			echo "BODY BODY BODY of the doc"; other things
	}

	function sekce_head()
	{
			if () ... // actions, headers and others...
	}
-----------------------------------------------
*/


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Redakční systém Aragornu</title>
<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />

<meta http-equiv="Content-language" content="cs" />
<meta http-equiv="pragma" content="no-cache" />
<meta name="robots" content="ALL,FOLLOW" />
<meta name="description" content="RS - Aragorn" />

<link rel="stylesheet" type="text/css" href="/rs/css/style.css" title="RS A3 style" />

<link rel="stylesheet" type="text/css" href="http://s1.aragorn.cz/j/MooEditable/MooEditable.css">
<link rel="stylesheet" type="text/css" href="http://s1.aragorn.cz/j/MooEditable/MooEditable.Extras.css">
<link rel="stylesheet" type="text/css" href="http://s1.aragorn.cz/j/MooEditable/MooEditable.SilkTheme.css">
<link rel="stylesheet" type="text/css" href="http://s1.aragorn.cz/j/slimbox/css/slimbox.css" />

<script charset="utf-8" type="text/javascript" src="http://s1.aragorn.cz/j/slimbox/js/mootools.js"></script>
<script charset="utf-8" type="text/javascript" src="http://s1.aragorn.cz/j/slimbox/js/slimbox.js"></script>

<script charset="utf-8" type="text/javascript" src="http://s1.aragorn.cz/j/MooEditable/MooEditable.js"></script>
<script charset="utf-8" type="text/javascript" src="http://s1.aragorn.cz/j/MooEditable/MooEditable.UI.MenuList.js"></script>
<script charset="utf-8" type="text/javascript" src="http://s1.aragorn.cz/j/MooEditable/MooEditable.Extras.js"></script>

</head>
<body>

<div class="holder">

<h1><a href="/rs/" title="Redakční systém Aragornu">Redakční systém Aragornu</a></h1>
<h2 class='aragorncz'><a href='/' title='Zpět na Aragorn.cz'>Zpět na Aragorn.cz</a></h2>
<div class="menu">
	<ul>
<?php

natsort($menuLinks);

foreach ($menuLinks as $key => $value) {
	$canMenu = false;
	if ($_SESSION['lvl']<4){
		if ($canDoThat->$key) $canMenu = true;
	}
	else $canMenu = true;
	if ($canMenu || !$requireRights[$key]) {
		if (isset($_GET['slink']) && $key == $_GET['slink'])
			echo "\t\t<li><a class='selected' href='/rs/$key/'>$value</a></li>\n";
		else echo "\t\t<li><a href='/rs/$key/'>$value</a></li>\n";
	}
}
			ob_flush();
?>
  </ul>
  
  <span><?php echo $_GET["slink"]; ?></span>
</div>
<?php
	ob_flush();
?>
<div class="content">
  <div class="content-in">

<?php

if ($_GET['slink']!="" && function_exists($body_func)) {
	if ($_SESSION['lvl']<4) {
		$canDoThis = $canDoThat->$rub_in;
	}
	else $canDoThis = 1;
	if ($canDoThis || !$requireRights[$rub_in]) call_user_func($body_func, $rub_in);
	else {
		echo "<big>Do této sekce Redakčního systému nemáte přístup.</big>";
	}
}
else {
?>
<p>
	<big>Tak co to bude?</big>
</p>
<p>Třeba seznam pravidelných měsíčních činností?</p>
<ul>
	<li<?php if(date("j")<10)echo " class='error'";?>>Galerie - Aktualizovat dílo měsíce</li>
	<li>Blog - napsat alespoň jeden úvodník shrnující dění na Aragornu</li>
</ul>
<p>Máme <a href="http://apophis.aragorn.cz/rozcesti_nove.html" target="_blank">nové situace</a> do Rozcestí, tak šup s nima do Systému, když už pro to máme v Redakčním Systému sekci!</p>
<?php
}

?>

  </div>
</div>

</div>
<script type="text/javascript">
/* <![CDATA[ */
document.write('<img src="http://toplist.cz/dot.asp?id=40769&amp;http='+escape(document.referrer)+'&amp;wi='+escape(window.screen.width)+'&amp;he='+escape(window.screen.height)+'&amp;cd='+escape(window.screen.colorDepth)+'&amp;t='+escape(document.title)+'" width="1" height="1" border="0" alt="TOPlist" />'); 

function conf(link){
 r = confirm("Jste si jist(a)?")
 if (r == true){
  window.location.href = link
 }
}

/* ]]> */
</script>
<div class="footline"><?php echo $dbCnt;?> queries</div>
<a rel="nofollow" href="http://www.toplist.cz/stat/40769"><script type="text/javascript">
/* <![CDATA[ */
	document.write('<img src="http://toplist.cz/dot.asp?id=40769&amp;http='+escape(document.referrer)+'&amp;wi='+escape(window.screen.width)+'&amp;he='+escape(window.screen.height)+'&amp;cd='+escape(window.screen.colorDepth)+'&amp;t='+escape(document.title)+'" width="1" height="1" border="0" alt="TOPlist" />'); 
/* ]]> */
</script></a><noscript><img src="http://toplist.cz/dot.asp?id=40769" border="0" alt="TOPlist" width="1" height="1" /></noscript>
<div id="ifrm"><iframe="/rs/?refresh" height="1" width="1"></iframe></div>
</body>
</html>
