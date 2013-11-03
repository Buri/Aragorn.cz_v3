<?php

$LoginTimeout = 60*30; // po jakem case prohlasi uzivatele za odhlaseneho
$LogedIn = false;

function checkLongLoginGenerate($h="",$p=""){
	return sha1($h."4^hIwQ4-9@_:".sha1("0".$p."~"));
}

function new_auth_do_clear($n){
	global $time;
	mysql_query("DELETE FROM 3_long_login WHERE nick = '".addslashes($n)."'");
	setcookie("longLoginNick","",$time-10000,"","www.aragorn.cz");
	setcookie("longLoginHash","",$time-10000,"","www.aragorn.cz");
	setcookie("longLoginChck","",$time-10000,"","www.aragorn.cz");
}

function new_auth_long_login(){
	global $row,$LogedIn,$link,$redirectLoginSuccess,$time,$inc;

	if (isset($_COOKIE['longLoginNick'],$_COOKIE['longLoginHash'],$_COOKIE['longLoginChck'])) {
		$sql = "SELECT * FROM 3_long_login WHERE nick = '".addslashes($_COOKIE['longLoginNick'])."' AND hash = '".addslashes($_COOKIE['longLoginHash'])."' AND chck = '".addslashes($_COOKIE['longLoginChck'])."' LIMIT 1";
		$checkerS = mysql_query($sql);
		if ($checkerS && mysql_num_rows($checkerS)) {
			$checker = mysql_fetch_object($checkerS);
			$sql = "SELECT pass,id,timestamp,login_rew FROM 3_users WHERE login_rew = '".addslashes($checker->nick)."' AND reg_code = '0' LIMIT 1";
			$userS = mysql_query($sql);
			if ($userS && mysql_num_rows($userS)>0) {
				$row = mysql_fetch_object($userS);
				if ($checker->chck == checkLongLoginGenerate($checker->hash,$row->pass)) {
					$redirectLoginSuccess = $_SERVER['REQUEST_URI'];
					new_auth_do_logged(true);
				}
				else new_auth_do_clear($_COOKIE['longLoginNick']);
			}
			else new_auth_do_clear($_COOKIE['longLoginNick']);
		}
		else new_auth_do_clear($_COOKIE['longLoginNick']);
	}
}

function new_auth_do_logged($doLong=false){
	global $row,$LogedIn,$link,$redirectLoginSuccess,$time,$inc,$AragornCache;

	$SEZENI = array();
	$Login_query = "SELECT u.id,u.login,u.login_rew,u.level,u.set_titles,u.chat_color,u.chat_ref,u.chat_time,u.chat_order,u.chat_font,u.chat_sys,u.account_created,us.serialized FROM 3_users AS u LEFT JOIN 3_users_settings AS us ON us.uid = u.id WHERE u.id = '$row->id' AND u.reg_code = '0'";
	$LoginRes = mysql_query($Login_query) or die(mysql_error());

	$ipe = getUserIP();

	$LoginObj = mysql_fetch_object($LoginRes);

	$uid = $SEZENI["uid"] = intval($LoginObj->id);

  if (isset($AragornCache)) {
  	$AragornCache->delVal("post-unread:$uid");
	}

	$login = $SEZENI["login"] = stripslashes($LoginObj->login);
	$login_rew = $SEZENI["login_rew"] = stripslashes($LoginObj->login_rew);
	$SEZENI['chat_color'] = $LoginObj->chat_color;
	$SEZENI['chat_ref'] = $LoginObj->chat_ref;
	$SEZENI['chat_time'] = intval($LoginObj->chat_time);
	$SEZENI['chat_order'] = $LoginObj->chat_order;
	$SEZENI['chat_font'] = $LoginObj->chat_font;
	$SEZENI['chat_sys'] = $LoginObj->chat_sys;
	$SEZENI['novacek'] = ($time < (3600 * 24 * 14 + intval($LoginObj->account_created, 10)) ? true : false);
	
	$LoginObj->serialized = $LoginObj->serialized ? json_decode($LoginObj->serialized, true) : array();
	$SEZENI['chut'] = isset($LoginObj->serialized['chut']) ? $LoginObj->serialized['chut'] : 1;

	if (isset($SEZENI['updated'])){
		if ($SEZENI['updated'] + 900 < $time ){
			session_regenerate_id();
			$SEZENI["updated"] = $time;
		}
	}
	else {
		session_regenerate_id();
		$SEZENI["updated"] = $time;
	}
	$SEZENI["lvl"] = intval($LoginObj->level);
	if (intval($LoginObj->level) >= 2) {
		$SEZENI['titles'] = intval($LoginObj->set_titles);
	}
	else {
		$SEZENI['titles'] = 0;
	}
	$SEZENI["ip"] = $ipe;
	$SEZENI["first"] = true;
	$LogedIn = true;

	include "./add/check_for_ban.php";

	mysql_query ("UPDATE 3_users SET ip = '$ipe', online = '1', timestamp = '$time', last_login = '$time' WHERE id = '$LoginObj->id'");

	if ($link == "chybny-login" || $link == "registrace" || $link == "timeout" || $link == "uspesna-registrace" || $link == "potvrzeni-registrace") {
		$redirectLoginSuccess = "";
	}
//			$_SESSION["fresh"] = $redirectLoginSuccess;
	$SEZENI["fresh"] = false;
	//upozorneni na chat, ze se prihlasil nekdo koho ma ten z chatu v pratelich - jen pro bonusové
	$doRedirect = true;

	$_SESSION = $SEZENI;
	if ($LogedIn) {
		if ($doLong || (!$doLong && isset($_POST['longlogin']))) {
			$hash = sha1(uniqid(rand()));
			$chck = checkLongLoginGenerate($hash,$row->pass);
			$timeFuture = $time+1314000;
			if (!$doLong) {
				mysql_query("DELETE FROM 3_long_login WHERE nick = '$row->login_rew'");
				mysql_query("INSERT INTO 3_long_login (nick,hash,chck) VALUES ('$row->login_rew','$hash','$chck')");
			}
			else {
				mysql_query("UPDATE 3_long_login SET hash = '$hash', chck = '$chck' WHERE nick = '$row->login_rew'");
			}
			setcookie("longLoginNick", $row->login_rew, $timeFuture,"","www.aragorn.cz");
			setcookie("longLoginHash", $hash, $timeFuture,"","www.aragorn.cz");
			setcookie("longLoginChck", $chck, $timeFuture,"","www.aragorn.cz");
			$doRedirect = false;
		}
		else {
			new_auth_do_clear($_SESSION['login_rew']);
		}

	}

	if ($doRedirect && $LogedIn && $row->timestamp < 10) {
		$friendSql = mysql_query("SELECT f.uid,r.type,c.rid,u.login,u.chat_warn_roz,u.chat_warn_other,u.chat_warn_ajax FROM 3_chat_rooms AS r, 3_friends as f, 3_chat_users AS c, 3_users AS u WHERE r.id=c.rid AND f.fid=$LoginObj->id AND f.uid=c.uid AND u.id=c.uid AND u.level>=2");
		while($RS = mysql_fetch_object($friendSql)){
			if($RS->rid > 1){
				$text = addslashes("Právě se na server Aragorn.cz přihlásil Váš přítel <i>".$_SESSION["login"]."</i>.");
				if ($RS->type > 0 && $RS->chat_warn_roz > 0) {
					mysql_query ("INSERT INTO 3_chat_mess (uid, rid, wh, time, text) VALUES (0, $RS->rid, $RS->uid, $time, '$text')");
				}
				elseif ($RS->type < 1 && $RS->chat_warn_other > 0) {
					mysql_query ("INSERT INTO 3_chat_mess (uid, rid, wh, time, text) VALUES (0, $RS->rid, $RS->uid, $time, '$text')");
				}
			}
			else{
				if ($RS->chat_warn_ajax > 0) {
					$text = "Právě se na server Aragorn.cz přihlásil Váš přítel <i>".$_SESSION["login"]."</i>.";
					ajaxChatInsertSystemWhisper($text, $RS->rid, $RS->uid, $RS->login);
				}
			}
		}
	}

	if ($doRedirect) {
		Header("Location: " . $inc . $redirectLoginSuccess );
		exit();
	}
}

/*
///////////////////////////////////
///////////////////////////////////
	all new_auth function end here
///////////////////////////////////
///////////////////////////////////
*/

// *** Validate request to login to this site.
if (isset($_POST['log_process']) && isset($_POST['login']) && ( isset($_POST['pass']) || (isset($_POST['password_hmac']) && isset($_POST['challenge'])))) {
	if (($_POST['log_process'] == "1") && ($_POST['login'] !== "") && ( ( isset($_POST['pass']) && ($_POST['pass'] !== "") ) || ( isset($_POST['password_hmac']) && ($_POST['password_hmac'] !== "") ) ) ) {
		$redirectLoginSuccess = $_SERVER['REQUEST_URI'];
		$noHmac = true;
		$redirectLoginFailed	= "/chybny-login/";
		$loginName = addslashes($_POST["login"]);
		$sql = mysql_query("SELECT pass,id,online,timestamp,login_rew FROM 3_users WHERE login = '$loginName' AND reg_code = '0' LIMIT 1");
		$valid = $logged = false;
		if (mysql_num_rows($sql) < 1) {
			$valid = false;
		}
		else {
			$row = mysql_fetch_object($sql);
			if ( isset($_POST['password_hmac']) && ($_POST["password_hmac"] !== "") ) {
				$noHmac = false;
				$valid = (hmac_md5($row->pass, $_POST["challenge"]) == $_POST["password_hmac"]);
			} else {
				$valid = ($row->pass == md5($_POST["pass"]));
			}
		}

		if ($valid) {
			mysql_query("UPDATE 3_challenges SET used_r = '1' WHERE id = " . intval($_POST["challenge"]));
			if (mysql_affected_rows() || $noHmac) {
				$logged = true;
			}
		}
		else {
			mysql_query("UPDATE 3_challenges SET used_r = '1' WHERE id = " . intval($_POST["challenge"]));
		}
		if ($logged) {
			new_auth_do_logged();
		}
		else {
			Header("Location: ". $inc . $redirectLoginFailed );
			exit();
		}
	}
	else {
		Header("Location: ". $inc );
	}
}
else { // uzivatel se nehlasil, nebo jiz je prihlaseny
	$LogedIn = false; // defaultne je neprihlaseny

	if (isset($_SESSION['uid']) && isset($_SESSION['uid'])) {
		if ($_SESSION["uid"] > 0 && $_SESSION["login"] != "") {
			$sq = "SELECT count(*) FROM 3_users WHERE id = $_SESSION[uid] AND online = 1 AND timestamp > ".($time - $LoginTimeout);
			if (isset($_SESSION['lvl'])) {
				if ($_SESSION['lvl'] >= 2) {
					$sq = "SELECT count(*) FROM 3_users WHERE id = $_SESSION[uid] AND online = 1 AND timestamp > ".($time - (2*$LoginTimeout));
				}
			}
			$chT = mysql_fetch_row( mysql_query ($sq) );
			if ($chT[0] > 0){
				$LogedIn = true;
				$ar = array();
				foreach($_SESSION as $k => $v){
					$ar[$k] = $v;
				}
				if (!isset($ar['titles']))
					$ar["titles"] = 0;
				if (!isset($ar['chut']))
					$ar["chut"] = 1;
				mysql_query("UPDATE 3_users SET online = '1', timestamp = '$time' WHERE id = '$_SESSION[uid]'");
				foreach($ar as $k => $v){
					$_SESSION[$k] = $v;
				}
			}
			else {
				$LogedIn = false;

				include "./add/check_for_ban.php";
				new_auth_long_login();

				if (!$LogedIn) {
					mysql_query ("UPDATE 3_users SET online = '0', timestamp = '0' WHERE id = $_SESSION[uid]");

					$_SESSION = array();
					$postArray = &$_POST;
					$postedValues = array();
					if ($link == "herna") {
						$postedValues["link"] = $link;
						if ($hFound) {	// byl v jeskyni
							if ($pFound || $slink == "pj") { //	byl v editaci postavy
								foreach ($postArray as $key => $value) {if ( strpos($key, "_edit") !== false ) $postedValues[] = "<b>[".substr($key,0,-5)."]</b>: " . _htmlspec($value);}
							}
							elseif (isset($_POST['mess'])) {
								$postedValues = array("link" => $link, "mess" => $_POST['mess']);
							}
							elseif ($slink = "reg") {
								foreach ($postArray as $key => $value ) {if ( strpos($key, "_edit") !== false ) $postedValues[] = "<b>[".substr($key,0,-8)."]</b>: " . _htmlspec($value);}
							}
						}
						elseif ($slink == "new") {
								foreach ($postArray as $key => $value ) {if ( strpos($key, "cave_") !== false ) $postedValues[] = "<b>[".substr($key,5)."]</b>: " . _htmlspec($value);}
						}
					}
					else {
						$postedValues = array("link" => $link, "mess" => $_POST['mess']);
					}
	 				$_SESSION['saved_array'] = $postedValues;
					Header ("Location:".$inc."/timeout/");
					exit;
				}
			}
		}
		else {
			include "./add/check_for_ban.php";
			new_auth_long_login();

			if (isset($_POST['mess'])){
				$_SESSION['saved_array'] = array("link" => $link, "mess" => $_POST['mess']); //rozepsany text
				Header ("Location:".$inc."/timeout/");
				exit;
			}
			elseif ($link != "timeout"){
				session_destroy();
			}
			$LogedIn = false;
		}
	}
	else {
		new_auth_long_login();
	}


	if (!$LogedIn) {
		if (isset($_POST['mess'])){
			$_SESSION['saved_array'] = array("link" => $link, "mess" => $_POST['mess']); //rozepsany text
			Header ("Location:".$inc."/timeout/");
			exit;
		}
		elseif ($link != "timeout"){
			session_destroy();
		}
		$LogedIn = false;
	}
}

if ($link == "pokracovat" && $LogedIn) {
	$urlWanted = $_SESSION['fresh'];
	$_SESSION['fresh'] = false;
	$uvodniky = false;
	header("Location: ".$inc.$urlWanted);
	exit;
}

if (($link == "logout") && ($LogedIn == true) && ($_SESSION['uid'] > 0) && ($_SESSION['login'] != "")) {
	$LogedIn = false;
	mysql_query ("UPDATE 3_users SET last_login = $time, login_count = login_count+1 WHERE id = $_SESSION[uid]");
	mysql_query ("UPDATE 3_users SET timestamp = '0', online = '0' WHERE id = $_SESSION[uid]");

	new_auth_do_clear($_SESSION['login_rew']);

	$_SESSION = array();

	unset($_SESSION);
	session_destroy();
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}

	Header ("Location:".$inc);
	exit;
}

?>