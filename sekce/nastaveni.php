<?php
  $titleNastaveni = $itIsApril ? 'Poštelovat' : 'Nastavení';
?><h2 class='h2-head'><a href='/nastaveni/' title='<?php echo $titleNastaveni;?>'><?php echo $titleNastaveni;?></a></h2>
<h3><a href='<?php if ($slink){$add = "/";} echo "/$link/$slink$add"; ?>' title='<?php echo $title; ?>'><?php echo $title; ?></a></h3>
<p class='submenu'>
	<a href='/nastaveni/osobni/' class='permalink' title='Osobní nastavení'>Ikonka/Osobní</a>
	<a href='/nastaveni/systemove/' class='permalink' title='Systémové nastavení'>Systém</a>
	<a href='/nastaveni/chat/' class='permalink' title='Chat'>Chat</a>
	<a href='/nastaveni/rozcesti/' class='permalink' title='Rozcestí'>Rozcestí</a>
	<a href='/bonus/' class='permalink' title='Bonus'>Bonus</a>
</p>

<?php
//nastaveni vraceno s chybou
if (isSet($_GET['error'])){

switch ($_GET['error']){

case 1:
  $error = "Ikonka nebyla zadána.";
break;

case 2:
  $error = "Ikonka má nesprávný formát.<br /> Dovolené formáty jsou : <strong>GIF</strong>, <strong>JPEG</strong>, <strong>PNG</strong>.";
break;

case 3:
  $error = "Ikonka nemá povolené rozměry.<br /> Limit je <strong>40-50px</strong> / <strong>40-70px</strong> (š, v).";
break;

case 4:
  $error = "Velikost ikonky je vyšší než 16kB.";
break;

case 5:
  $error = "Heslo musí mít minimálně 5 znaků.";
break;

case 6:
  $error = "Kontrola hesla musí mít minimálně 5 znaků.";
break;

case 7:
  $error = "Zadaná hesla se sobě nerovnají, proveďte prosím jejich kontrolu.";
break;

case 8:
  $error = "Zadané současné heslo není platné.";
break;

case 9:
  $error = "Zadaný E-mail již někdo používá.<br />Změna nebyla možná.";
break;

case 10:
  $error = "Smazání ikonky se nepovedlo.";
break;

case 16:
  $error = "_is_uploaded_file_ Error";
break;

case 17:
  $error = "_move_uploaded_file_ Error";
break;

}

info($error);
}elseif (isSet($_GET['ok'])){

switch ($_GET['ok']){

case 1:
  $ok = "Ikonka byla v pořádku nahrána.";
break;

case 2:
  $ok = "Heslo bylo v pořádku změněno.";
break;

case 3:
  $ok = "E-mail byl v pořádku změněn.";
break;

case 4:
  $ok = "Osobní údaje v pořádku uloženy.";
break;

case 5:
  $ok = "Podpis v pořádku změněn.";
break;

case 6:
  $ok = "Nastavení chatu v pořádku uloženo.";
break;

case 7:
  $ok = "Sledování odstraněno.";
break;

case 8:
  $ok = "Ikonka vpořádku smazána.";
break;

case 9:
  $ok = "Údaje změněny.";
break;

case 10:
  $ok = "Chuťovky uloženy.";
break;
}

ok($ok);

}

//vytazeni udaju
$user = mysql_fetch_object(mysql_query("SELECT u.*,ua.about_me,us.serialized AS settings FROM 3_users AS u LEFT JOIN 3_users_about AS ua ON ua.uid = u.id LEFT JOIN 3_users_settings AS us ON us.uid = u.id WHERE u.id = '$_SESSION[uid]'"));

switch($_GET['slink']){

case "rozcesti":
	if ($user->roz_ico != "") {
		$ico = "<img src='http://s1.aragorn.cz/r/".$user->roz_ico."' style='position: relative; left: 5px' /> <a href='/nastaveni/rozcesti/?akce=nastaveni-rozcesti&amp;do=ico-delete&amp;c=".md5("roz-ico-delete-".$_SESSION['login_rew'])."' onclick='if(confirm(\"Skutečně smazat ikonku postavy?\")){return true;}return false;'>smazat&nbsp;ikonku</a>";
	}
	else {
		$ico = "Žádná ikonka postavy &raquo; Bude použita ikonka z profilu.";
	}

	$user->roz_popis = mb_strimwidth($user->roz_popis, 0, 250, "");

?>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/?akce=nastaveni-rozcesti&amp;do=ico-upload' method='post' enctype='multipart/form-data' accept='image/*' class='f'>
<fieldset>
<legend>Ikonka postavy</legend>
<div><?php echo $ico;?></div>
<label><span>Ikonka</span><input type='file' name='ico' /></label>
<input class='button' type='submit' value='Nahrát' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/?akce=nastaveni-rozcesti&amp;do=edit' method='post' class='f'>
<fieldset>
<legend>Údaje o postavě</legend>
<label><span>Jméno</span><input maxlength="20" type='text' name='jmeno_postavy' value='<?php echo $user->roz_name; ?>' size='20' maxlength='20' /></label>
<label><span>O postavě</span><textarea onkeypress="document.getElementById('updatechars').innerHTML=(180-this.value.length);if(this.value.length>180){this.value=this.value.substring(0,180);return;}document.getElementById('updatechars').innerHTML=(180-this.value.length);" onkeyup="document.getElementById('updatechars').innerHTML=(180-this.value.length);if(this.value.length>180){this.value=this.value.substring(0,200);return;}document.getElementById('updatechars').innerHTML=(180-this.value.length);" rows='4' cols='70' name='popis_postavy' /><?php echo $user->roz_popis; ?></textarea></label>
<div class="t-a-c"><small>Text &bdquo;O postavě&ldquo; může mít délku nejvýše <strong>180</strong> znaků. <span id="updatechars"><?php echo (180-mb_strlen($user->roz_popis));?></span> zbývá.</small></div>
<input class='button' type='submit' value='Uložit změny' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='art'>
	<p class="text t-a-c">Postavy a jejich majitelé s nejvíce XP body - prvních 20, řazeno dle&nbsp;počtu XP, vynechávání Správců Rozcestí</p>
	<table class="edttbl" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td><strong>Nick</strong></td>
			<td><strong>Postava</strong></td>
		</tr>
<?php
	$postavs = array();
	$postavy = mysql_query("SELECT u.login_rew,u.login,u.roz_name,a.typ FROM 3_users AS u LEFT JOIN 3_chat_admin AS a ON a.uid = u.id WHERE u.level < 3 AND a.typ IS NULL ORDER BY u.roz_exp DESC LIMIT 20");
	while($post = mysql_fetch_row($postavy))
		$postavs[] = "		<tr><td><a href='/uzivatele/$post[0]'>$post[1]</a></td><td>".$post[2]."</td></tr>";

	ksort($postavs);
	$postavs = join("\n",$postavs);
	echo $postavs;
?>
	</table>
</div>
<?php
break;

case "osobni":
$mail = stripslashes($user->mail);
$name = stripslashes($user->name);
$city = stripslashes($user->city);
$icq = stripslashes($user->icq);
$about_me = stripslashes($user->about_me);
?>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/?akce=nastaveni-ico' method='post' enctype='multipart/form-data' accept='image/*' class='f'>
<fieldset>
<legend>Nahrání ikonky</legend>
<div><img src='http://s1.aragorn.cz/i/<?php echo $user->ico; ?>' style='position: relative; left: 5px' alt='<?php echo $_SESSION['login']; ?>' title='<?php echo $_SESSION['login']; ?>' /></div>
<label><span>Ikonka</span><input type='file' name='ico' /></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/?akce=nastaveni-heslo' name='reg' method='post' class='f' onSubmit='return checkForm()'>
<fieldset>
<legend>Změna hesla</legend>
<label><span>Současné heslo</span><input type='password' name='old_pass' size='20' /></label>
<label><span>Nové heslo</span><input type='password' name='pass' size='20' /></label>
<label><span>Nové heslo znovu</span><input type='password' name='pass2' size='20' /></label>
<input class='button' type='submit' value='Změnit heslo' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/?akce=nastaveni-mail' name='reg2' method='post' class='f' onSubmit='return checkMail()'>
<fieldset>
<legend>Změna E-mailu</legend>
<label><span>E-mail</span><input type='text' name='mail' value='<?php echo $mail; ?>' size='20' /></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/?akce=nastaveni-os' method='post' class='f'>
<fieldset>
<legend>Osobní údaje uživatele</legend>
<label><span>Jméno</span><input type='text' name='name' value='<?php echo $name; ?>' size='20' maxlength='20' /></label>
<label><span>Město</span><input type='text' name='city' value='<?php echo $city; ?>' size='20' maxlength='20' /></label>
<label><span>ICQ</span><input type='text' name='icq' value='<?php echo $icq; ?>' size='20' maxlength='20' /></label>
<label><span>O mně</span><textarea rows='8' name='about_me' /><?php echo $about_me; ?></textarea></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<script language='JavaScript' type='text/javascript'>
function checkForm(){
end=0
ver=new Array("old_pass", "pass", "pass2")
ver2=new Array("Současné heslo", "Nové heslo", "Nové heslo znovu")
ver_count=ver.length

for(i=0;i<ver_count;i++){
if(document.forms["reg"][ver[i]].value.length==0 && end < 1){
alert("Nebylo vyplněno pole "+ver2[i])
end = 1
return false
}
}

if (document.forms["reg"]["pass"].value.length < 5){
alert ("Heslo musí mít alespoň 5 znaků")
return false
}

if (document.forms["reg"]["pass"].value != document.forms["reg"]["pass2"].value){
alert ("Zadaná hesla se neshodují")
return false
}

return true
}

function checkMail(){
end = 0
ver = new Array("mail")
ver2 = new Array("E-mail")
ver_count = ver.length

for (i=0;i<ver_count;i++){
if(document.forms["reg2"][ver[i]].value.length==0 && end < 1){
alert ("Nebylo vyplněno pole "+ver2[i])
end = 1
return false
}
}

if (!check_mail(document.forms["reg2"]["mail"].value)){
alert ("E-mailová adresa je v chybném tvaru")
end = 1
return false
}

return true
}
</script>

<?php
break;
case "systemove";

$sign = stripslashes($user->signature);
if ($_SESSION['lvl'] < 2){
?>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/?akce=nastaveni-bonus' method='post' class='f'>
<fieldset>
<legend>Aktivace <acronym title='Placená verze Aragornu (nadstandardní funkce, získání hvězdy)' xml:lang='cs'>bonusu</acronym></legend>
<label><span>Bonus</span><select name='bonus' style='width: 152px'>
<?php
if ($user->level < 1){
?>
<option value='6'>Půl roku (150 Kč)</option>
<option value='12'>Rok (300 Kč)</option>
<?php
}else{
?>
<option value='0'>Zrušit žádost</option>
<?php
}
?>
</select></label>
<input class='button' type='submit' value='Provést' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
<?php
}
else {
?>
	<p class="text">Informace <a href="/bonus/">o platbách Bonusu</a> nalezneš ve specializované sekci.</p>
<?php
	if ($user->set_titles == "1"){
		$setT = " selected='selected'";
	}else{
		$setT = "";
	}

?>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/systemove/?akce=nastaveni-title' method='post' class='f'>
<fieldset>
<legend>Aktivace <acronym title='Automatické obnovování titulku stránky, kdy se dopočítají záložky a nová pošta dle aktuálního počtu. Obnovuje se společně se výpisem záložek, přátel, aj.' xml:lang='cs'>inteligentních</acronym> titulků stránky</legend>
<label><span>Inteligentní titulky</span><select name='activation' style='width: 152px'>
<option value='0'>vypnuto</option>
<option value='1'<?php echo $setT;?>>zapnuto</option>
</select></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<?php

}

	$userSettings = ($user->settings ? json_decode($user->settings, true) : array());

	$chut = $help = array_fill(0,2,"");

	if (!isset($userSettings['chut'])) {
	}
	elseif ($userSettings['chut'] == 1) {
		$chut[1] = " selected='selected'";
	} else {
		$chut[0] = " selected='selected'";
	}

	if (isset($userSettings['help']) && $userSettings['help'] == 1) {
		$help[1] = " selected='selected'";
	} else {
		$help[0] = " selected='selected'";
	}

//pocet sledovanych veci
$a = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_visited_1 WHERE uid = $_SESSION[uid]"));
$b = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_visited_2 WHERE uid = $_SESSION[uid]"));
$c = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_visited_3 WHERE uid = $_SESSION[uid]"));
$d = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_visited_4 WHERE uid = $_SESSION[uid]"));
?>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/systemove/?akce=nastaveni-system-specialities' method='post' class='f'>
<fieldset>
<legend>Systémové "chuťovky"</legend>
<label><span>Chci pomáhat nováčkům</span><select name='be-help' style='width: 152px'>
<option value='1'<?php echo $help[1];?>>ano</option>
<option value='0'<?php echo $help[0];?>>ne</option>
</select></label>
<label><span>Mám bejt drsnej?</span><select name='chutovky' style='width: 152px'>
<?php echo (!isset($userSettings['chut']) ? '<option value="0">nevím</option>' : '');?>
<option value='1'<?php echo $chut[1];?>>jasně</option>
<option value='0'<?php echo $chut[0];?>>radši ne</option>
</select></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>


<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/systemove/?akce=nastaveni-podpis' method='post' class='f'>
<fieldset>
<legend>Podpis pod diskuzemi</legend>
<label><span>Podpis</span><textarea rows='4' name='sign'><?php echo $sign; ?></textarea></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/systemove/?akce=nastaveni-attend' method='post' class='f'>
<fieldset>
<legend>Odstranění navštívených (sledovaných) diskuzí, článků apod.<br />Odstraní i případné záložky!</legend>
<label><span>Sekce</span><select name='subject'><option value='1'>Články (<?php echo $a[0]; ?>)</option><option value='2'>Galerie (<?php echo $b[0]; ?>)</option><option value='3'>Diskuze (<?php echo $c[0]; ?>)</option><option value='4'>Herna (<?php echo $d[0]; ?>)</option><option value='5'>Vše</option></select></label>
<input class='button' type='submit' value='Odstranit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<?php
break;
case "chat":

if ($user->chat_order == "asc"){
  $chO = " selected='selected'";
}else{
  $chO = "";
}

if ($user->chat_time < 1){
  $chT = " selected='selected'";
}else{
  $chT = "";
}

if ($user->chat_sys < 1){
  $chS = " selected='selected'";
}else{
  $chS = "";
}

if ($user->chat_warn_roz < 1){
  $chSR = " selected='selected'";
}else{
  $chSR = "";
}

if ($user->chat_warn_ajax < 1){
  $chSA = " selected='selected'";
}else{
  $chSA = "";
}

if ($user->chat_warn_other < 1){
  $chSO = " selected='selected'";
}else{
  $chSO = "";
}
?>
<link rel="stylesheet" href="/system/mooRainbow.css" type="text/css" />
<script src="/js/mooRainbow.js" type="text/javascript"></script>
<script type="text/javascript">
var csssX,jsssX,myRB;
window.addEvent('domready',function(){
	createMyRB();
});
function createMyRB(){
	myRB = new MooRainbow('myRainbow', {
		imgPath: '/system/ruzne/',
		<?php
		if (strpos($user->chat_color,"#") !== false && strlen($user->chat_color) == 7) {
			$l = (strlen($user->chat_color) - 1) / 3;
			$r = hexdec(substr($user->chat_color,1,$l));
			$g = hexdec(substr($user->chat_color,$l+1,$l));
			$b = hexdec(substr($user->chat_color,2*$l+1,$l));
			echo "'startColor': [".$r.",".$g.",".$b."],\n";
		}
		?>
		onChange: function(color) {
			$('myColor').value = color.hex;
		},
		onComplete: function(color) {
			$('myColor').value = color.hex;
		}
	});
}
</script>

<div class='f-top'></div>
<div class='f-middle'>
<form action='/nastaveni/?akce=nastaveni-chat' method='post' class='f'>
<fieldset>
<legend>Nastavení chatu</legend>
<label><span>Barva </span><input id='myColor' type='text' name='color' value='<?php echo $user->chat_color; ?>' size='20' maxlength='20' /><img id="myRainbow" src="/system/ruzne/rainbow.png" alt="" title="Otevřít míchátko barev" style="vertical-align:middle" /></label>
<p><small>Příliš tmavá barva písma může mít &bdquo;všelijaké&ldquo; nečekané následky.</small></p>
<label><span>Refresh</span><input type='text' name='ref' value='<?php echo $user->chat_ref; ?>' size='20' maxlength='2' /></label>
<label><span>Velikost písma (px)</span><input type='text' name='size' value='<?php echo $user->chat_font; ?>' size='20' maxlength='2' /></label>
<label><span>Výpis zpráv</span><select name='order' style='width: 152px' /><option value='desc'>Sestupně</option><option value='asc'<?php echo $chO; ?>>Vzestupně</option></select></label>
<?php if ($_SESSION['lvl'] >= 2) {
?>
<label><span>Sys.zprávy loginu v Rozcestí</span><select name='sys_roz' style='width: 152px' /><option value='1'>Ano</option><option value='0'<?php echo $chSR; ?>>Ne</option></select></label>
<label><span>Sys.zprávy loginu v AjaxChatu</span><select name='sys_ajax' style='width: 152px' /><option value='1'>Ano</option><option value='0'<?php echo $chSA; ?>>Ne</option></select></label>
<label><span>Sys.zprávy loginu v ostatní</span><select name='sys_other' style='width: 152px' /><option value='1'>Ano</option><option value='0'<?php echo $chSO; ?>>Ne</option></select></label>
<?php
}
?>
<label><span>Zobrazovat čas</span><select name='v_time' style='width: 152px' /><option value='1'>Ano</option><option value='0'<?php echo $chT; ?>>Ne</option></select></label>
<label><span>Zobrazovat sys. zprávy</span><select name='sys' style='width: 152px' /><option value='1'>Ano</option><option value='0'<?php echo $chS; ?>>Ne</option></select></label>
<input class='button' type='submit' value='Uložit' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<?php
break;
}
?>
