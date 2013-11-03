<?php
date_default_timezone_set('Europe/Prague');
if ($_SESSION['lvl'] >= 2) $ajaxTimeout = 25*60;
$activ = mysql_fetch_row( mysql_query ("select uid, prava from 3_chat_users where uid = '$_SESSION[uid]' and rid = '$id' and timestamp > ($time - $ajaxTimeout) AND odesel='0'") );
$roomExistsS = mysql_query ("select id,nazev from 3_chat_rooms where id = '$id'");
if ($roomExistsS && mysql_num_rows($roomExistsS)>0) $roomExists = mysql_fetch_row($roomExistsS);
else $roomExists = array(0,0,0);
$fontSize = mysql_fetch_row( mysql_query ("SELECT chat_font, chat_time, chat_order FROM 3_users WHERE id = '$_SESSION[uid]'") );
if ($activ[0] < 1 || $roomExists[0] < 1){
	Header ("Location: $inc/chat/");
	exit;
}
?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
	<meta http-equiv="Content-language" content="cs" />
	<meta http-equiv="Content-Type" content="text/xhtml; charset=utf-8" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta name="robots" content="all, follow" />
	<meta name="description" content="Aragorn AjaxChat" />
	<meta http-equiv="X-UA-Compatible" content="IE=IE8" >
	<base target="_blank" />
	<title>AjaxChat - <?php echo _htmlspec($roomExists[1]);?></title>
	<link rel="stylesheet" type="text/css" href="/ajax_chat/css/style_oop_actual.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js" type="text/javascript"></script>
	<script src="/js/ajax_chat_oop_actual.js" type="text/javascript"></script>
	<script type="text/javascript" charset="utf-8">
		var g_RID = <?php echo $id; ?>;
		var gShowTime = <?php echo $fontSize[1]; ?>;
		var gTopToBottom = <?php echo ($fontSize[2]=='desc'?'true':'false'); ?>;
	</script>
	<script type="text/javascript">var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-28138327-1']);_gaq.push(['_setDomainName', 'aragorn.cz']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();</script>
</head>
<body>
<div id="dhtmltooltip"></div>
<script src='/js/dhtmltip.js' type='text/javascript' async='false'></script>
<a class="hlink" href="/" title="Úvodní stránka Aragorn.cz" target="_blank">Aragorn.cz</a>
<div class="holder">
	<div class="top">
		<div id="nav"><?php echo ajaxRefreshOccupants($id, 1);?>
		</div>
	</div>
	<div class="middle">

<?php
if (false) {
// if ($time < mktime(17, 0, 0, 5, 7, 2008)) {
?>
		<!-- odpočítávadlo-->
		<div style="text-align:center; position:absolute;z-index:5; top:5px; right:80px; font-size:10px;">Do <a href="/diskuze/larp-info/" title="Informace o AraLARPu 5">AraLARPu V</a> zbývá:
			<span id="count2" style="color:#FFFFFF;text-align:right"></span> <a href="#" onclick="disableCountdown();return false;" title="Vypnout odpočítadlo !!!">X</a>&nbsp;</div>
			<script type="text/javascript">var xRunCount=true;function disableCountdown(){xRunCount=false;$('count2').getParent().remove();};var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");function countdown(yr,m,d){if(!xRunCount){return};theyear=yr;themonth=m;theday=d;var today=new Date();var todayy=today.getYear();if (todayy < 1000){todayy+=1900;}var todaym=today.getMonth();var todayd=today.getDate();var todayh=today.getHours();var todaymin=today.getMinutes();var todaysec=today.getSeconds();var todaystring=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec;futurestring=montharray[m-1]+" "+d+", "+yr+" 17:00:00";dd=Date.parse(futurestring)-Date.parse(todaystring);dday=Math.floor(dd/(60*60*1000*24)*1);dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1);if(dday==0&&dhour==0&&dmin==0&&dsec==1){	document.getElementById("count2").innerHTML=dday+ " dnů, "+dhour+" hodin, "+dmin+" minut, a "+dsec+" sekund";return;}else{document.getElementById("count2").innerHTML=dday+ " dnů, "+dhour+" hodin, "+dmin+" minut, a "+dsec+" sekund";setTimeout("countdown(theyear,themonth,theday)",1000);}}countdown(2008,5,7);</script>
		<!-- odpočítávadlo-->
<?php
}
?>
		<div id="chat" style="font-size: <?php echo $fontSize[0]; ?>px"></div>
	</div>
	<div class="bottom">
		<div id="forma"><form onsubmit="Aragchat.cmd(3);return false;">Zpráva <input type="text" accesskey="x" tabindex="1" id="message" size="40" /> &nbsp; <select tabindex="2" name="users" id="users"><option value="0">Všem</option></select> <input tabindex="3" type="checkbox" id="ch-eck" />&nbsp;<input tabindex="4" type="submit" value="Poslat" title="Zprávu lze poslat i klávesou Enter!" /><?php if ($activ[1] > 0)echo " &nbsp; <input tabindex='5' type='button' onclick='Aragchat.cmd(6);' value='Ban' /> <input tabindex='6' type='button' onclick='Aragchat.deleter();' id='mazani_zprav' value='Mazání Zpráv' />"; ?> &nbsp; <input tabindex="7" type="button" onclick="Aragchat.cmd(4);" value="Odejít" />
			<span id="progress"><span class="progressTxt">Žádná&nbsp;akce</span></span>
		</form>
		</div>
	</div>
<?php if ($_SESSION['lvl']>=2):
?>
	<div id="friends"></div>
<?php
	endif;
?>
	<div id="smileys"><div>
	<big>Klasika</big><img title="*:-D*" src="/chat/smile/grin.gif" /><img title="*:-)*" src="/chat/smile/smile.gif" /><img title="*=)*" src="/chat/smile/smile2.gif" /><img title="*;-)*" src="/chat/smile/wink.gif" /><img title="*:-/*" src="/chat/smile/dontknow.gif" /><img title="*:-x*" src="/chat/smile/quiet.gif" /><img title="*:-(*" src="/chat/smile/sad.gif" /><img title="*:-P*" src="/chat/smile/tongue.gif" />
	<big>Rozšiřující</big><img title="*:angry:*" src="/chat/smile/angry.gif" /><img title="*:oops:*" src="/chat/smile/ashamed.gif" /><img title="*:cry:*" src="/chat/smile/cry.gif" /><img title="*:censored:*" src="/chat/smile/censored.gif" /><img title="*:wow:*" src="/chat/smile/wtf.gif" /><img title="*:tongue:*" src="/chat/smile/tongue2.gif" /><img title="*:vamp:*" src="/chat/smile/vamp.gif" /><img title="*:twisted:*" src="/chat/smile/twisted.gif" /><img title="*:mad:*" src="/chat/smile/ai.gif" /><img title="*:yes:*" src="/chat/smile/yes.gif" /><img title="*:no:*" src="/chat/smile/no.gif" /><img title="*:green:*" src="/chat/smile/green.gif" /><img title="*:red:*" src="/chat/smile/red.gif" /><img title="*:blue:*" src="/chat/smile/blue.gif" />
	<big>Specialitky</big><img title="*:angel:*" src="/chat/smile/angel.gif" /><img title="*:sun:*" src="/chat/smile/sun.gif" /><img title="*:moon:*" src="/chat/smile/moon.gif" /><img title="*:duck:*" src="/chat/smile/duck.gif" /><img title="*:redstar:*" src="/chat/smile/red-star.gif" /><img title="*:flower:*" src="/chat/smile/flower.gif" /><img title="*:flower2:*" src="/chat/smile/flower2.gif" /><img title="*:kiss:*" src="/chat/smile/kiss.gif" /><img title="*:coffee:*" src="/chat/smile/coffee.gif" /><img title="*:coffee2:*" src="/chat/smile/coffee2.gif" /><img title="*:touchme:*" src="/chat/smile/vetvicka.gif" /><img title="*:cccp:*" src="/chat/smile/cccp.gif" /><img title="*:czech:*" src="/chat/smile/czech.gif" /><img title="*:naistar:*" src="/chat/smile/star.gif" /><img title="*:bighug:*" src="/chat/smile/hug2.gif" /><img title="*:hug:*" src="/chat/smile/hug.gif" />
<?php
if($activ[1] > 0) {
?>
	<big>Adminské smajle</big><img title="*:metal4ever:*" src="/chat/smile/metal.gif" /><img title="*:kill:*" src="/chat/smile/kill.gif" /><img title="*:selfkill:*" src="/chat/smile/self_killer.gif" /><img title="*:bagr4gran:*" src="/chat/smile/bagr2.gif" /><img title="*:wall:*" src="/chat/smile/wall.gif" /><img title="*:regretful:*" src="/chat/smile/regretful.gif" /><img title="*:car:*" src="/chat/smile/car2.gif" /><img title="*:4077:*" src="/chat/smile/helicopter.png" /><img title="*:spank:*" src="/chat/smile/spank.gif" /><img title="*:guitar:*" src="/chat/smile/guitar.gif" /><img title="*:witch:*" src="/chat/smile/witch.gif" />
<?php
}
?>
	</div></div>
</div>

<?php

echo "<scr"."ipt type=\"text/javascript\" charset=\"utf-8\">\n";

if($activ[1]>0) {
	echo "crossForDel = '×';func"."tion adel(e,n){ if (!e) { var e = window.event; } Aragchat.todel(e,n); }\n";
}else echo "crossForDel='';func"."tion adel(){}\n";

echo "
var Aragchat;
window.addEvent('domready',function(){
	Aragchat = new Ajaxchat('chat','/ajax_chat/chat_ajaxing.php');
	$('message').focus();
});
</script>
";

?>
</body>
</html>
