<?php
//zaregistrovani usera

//je takovy nick nebo mail v db?
//

$seo = do_seo(trim($_POST['login']));
$mail = addslashes(trim($_POST['mail']));

$sel_log = mysql_query("SELECT count(*) FROM 3_users WHERE login_rew = '".addslashes($seo)."'");
$out_log = mysql_fetch_row($sel_log);

$sel_last_regtime = mysql_query("SELECT count(*) FROM 3_users WHERE (ip LIKE '%".addslashes(array_shift(explode("@", getUserIP())))."%') AND (account_created > UNIX_TIMESTAMP() - 86400)");
$sel_last_regtime = mysql_fetch_row($sel_last_regtime);
$sel_last_regtime = $sel_last_regtime[0];

$sel_mail = mysql_query ("SELECT count(*) FROM 3_users WHERE mail = '$mail'");
$out_mail = mysql_fetch_row($sel_mail);

$blacklist = array(
	'mailinator.com',
	'spambog.com',
	'trashmail.net',
	'10minutemail.com',
	'guerrillamailblock.com',
	'dodgit.com',
	'mintemail.com',
	'getonemail.com',
	'guerrillamailblock.co',
	'onewaymail.com'
);

function matchBlacklist($mail, $array) {
	foreach($array as $k => $v){
		if (stripos($mail, $v, 2) !== false) {
			return true;
		}
	}
	return false;
}

// chyba
if (strlen(trim($_POST['login'])) < 3){
  $error = 1;
}elseif (bl(trim($_POST['login'])) > 0){
  $error = 2;
}elseif (strlen($_POST['pass']) < 5){
  $error = 3;
}elseif (strlen($_POST['pass2']) < 5){
  $error = 4;
}elseif ($_POST['pass'] !== $_POST['pass2']){
  $error = 5;
}elseif (mb_strlen($_POST['mail']) < 9){
  $error = 6;
}elseif ($out_log[0] > 0){
  $error = 7;
}elseif ($out_mail[0] > 0){
  $error = 8;
}elseif ($_POST['rcheck'] != "6"){
  $error = 9;
}elseif (matchBlacklist($_POST['mail'], $blacklist)) {
	$error = 11;
}elseif ($sel_last_regtime > 0) {
	$error = 12;
}
else{
//vlozeni do db, pokud vse ok - vygenerovani potvrzovacigo reg. klice a odeslani mailem

for ($i=0;$i<7;$i++){
  $reg_code .= rand(0,9);
}

$reg_code = intval($reg_code);

$a = mysql_query("INSERT INTO 3_users (login, login_rew, pass, mail, ip, reg_code, account_created) VALUES ('".trim($_POST['login'])."', '".$seo."', '".md5($_POST['pass'])."','".trim($_POST['mail'])."','".getUserIP()."','$reg_code','$time')");
$o = mysql_affected_rows();

	if ($o < 1) {
		$error = 10;
	}
}

//redirect pri chybe / uspesny redirect

if (isSet($error)){
	Header ("Location:$inc/registrace/?error=$error");
}else{
	$mail_text = "<html><body><h2>Registracni udaje ze serveru Aragorn.cz</h2><p>Na serveru <a href='http://www.aragorn.cz' target='_blank'>Aragorn.cz</a> byla provedena registrace noveho uzivatele <i>".trim($_POST['login'])."</i> na e-mail <i>$_POST[mail]</i>.<br />Pokud jste Vy tuto registraci nevyplnili nebo nekdo za Vas a s touto registraci nesouhlasite, muzete tento e-mail smazat. V opacnem pripade prosim ctete dal.</p><hr /> <p>Registrace na serveru Aragorn.cz probehla uspesne a nyni je treba ji potvrdit.</p><p>Login: <u>".trim($_POST["login"])."</u><br />Heslo: <u>$_POST[pass]</u></p><p>Pro potvrzeni a dokonceni registrace pouzijte nasledujici odkaz:<br /><a href='$inc/potvrzeni-registrace/?reg_code=$reg_code'>$inc/potvrzeni-registrace/?reg_code=$reg_code</a></p><p>Dekujeme za Vas cas a verime, ze Vam server Aragorn.cz prinese mnoho hodin zabavy<br /><br />Administratori Aragorn.cz</p></body></html>";
	mail($_POST['mail'], "Registrace na Aragorn.cz", $mail_text ,$headers);
//	mail('registrace@aragorn.cz', "Registrace na Aragorn.cz", "login: ".trim($_POST['login']),$headers);
	Header ("Location:$inc/uspesna-registrace/");
}
exit;
?>
