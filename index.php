<?php
date_default_timezone_set('Europe/Prague');
#header("Location: http://aragorn.cz/chat.html");
die('Do�asn� mimo provoz. Za zp�soben� pot�e se omlouv�me.'); 
//presmerovani
$itIsApril = ((time() > mktime(0, 0, 1, 4, 1, 2012) && time() < mktime(23, 59, 59, 4, 1, 2012)) ) ? true : false;

/*if(!$_COOKIE['showonce'] && date('d.m') == '01.04')
{
setCookie('showonce', 'yes', time()+3600*24*7);
header('Location: http://aragorn.cz/stop.html');
exit;
}*/

mb_internal_encoding("UTF-8");

if (isset($_COOKIE['style'])) {
	switch($_COOKIE['style']){
		case "megadeth-pod":case "Megadeth-PoD":case "megadethPod":case "megadethpod":
			$cookieStyle = "megadethpod";
		break;
		case "retro":case "Retro":
			$cookieStyle = "retro";
		break;
		case "resize":case "Resize-Gray":case "resizeGray":case "resizegray":
			$cookieStyle = "resizegray";
		break;
		case "jungle":case "Jungle-Time":case "jungleTime":case "jungletime":
			$cookieStyle = "jungletime";
		break;
		case "light":case "Light":
			$cookieStyle = "light";
		break;
		case "blueNight":case "Blue-Night":case "blue-night":case "bluenight":
			$cookieStyle = "bluenight";
		break;
		case "gallery":case "Gallery":
		default:
			$cookieStyle = "gallery";
		break;
	}
}
else {
	$cookieStyle = "gallery";
}

//cesta
$inc = 'http://' . $_SERVER['HTTP_HOST'];
if(!$inc)
	$inc = "http://".$_SERVER['HTTP_HOST'];
$zalozkyOmezeniCount = 20;

if (isset($_GET['_r'])) {
	$t = str_replace("?_r=1&", "?", $_SERVER['REQUEST_URI']);
	$t = str_replace("?_r=1", "", $t);
	$t = str_replace("_r=1&", "", $t);
	$t = str_replace("&_r=1", "", $t);
	header("Location: ".$t);
	exit;
}

//spojeni s db
require_once "./db/conn.php";

$itIsApril = ($itIsApril && isset($_SESSION['login']) && (!isset($_SESSION['chut']) || $_SESSION["chut"])) ? true : false;

if (!function_exists("_htmlspec")) { /* NEMAM SPOJENI */
	function _htmlspec($a) {
		return htmlspecialchars($a, ENT_QUOTES, "UTF-8");
	}
	echo "<style type='text/css'>body{margin:0 !important;}</style><big style='z-index: 50000; font-size: 200%; width: 100%; color: red; background-color: black; padding: 10px 0; position: absolute; top: 50px; left:0 display: block; text-align: center'>Aragorn.cz se nepřipojil k databázi a proto není funkční!</big>";
}
else { /* ALL NORMAL */
	//vlozeni cache_hitu
	require_once "./add/memcache.php";
	//vlozeni fci
	require_once "./add/funkce.php";
	//vlozeni rewrite prevodu
	require_once "./add/rewrite.php";
	//autorizace
	require_once "./add/auth.php";
	//akce
	if (isset($_GET['akce'])){
		include_once "./akce/akce.php";
	}
}
	require_once "./cache.php";
	//cache inline scripts ;-)

echo $xmlHeader;

?><!doctype html>
<html lang="cs" xml:lang="cs" xmlns="http://www.w3.org/1999/xhtml" id="ht<?php echo $cookieStyle;?>">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<meta http-equiv='Content-language' content='cs' />
<title><?php if($link==""){echo ($title != ""?$title." | ":"");}else{echo $title." | ";} ?><?php echo ($itIsApril ? 'Aragonymous.cz ' : ' Aragorn.cz'); ?> <?php echo strip_tags(aprilovyZertik($lastRandomNumber));?></title>
<meta name="revisit-after" content="2 hours" /><meta name="robots" content="index, follow" />
<meta name="author" content="Jakub Korál :: apophis, e-mail: apophis&#64;aragorn&#46;cz" />
<meta name="keywords" content="online,on-line,fantasy,komunita,Dračí doupě,DrD,RPG,herna,články,galerie,ORP,Open Role Play" lang="cs" />
<?php
	if ($GLOBAL_description != ""){
		echo '<meta lang="cs" name="description" content="'.str_replace(array('"',"<",">"), array("&apos;","&lt;","&gt;"), $GLOBAL_description).'" />';
	}?>
<meta name="verify-v1" content="Ag52d+2UaYRLwlJy5DiR+SjkptPFk0nsSdKsIKNZpeI=" />
<meta http-equiv="X-UA-Compatible" content="IE=8,chrome=1" />
<meta http-equiv="Content-Script-Type" content="text/javascript" /><meta http-equiv="Content-Style-Type" content="text/css" />
<?php
if ($slink == "") {
	switch ($link) {
		default:
			echo '<link rel="alternate" title="Aragorn.cz RSS" href="'.$inc.'/rss/" type="application/rss+xml" />
';
		break;
		case "diskuze":
			echo '<link rel="alternate" title="Aragorn.cz RSS - Nové diskuze" href="'.$inc.'/rss/diskuze/" type="application/rss+xml" />
';
		break;
		case "herna":
			echo '<link rel="alternate" title="Aragorn.cz RSS - Nové jeskyně" href="'.$inc.'/rss/herna/" type="application/rss+xml" />
';
		break;
		case "galerie":
			echo '<link rel="alternate" title="Aragorn.cz RSS - Nové obrázky" href="'.$inc.'/rss/galerie/" type="application/rss+xml" />
';
		break;
		case "clanky":
			echo '<link rel="alternate" title="Aragorn.cz RSS - Nové články" href="'.$inc.'/rss/clanky/" type="application/rss+xml" />
';
		break;
	}
}
?>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $inc;?>/favicon.ico" />
<link rel="stylesheet" media="print" type="text/css" href="http://s1.aragorn.cz/c/printing.css" />
<?php
	if ($itIsApril) {
?><link rel="stylesheet" media="screen, presentation" type="text/css" href="http://s1.aragorn.cz/c/<?php echo getCachedFile("css","blue-night-n|gallery-n|jungle-n|light-n|megadeth-pod-n|resize-n|retro-n|april","389b","css");?>" />
<?php
	}
	else {
?>
<link rel="stylesheet" media="screen, presentation" type="text/css" href="http://s1.aragorn.cz/c/<?php echo getCachedFile("css","blue-night-n|gallery-n|jungle-n|light-n|megadeth-pod-n|resize-n|retro-n","389b","css");?>" />
<?php
	}
?>
<script type="text/javascript" charset="utf-8" src="http://s1.aragorn.cz/j/<?php echo getCachedFile("js","mootools-core|mootools-more","101moo","js");?>"></script>
<?php
	if (!$LogedIn) {
?>
<script type="text/javascript" charset="utf-8" src="http://s1.aragorn.cz/j/<?php echo getCachedFile("js","main|styleswitcher|floatmenu|md5|hyphenator","101endhash","js");?>"></script>
<?php
	}
	else {
?>
<script type="text/javascript" charset="utf-8" src="http://s1.aragorn.cz/j/<?php echo getCachedFile("js","main|styleswitcher|floatmenu|hyphenator","101end","js");?>"></script>
<?php
	}


	if ($LogedIn && $link == "clanky" && ($slink == "my" || $slink == "new")) {
?>
<script type="text/javascript" src="http://s1.aragorn.cz/j/tiny_mce/tiny_mce.js"></script>
<!-- TinyMCE -->
<script charset="utf-8" type="text/javascript">
/* <![CDATA[ */
      
function trimSaveContent(element_id, html, body) {
    html = html.replace(/<!--.*?-->/g,'');
    return html;
}
      
tinyMCE.init({
        language : "cs",
        mode : "exact",
        elements : "mess",
        theme : "advanced",
<?php
	if ($cookieStyle == "resizegray" || $cookieStyle == "megadethpod") echo "        skin : \"o2k7\",\n        skin_variant : \"black\",\n";
	if ($cookieStyle == "retro") echo "        skin : \"retro\",\n";
?>        
        plugins : "fullscreen,preview", 
        valid_elements : "br,strong/b,em/i,ul,ol,li,h4,h5,a[!href|title|target=_blank]",
        inline_styles : false,
        // convert p to br
        force_br_newlines : true,
        force_p_newlines : false,
        forced_root_block : '', // Needed for 3.x

        save_callback : "trimSaveContent",

        // Theme options - button# indicated the row# only
        theme_advanced_buttons1 : "newdocument,cleanup,undo,redo,|,bold,italic,removeformat,|,bullist,numlist,|,fullscreen",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "center",
        theme_advanced_statusbar_location : "none",
        theme_advanced_resizing : true    
});
/* ]]> */
</script>
<!-- /TinyMCE -->
<?php
	}
	elseif ($link == 'galerie') {
		echo '<link rel="stylesheet" type="text/css" href="http://s1.aragorn.cz/j/slimbox/css/slimbox.css" />
<script charset="utf-8" type="text/javascript" src="http://s1.aragorn.cz/j/slimbox/js/slimbox.js"></script>
';
	}
?>
<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="http://s1.aragorn.cz/c/ie6.css" /><![endif]-->
<script type="text/javascript">var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-28138327-1']);/*_gaq.push(['_setDomainName', 'aragorn.cz']);*/_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();
</script>
<script type="text/javascript" src="http://s1.aragorn.cz/j/textarea.js"></script>
</head><?php ob_flush();?>
<body class='<?php if($LogedIn){echo "js ";} echo $cookieStyle;?>'>
<div id="dhtmltooltip"></div>
<script src='http://s1.aragorn.cz/j/dhtmltip.js' type='text/javascript'></script>
<?php
	if ($LogedIn) {
		if ($_SESSION['first'] == true) {
			$_SESSION['first'] = false;
		}
	}
?>
<div class='holder'>

<div class='top'>
	<h1><a href='/' title='Aragorn.cz'><span><strong class='hide'><?php echo $title;?> - </strong><?php echo ($itIsApril ? 'Aragonymous.cz': 'Aragorn.cz'); ?> <?php echo aprilovyZertik($lastRandomNumber);?></span></a></h1>
</div>

<hr class="hide" />

<div class='menu'>
<a name="navigace_menu" class="hide">Navigace - Menu</a>
<?php
	if ($itIsApril){
?>
	<ul>
	<?php if ( $LogedIn == false ){ ?>
	<li id='men1' class='men-reg'><a href='/registrace/' title='Šalin karta'<?php echo $marked[0]; ?>>Šalin karta</a></li>
	<?php }else{ ?>
	<li id='men1' class='men-set'><a href='/nastaveni/' title='Poštelovat'<?php echo $marked[0]; ?>>Poštelovat</a></li>
	<?php } ?>
	<li id='men2'><a href='/herna/' title='Xbox / PS3'<?php echo $marked[1]; ?>>Xbox / PS3</a></li>
	<li id='men3'><a href='/diskuze/' title='Krafárna'<?php echo $marked[2]; ?>>Krafárna</a></li>
	<li id='men4'><a href='/clanky/' title='Pisálkova suť'<?php echo $marked[3]; ?>>Pisálkova suť</a></li>
	<li id='men5'><a href='/galerie/' title='Omalovánky'<?php echo $marked[4]; ?>>Omalovánky</a></li>
	<li id='men6'><a href='/napoveda/' title='Když nevíš'<?php echo $marked[5]; ?>>Když nevíš</a></li>
	<li class='hide'><a href='/uzivatele/' title='Parchanti'>Parchanti</a></li>
	</ul>
<?php
	}
	else {
?>
	<ul>
	<?php if ( $LogedIn == false ){ ?>
	<li id='men1' class='men-reg'><a href='/registrace/' title='Registrace'<?php echo $marked[0]; ?>>Registrace</a></li>
	<?php }else{ ?>
	<li id='men1' class='men-set'><a href='/nastaveni/' title='Nastavení'<?php echo $marked[0]; ?>>Nastavení</a></li>
	<?php } ?>
	<li id='men2'><a href='/herna/' title='Herna'<?php echo $marked[1]; ?>>Herna</a></li>
	<li id='men3'><a href='/diskuze/' title='Diskuze'<?php echo $marked[2]; ?>>Diskuze</a></li>
	<li id='men4'><a href='/clanky/' title='Články'<?php echo $marked[3]; ?>>Články</a></li>
	<li id='men5'><a href='/galerie/' title='Galerie'<?php echo $marked[4]; ?>>Galerie</a></li>
	<li id='men6'><a href='/napoveda/' title='Nápověda'<?php echo $marked[5]; ?>>Nápověda</a></li>
	<li class='hide'><a href='/uzivatele/' title='Uživatelé'>Uživatelé</a></li>
	</ul>
<?php
	}
?>
</div>

<hr class="hide" />
<?php ob_flush(); ?>
<div class='content'><div class='topframe'></div><div class='frame'><div class='frame-in'>
<?php
//obsah webu

if ($uvodniky == true) {
	include "./sekce/uvod.php";
}
else {

switch($link){

//registrace
case "registrace":
	if ( $LogedIn == false ){
		include "./sekce/registrace.php";
		}else{
		include "./sekce/zakaz.php";
		}
break;

case "uspesna-registrace":
		if ( $LogedIn == false ){
		include "./sekce/uspesna_registrace.php";
		}else{
		include "./sekce/zakaz.php";
		}
break;

case "potvrzeni-registrace":
		if ( $LogedIn == false ){
		include "./sekce/potvrzeni_registrace.php";
		}else{
		include "./sekce/zakaz.php";
		}
break;

//vypis uzivatelu
case "uzivatele":
		include "./sekce/uzivatele.php";
break;

//nastaveni
case "nastaveni":
		if ( $LogedIn == true ){
		include "./sekce/nastaveni.php";
		}else{
		include "./sekce/zakaz2.php";
		}
break;

//bonus
case "bonus":
		if ( $LogedIn == true ){
		include "./sekce/bonus.php";
		}else{
		include "./sekce/zakaz2.php";
		}
break;

//posta
case "posta-old":
		if ( $LogedIn == true ){
		include "./sekce/posta.php";
		}else{
		include "./sekce/zakaz2.php";
		}
break;

//posta-new
case "posta":
		include "./sekce/posta_new.php";
break;

//timeout
case "timeout":
		if ( $LogedIn == false ){
		include "./sekce/timeout.php";
		}else{
		include "./sekce/zakaz.php";
		}
break;

//administratori
case "admins":
case "administratori":
		include "./sekce/administratori.php";
break;

//galerie
case "galerie":
		include "./sekce/galerie.php";
break;

//clanky
case "clanky":
		include "./sekce/clanky.php";
break;

case "clanky-test":
		include "./sekce/clanky_test.php";
break;

//diskuze
case "diskuze":
case "diskuse":
		$link = "diskuze";
		include "./sekce/diskuze.php";
break;

//false login
case "chybny-login":
		if ( $LogedIn == false ){
		include "./sekce/chybny_login.php";
		}else{
		include "./sekce/zakaz.php";
		}
break;

//chat
case "chat":
		include "./sekce/chat.php";
break;

//chat
case "zalozky":
		include "./sekce/zalozky.php";
break;

//napoveda
case "napoveda":
		include "./sekce/napoveda.php";
break;

//herna
case "herna":
		include "./sekce/herna.php";
break;

//uvodni stranka
default:
		include "./sekce/uvod.php";
break;

}

}

$SEZENI = $_SESSION;
session_write_close();
$xGht87FGH = "_SESSION";
$$xGht87FGH = $SEZENI;


?>
</div><div class='bottomframe'></div></div>

</div>

<hr class="hide" />

<div class='footer'><div class='footer2'>
	Dračí doupě<sup>&reg;</sup>, DrD&trade; a ALTAR<sup>&reg;</sup> jsou zapsané ochranné známky nakladatelství <a href="http://www.altar.cz/" title="Stránky nakladatelství Altar.cz">ALTAR</a><br />

	RSS Feedy: <a href="/rss/" title="RSS kanál od všeho trochu">Všehochuť</a> | <a href="/rss/diskuze/" title="RSS kanál čerstvých Diskuzních témat">Diskuzní témata</a> | <a href="/rss/herna/" title="RSS kanál právě schválených jeskyní v Herně">Jeskyně</a> | <a href="/rss/clanky/" title="RSS kanál nejnovějších Článků">Články</a> | <a href="/rss/galerie/" title="RSS kanál nových obrázků v Galerii">Obrázky</a><br />
	Aragorn.cz &copy; 2001&ndash;<?php echo date("Y");?> | <a href="/sitemap.php" title="Mapa stránek - funkční a aktuální odkazy">Mapa stránek</a><br />
	Provozovatel Aragorn.cz nezodpovídá za příspěvky čtenářů, uživatelů či přispěvatelů. Veškerý publikovaný obsah je chráněn autorskými právy vlastníků či autorů konkrétních textových či grafických děl. Jakékoliv kopírování obsahu je zakázáno.
<?php
/*
	<br />
	<a rel="nofollow" href="http://www.tvorba-webu.cz/webhosting/" title="Jak vybrat webhosting">Webhosting</a> | <a rel="nofollow" href="http://www.a-praha.com" title="Ubytování v Praze">hotely Praha: Hotel Praha</a> 
*/
?>
	<table class="bannerTable" cellspacing="0" cellpadding="0">
		<tr>
			<td><?php
			if ($LogedIn) {
				if ($_SESSION['lvl']<2) {
//					echo '<'.'iframe width="468" height="60" frameborder="0" scrolling="no" src="http://ad.adfox.cz/ppcbe?js=0&amp;format=800000000000d6be42ffffffd6be4258&amp;partner=1018&amp;stranka='.$inc._htmlspec($_SERVER["REQUEST_URI"]).'"><'.'/iframe>';
				}
			}
			else {
//				echo '<'.'iframe width="468" height="60" frameborder="0" scrolling="no" src="http://ad.adfox.cz/ppcbe?js=0&amp;format=800000000000d6be42ffffffd6be4258&amp;partner=1018&amp;stranka='.$inc._htmlspec($_SERVER["REQUEST_URI"]).'"><'.'/iframe>';
			}
			?></td>
			<td><a class="thalieIco" target="_blank" href="http://thalie.pilsfree.cz" title="Thalie - Persistentní svět hry Neverwinter Nights"><img title="Thalie - Persistentní svět hry Neverwinter Nights" src="http://s1.aragorn.cz/reklama/thalie-ico.gif" alt="Ikonka Thalie" width="88" height="31" /></a>
				<a target="_blank" href="http://www.iw.cz" title="Insect World - Online hra"><img src="http://s1.aragorn.cz/reklama/iw.gif" alt="Insect World - Online hra" width="88" height="31" /></a>
				<a target="_blank" href="http://drakkar.rpgplanet.cz/" title="Drakkar - časopis o hrách na hrdiny"><img src="http://s1.aragorn.cz/reklama/drakkar2.jpg" alt="Drakkar - časopis o hrách na hrdiny" width="88" height="31" /></a>
				<a target="_blank" href="http://www.pevnost.cz/" title="Pevnost.cz"><img src="http://s1.aragorn.cz/reklama/pevnost.gif" alt="Pevnost.cz" /></a>
				<a target="_blank" href="http://pathfinder.aragorn.cz/" title="Pathfinder RPG"><img src="http://pathfinder.aragorn.cz/img/logo.png" alt="pathfinder.aragorn.cz" style="max-height:31px;" /></a>
				</td>
		</tr>
	</table>
</div></div>

</div>

<?php

	ob_flush();

?>
<div id="chromeout"><div id="chrome">
	<a href="#navigace_menu" class="hide">&uarr; navigace</a><hr class="hide" />

<?php

			//online uzivatele
	$oU = mysql_fetch_row(mysql_query("SELECT COUNT(*),SUM(IF(c.uid IS NOT NULL,1,0)) FROM 3_users AS u LEFT JOIN 3_chat_users AS c ON c.uid = u.id AND c.odesel = '0' WHERE u.online = 1 LIMIT 1"));
	$onlineUzivatele = intval($oU[0]);
	$chCount = intval($oU[1]);
	//online pratele
	if (!$runBookmarks) {
		$addJs = $aF = $addJs2 = $addJs3 = $addCh = "";
	}
	if ($LogedIn == true){
		$addJs = "onmouseover=\"cssdropdown.dropit(this,event,'dropmenu2')\"";
		$addJs2 = "onmouseover=\"cssdropdown.dropit(this,event,'dropmenu4')\"";
		$addJs3 = "onmouseover=\"cssdropdown.dropit(this,event,'dropmenu5')\"";
		$oF = mysql_query ("SELECT u.login_rew,u.login,u.timestamp FROM 3_friends AS f, 3_users AS u WHERE f.uid = '$_SESSION[uid]' AND u.id = f.fid AND u.online = 1 AND u.timestamp <> 0 ORDER BY u.login_rew ASC");
		while($ooF = mysql_fetch_object($oF)){
			$friend_l = _htmlspec(stripslashes($ooF->login));
			$ooF->timestamp = $time - $ooF->timestamp;
			if ($ooF->timestamp < 120) {
				$profil = " (&lt; 2 minuty)";
			}
			elseif ($ooF->timestamp < 300) {
				$profil = " (&lt; 5 minut)";
			}
			elseif ($ooF->timestamp < 600) {
				$profil = " (&lt; 10 minut)";
			}
			else {
				$profil = " (".floor($ooF->timestamp/60)."+ minut)";
			}
			$aF .= "<h5><a href='/uzivatele/$ooF->login_rew/' title='".$friend_l."$profil'>$friend_l</a></h5>\n";
		}
		if ($aF==""){
			$aF = "<h5><a href='$_SERVER[REQUEST_URI]' title='Nikdo z přátel online'>Nikdo z přátel online</a></h5>\n";
		}
		//chat
		$sCh = mysql_query ("SELECT c.id, c.nazev,c.need_admin, SUM(IF(u.uid IS NOT NULL,1,0)) AS people FROM 3_chat_rooms AS c LEFT JOIN 3_chat_users AS u ON u.rid = c.id AND u.odesel = '0' GROUP BY c.id ORDER BY c.type ASC, c.nazev ASC");
		$chatCnt = 0;
		while($oCh = mysql_fetch_object($sCh)){
			$chatCnt += intval($oCh->people);
			$addCh .= "<h5><a href='/chat/?akce=chat-enter&amp;id=$oCh->id' title='Vstoupit :: "._htmlspec(stripslashes($oCh->nazev))."'".($oCh->need_admin == 1 ? " class='need_admin'" : "").">".stripslashes($oCh->nazev)." (".intval($oCh->people).")</a></h5>";
		}
//		echo "<!-- $chatCnt -->";
		
		//zalozky
		if (!$runBookmarks) {
			include "./add/bookmarks.php";
		}
	}

ob_flush();

?>
	<h3 class="hide"><a name="navigace_zalozky">Záložková navigace</a></h3>
		<ul>
			<li><h4><a id="dropmenu5head" href="/chat/" <?php echo $addJs3; ?>><?php echo $itIsApril ? 'Seznamka' : 'Chat';?> (<?php echo $chCount;?>)</a></h4></li>
		</ul>
<?php
if ($LogedIn == true) {
?>
		<div id="dropmenu5" class="dropmenudiv">
			<?php echo $addCh; ?>
		</div>
<?php
} // nemazat !!!
?>
		<ul>
			<li><h4><a id="dropmenu2head" href="/uzivatele/" <?php echo $addJs; ?>><?php echo $itIsApril ? 'Parchanti' : 'Uživatelé';?> (<?php echo $onlineUzivatele; ?>&nbsp;online)</a></h4></li>
		</ul>
<?php
if ($LogedIn == true){
?>
		<div id="dropmenu2" class="dropmenudiv">
			<?php echo $aF; ?>
		</div>
		<ul>
			<li><h4><a id="dropmenu4head" href="/zalozky/" <?php echo $addJs2; ?>><?php echo $itIsApril ? 'Srandičky' : 'Záložky';?><?php echo $addZalCount;?></a></h4></li>
		</ul>
		<div id="dropmenu4" class="dropmenudiv">
			<?php echo $zF; ?>
		</div>
<?php
} // nemazat !!!
ob_flush();
?>
		<ul>
			<li><h4><a id="dropmenu3head" rel="nofollow" href="<?php echo ($LogedIn ? '/zalozky/' : '');?>#skiny" onMouseOver="cssdropdown.dropit(this,event,'dropmenu3')"><?php echo $itIsApril ? 'Kůžička' : 'Skiny';?></a></h4></li>
		</ul>
		<div id="dropmenu3" class="dropmenudiv">
			<h5><a rel="nofollow" href="#" onClick="setStyleSheeter('Gallery');return false;">Galéria et Vellum</a></h5>
			<h5><a rel="nofollow" href="#" onClick="setStyleSheeter('Megadeth-PoD');return false;">Megadeth: Prince of Darkness</a></h5>
			<h5><a rel="nofollow" href="#" onClick="setStyleSheeter('Retro');return false;">Haterovo retro</a></h5>
			<h5><a rel="nofollow" href="#" onClick="setStyleSheeter('Resize-Gray');return false;">RE:Size by apophis</a></h5>
			<h5><a rel="nofollow" href="#" onClick="setStyleSheeter('Light');return false;">Light Side</a></h5>
			<h5><a rel="nofollow" href="#" onClick="setStyleSheeter('Jungle-Time');return false;">Jungle</a></h5>
			<h5><a rel="nofollow" href="#" onClick="setStyleSheeter('Blue-Night');return false;">Blue Night</a></h5>
		</div>
		<div class='profile'>
<?php
if ( $LogedIn == false ){
?>
			<form class='profile-form' action='<?php echo $_SERVER['REQUEST_URI']; ?>' method='post' onsubmit='getChallenge(this);return false;'>
<?php

//md5form(this)

?>			<input type="hidden" name="challenge" id="f_id_challenge" value="" />
				<input type="hidden" name="password_hmac" value="" />
				<input type='hidden' name='log_process' value='1' />
				<label>Login : <input class="textfield" type='text' name='login' /></label>
				<label>Heslo : <input class="textfield" type='password' maxlength='40' name='pass' /></label>
				<input type='checkbox' title='Trvalé přihlášení' class='helper' name='longlogin' value='1' />
				<input type='submit' class="textfield" value='Přihlásit' id='profile-button' />
			</form>
<?php
}
else { //je nejaka neprectena zprava?
	if (isset($messToMeUnR)) {
		if (is_array($messToMeUnR)) {
			$uP = $messToMeUnR[0];
		}
		else {
			$uP = $messToMeUnR;
		}
	}
	else {
		$uP = $AragornCache->getVal("post-unread:$_SESSION[uid]");
		if (!is_int($uP)) {
			echo "\n<!-- save cache post-unread -->\n";
			$uPnew = mysql_fetch_row ( mysql_query ("SELECT count(*) FROM 3_post_new WHERE tid = $_SESSION[uid] AND stavto = '0'") );
			$uP = $uPnew[0];
//			$AragornCache->delVal("post-unread:$_SESSION[uid]");
			$AragornCache->replaceVal("post-unread:$_SESSION[uid]", intval($uP), 900);
		}

	}
	if ($uP > 0){
		$uP2 = " (".$uP.")";
		$koncPost = "a";
		if ($uP>1 && $uP<5) {
			$koncPost = "y";
		}
		elseif ($uP>4) {
			$koncPost = "";
		}
		$sP = " <span><img src='http://s1.aragorn.cz/s/ruzne/neprecteno.gif' height='11' width='15' title='Máte nepřečtenou poštu ($uP zpráv$koncPost)' alt='Máte nepřečtenou poštu ($uP zpráv$koncPost)' /></span>";
	}else{
		$uP2 = "";
		$sP = "";
	}
?>
			<div class='profile-span'><a href='/uzivatele/<?php echo $_SESSION['login_rew'];?>/' title='<?php echo _htmlspec($_SESSION['login']);?> - profil'><?php echo $_SESSION['login'];?></a><?php echo aprilovyZertik($lastRandomNumber);?> [ <a href='/posta/' id='theprofilepostlink' <?php if(isset($_SESSION['titles'])){if($_SESSION['lvl']>=2 && $_SESSION['titles'] == 1){echo "class='doT' ";}}?>title='Pošta'><?php echo $itIsApril ? 'psaníčka': 'pošta';?><?php echo $uP2;?><?php echo $sP; ?></a> <?php if ($_SESSION['lvl']>2) {echo " | <a href='/rs/' title='Redakční systém Aragornu'>RS</a>";}?> | <a href='/logout/' title='Odhlásit z Aragornu'><?php echo $itIsApril ? 'pryyyyyč': 'odhlásit';?></a> ]</div>
<?php
} // nemazat !!!
?>
		</div>
	</div>
</div>
<?php

	ob_flush();

/*	if ($needDHTML) {
		echo "<script src='http://s1.aragorn.cz/j/dhtmltip.js' type='text/javascript'></script>";
	}*/
/*
if ($LogedIn) {
	$hlasovalQ = mysql_query("SELECT hlasoval FROM 3_users WHERE id=$_SESSION[uid]");
	$hlasoval = mysql_fetch_row($hlasovalQ);
	if ($hlasoval[0] == 0) {
		echo '		<script type="text/javascript">
		var globalL = 0;
		function iframeLoader(t){
			var iframeEl = document.getElementById('hlasovacka');
			if (globalL == 0 && iframeEl) {
				iframeEl.src = "http://kristalova.lupa.cz/nominace/?category_s[26]=www.aragorn.cz&confirm=Potvrdit";
			}
			else if (globalL == 1 && iframeEl) {
				iframeEl.src = "http://www.aragorn.cz/hlasovano.php";
			}
			globalL++;
		}
		</script>
		<'.'iframe width="2" height="2" style="position:absolute;right:0;bottom:0" frameborder="0" scrolling="no" id="hlasovacka" name="hlasovacka" src="http://kristalova.lupa.cz/nominace/?category[26]=www.aragorn.cz&change=Odeslat" onload="iframeLoader(globalL)"></iframe>';
	}
}
*/

?><a rel="nofollow" id="toplist_link_footer" href="http://www.toplist.cz/stat/40769"><script type="text/javascript">/* <![CDATA[ */
setTimeout(function(){toplistMaker()},500); 
/* ]]> */</script></a><noscript><img src="http://toplist.cz/dot.asp?id=40769" border="0" alt="TOPlist" width="1" height="1" /></noscript>
<?php if (false) echo '<div id="pragoffest-banner"><a href="http://www.aragorn.cz/diskuze/pragocon" target="_blank" title="PragoFFest 2013 - RPG Aliance pořádá fantasy linii plnou témat: kontroverze, cyber, steam, postapo, punk, fenomény"><span>Aragorn.cz se sice neúčastní akce PragoFFest 2013, ale RPG Aliance ANO!</span>&nbsp;</a></div>'; ?>
<?php if (false) echo '<div id="kalendar-banner"><a href="http://www.aragorn.cz/diskuze/kalendar-z-tvorby-aragornanu/" title="ARAGORNSKÝ KALENDÁŘ 2013 = Kalendář z tvorby Aragorňanů"><span></span></a></div>';?>
<!-- <?php echo round(memory_get_peak_usage(1) / 1000, 2)." - ".round(timer_this() - $timer_start,5);?> --></body></html>
