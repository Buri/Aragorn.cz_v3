<?php

$noOutputBuffer = true;

//napojeni na db
include "../db/conn.php";
//memcache
include "../add/memcache.php";
//narveme tam fce
include "../add/funkce.php";

session_set_cookie_params(2*3600);
session_start();
$LogedIn = false;
$time = time();
mb_internal_encoding("UTF-8");
$act = 0;

if (!isset($_SESSION['uid'])) {
	die("<ax><ac><![CDATA[$act]]></ac></ax>");
	exit;
}

$SEZENI = $_SESSION;

$limiter_activ = $time - $ajaxTimeout;

if ($_SESSION['lvl'] >= 2) $ajaxTimeout = 25*60;

$limiter_activB = $time - 25*60;

//vyfakuje uzivatele z chatu
function terminateUser($uid, $rid, $ban = false){
global $time, $AragornCache;
	if ($ban && isset($AragornCache)) {
		$AragornCache->delVal("chat-room-".$rid.":users-".$uid);
	}
	elseif (isset($AragornCache)) {
		$cachedVal = $AragornCache->getVal("chat-room-".$rid.":users-".$uid);
		if ($cachedVal !== false && $cachedVal['odesel'] != 1) {
			$cachedVal['odesel'] = 1;
			$cachedVal['timestamp'] = $time;
			$AragornCache->replaceVal("chat-room-".$rid.":users-".$uid, $cachedVal, 120);
		}
	}
	mysql_query ("UPDATE 3_chat_users SET odesel = '1', timestamp = '$time' WHERE uid = $uid AND rid = $rid AND odesel='0'");
	return mysql_affected_rows();
}

if (!isset($_GET["id"])) {
	$rid = 0;
	$act = 0;
}
else $rid = intval($_GET["id"]);

$uid = $SEZENI['uid'];

$cachedVal = $AragornCache->getVal("chat-room-".$rid.":users-".$uid);
if ($cachedVal === false) { // not in cache yet
	$activ = mysql_query("SELECT prava,timestamp FROM 3_chat_users WHERE uid = '$SEZENI[uid]' AND rid = '$rid' AND odesel = '0'");
	if (mysql_num_rows($activ)>0) {
		$pravaUser = mysql_fetch_row($activ);
		if ($pravaUser[1] > ($time - $ajaxTimeout)) {
			$act = 1;
			$AragornCache->replaceVal("chat-room-".$rid.":users-".$uid, array(
				'uid' => $uid,
				'rid' => $rid,
				'timestamp' => $time,
				'odesel' => 0,
				'prava' => $pravaUser[0]
			), 900);
		}
		else {
			terminateUser($SEZENI['uid'], $rid);
		}
	}
	else {
		$act = 0;
		terminateUser($SEZENI['uid'], $rid);
		$pravaUser = array(0,0);
	}
}
else {
	$pravaUser = array($cachedVal['prava'], $cachedVal['timestamp']);
	if ($pravaUser[1] > ($time - $ajaxTimeout)) {
		$act = 1;
	}
	else {
		$act = 0;
		terminateUser($uid, $rid);
	}
}

if ($act>0) {
	switch ($_GET['do']) {
	case "chat_command":
		session_write_close();
		switch ($_POST['cmd']) {
			case "ban":
			case "sys":
			case "msg":
				if ($pravaUser[0] > 0) {
					$cmd = $_POST['cmd_add'];
					$cmd = trim(preg_replace('/\s{2,}/', ' ', $cmd)); // odstraneni duplicitnich mezer atd.
					if ($_POST['cmd'] == "ban") {
						$nick = $reasonN = "";
						$reason = "Nevhodné chování na chatu.";
						$bannedFor = 60; $error = 0;

						if (strpos($cmd, '"') !== false && substr_count($cmd,'"')>1) { // obsahuje ""
							preg_match_all('#[" ]{0,1}([^"]+(.*))(\\2)#um', $cmd, $matches, PREG_PATTERN_ORDER);
							$add = $matches[1];
							for($a=0;$a<count($add);$a++) $add[$a] = trim($add[$a]);
							$add = array_values(array_filter($add));
							if(strpos($cmd,'"') === 0) $nick = array_shift($add);
							if (count($add)==2) {
								if (ctype_digit($add[0])) $bannedFor = array_shift($add);
								else if (ctype_digit($add[count($add)-1])) $bannedFor = array_pop($add);
								if (count($add)>0) $reasonN = join(" ",$add);
							}
							else {
								$add = split(" ",join("",$add));
								if (!ctype_digit($add[0]) && ctype_digit($add[count($add)-1])) $bannedFor = array_pop($add);
								else if (ctype_digit($add[0])) $bannedFor = array_shift($add);
								if (count($add)>0) $reasonN = join(" ",$add);
							}
						}
						elseif (strpos($cmd, "'") !== false && substr_count($cmd,"'")>1) { // obsahuje ''
							preg_match_all("#[' ]{0,1}([^']+(.*))(\\2)#um", $cmd, $matches, PREG_PATTERN_ORDER);
							$add = $matches[1];
							for($a=0;$a<count($add);$a++) $add[$a] = trim($add[$a]);
							$add = array_values(array_filter($add));
							if(strpos($cmd,"'") === 0) $nick = array_shift($add);
							if (count($add)==2) {
								if (ctype_digit($add[0])) $bannedFor = array_shift($add);
								else if (ctype_digit($add[count($add)-1])) $bannedFor = array_pop($add);
								if (count($add)>0) $reasonN = join(" ",$add);
							}
							else {
								$add = split(" ",join("",$add));
								if (!ctype_digit($add[0]) && ctype_digit($add[count($add)-1])) $bannedFor = array_pop($add);
								else if (ctype_digit($add[0])) $bannedFor = array_shift($add);
								if (count($add)>0) $reasonN = join(" ",$add);
							}
						}
						else {	// neobsahuje zadne uvozovky
							if (strpos($cmd," ") === false) $nick = $cmd; // neobsahuje mezeru => direct ban na tenhle nick
							else {
								preg_match_all('#[ ]{0,1}([^ ]+(.*))(\\2)#um', $cmd, $matches, PREG_PATTERN_ORDER);
								$add = $matches[1];
								for($a=0;$a<count($add);$a++) $add[$a] = trim($add[$a]);
								$add = array_values(array_filter($add));
								if (count($add)>2) {
									$nick = array_shift($add);
									if (ctype_digit($add[0])) {
										$bannedFor = array_shift($add);
										if (count($add)>0) $reasonN = join(" ",$add);
										$add = array();
									}
									else $error = 1;
								}
								else {
									$nick = array_shift($add);
									if (ctype_digit($add[0])) {
										$bannedFor = array_shift($add);
										if (count($add)>0) $reasonN = join(" ",$add);
										$add = array();
									}
									else if (ctype_digit($add[count($add)-1])) $bannedFor = array_pop($add);
									if (count($add)>0) $error = 1;
								}
							}
						}
						if ($error != "0") {
							echo "Nelze rozpoznat nick";
						}
						else if (mb_strlen($nick)>=3) {
							if (mb_strlen($reasonN)>3) $reason = $reasonN;
							$nick = addslashes($nick);
							$s = mysql_query("SELECT login, level, ip, id FROM 3_users WHERE login = '$nick' AND reg_code = '0'");
							if ($s && mysql_num_rows($s)){
								$n = mysql_fetch_row($s);
								if ($n[1]<3) {
									$hasBan = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_ban WHERE uid = '$n[3]'"));
									if ($hasBan[0] > 0) {
										echo "$n[0] již ban má";
									}
									else {
										if ($n[3] != 2) {
											if (terminateUser($n[3], $rid, 1)) {
												mysql_query("DELETE FROM 3_chat_users WHERE rid = $rid AND uid = '$n[3]'");
											}
											$AragornCache->delVal("chat-room-".$rid.":users-".$n[3]);
											$bannedFor = 60*$bannedFor;
											$tAdmin = "Administrátor";
											if ($SEZENI['lvl'] < 3) $tAdmin = "Správce ";
											$reason = addslashes($reason);
											$text = "$tAdmin $SEZENI[login] udělil ban uživateli $n[0].";
											mysql_query("UPDATE 3_users SET online = 0, timestamp = 0 WHERE id = '$n[3]'"); //odhlasi ho
											mysql_query("INSERT INTO 3_ban (uid, fid, time, assignedin, reason, ipe) VALUES ('$n[3]', '$SEZENI[uid]', '$bannedFor', '$time', '$reason', '$n[2]')");
											ajaxChatInsertSystem($text, $_GET['id']);
											echo "Ban udělen - $n[0]";
										}
										else echo "apophise nevyhodíš!";
									}
								}
								else echo "Nelze vyhodit";
							}
							else if (mb_strtolower(stripslashes($nick)) == mb_strtolower($SEZENI['login'])) echo "Nelze vyhodit sebe";
							else echo "Uživatel nenalezen";
						}
						else echo "Chybné zadání";
					}
					else if ($_POST['cmd'] == "sys"){
						if (strlen($cmd)>5){
							ajaxChatInsertSystem($cmd, $_GET['id'], "Systém (~$SEZENI[login]~)");
							echo "/sys OK";
						}
						else echo "/sys zpráva 5+ znaků!!!";
					}
					else {
						echo "Příkaz /msg je dočasně nefunkční";
					}
				}
				else {
					echo "0";
				}
			break;
			case "vypatlej":
				if (isset($_POST['cmd_add']) && mb_strlen($_POST['cmd_add']) > 2) {
					$getColor = mysql_fetch_row( mysql_query ("SELECT chat_color FROM 3_users WHERE id = '$SEZENI[uid]'") );
					$color = $getColor[0];
					function vypatlej($whata) {
						$slova = Array("v"=>"w","!"=>"!!!","ú"=>"uuu","kw"=>"q","ů"=>"uuu","č"=>"cz","j"=>"y","š"=>"sh","ž"=>"zh","á"=>"aaa","é"=>"eee","eyt"=>"8","ř"=>"rz","í"=>"iii","ý"=>"yyy","ó"=>"ooo","ě"=>"e","ť"=>"t","ň"=>"n","ö"=>"o","ü"=>"u","ľ"=>"l","ŕ"=>"rrr","ĺ"=>"l","ô"=>"oo","ł"=>"l","ş"=>"s","ç"=>"c","ü"=>"u","ć"=>"c","ś"=>"s","ź"=>"z","ń"=>"n",". "=>" LOL. ",", "=>" woe, "," woe, "=>" chD, ","to yo"=>"tj","to ano"=>"tj","srdce"=>"srdiiiczkooo","spaaat"=>"hajat","spinkat"=>"hajinkat","spinka"=>"hajinka","spinkaaa"=>"hayinkaaa","howno"=>"howiiinkooo","polibek"=>"muckaaaniii","liiibaaaniii"=>"mucinkaaaniii","dobryyy"=>"good","prdel "=>"kakaaaczek ","prdel,"=>"kakaaaczek,","prdel."=>"kakaaaczek.","prdel!"=>"kakaaaczek!","do prdele"=>"do kakaaaczka","v prdeli"=>"v kakaaaczku","prdeliii"=>"kakaaaczkem","dobry"=>"good","piwo"=>"piiivo","mimochodem"=>"btw","diiik."=>"thx.","diiiky."=>"thx.","diiik!"=>"thx!!!","diiiky!"=>"thx!!!","diiik,"=>"thx,","diiiky,"=>"thx,","dekuju"=>"thx","dekuji."=>"thx.","mrdka"=>"mrdaaanek","mrdky"=>"mrdaaanky","mrdkou"=>"mrdaaanekm","mrdkami"=>"mrdaaankama","mrdkma"=>"mrdaaankama","kraaaw"=>"klawisht","koza"=>"koziczka","kozy"=>"koziczky","kozataaa"=>"koziczkataaa"," moc "=>" mocinky "," uuuplne "=>" upe "," uplne "=>" upe ","wole "=>"woe "," ano"=>" jj","newiiim"=>"nwm","newim"=>"nwm"," ty vole"=>" twe"," ty woe"=>" twe","milaaacz"=>"milaaash","miluy"=>"lowiiiskuy","milov"=>"lowiiiskow","neylepshiii "=>"best ","promin "=>"sry ","prominte"=>"soracz","ď"=>"d","smrt "=>"death ","kurva"=>"kua","protoze"=>"ptz","protozhe"=>"ptz","kurwa"=>"kua","prosim"=>"pllls ","prosiiim"=>"pls ","pawou"=>"pabou","huste"=>"cool","husteee"=>"cool","hustyyy"=>"cool"," oka "=>" kukucz "," oka,"=>" kukucz,"," oka."=>" kukucz."," oka!"=>" kukucz!","koczk"=>"koshisht","prase"=>"prasaaatko","sran"=>"kakan","seru"=>"kakaaam","spaaat"=>"dadynkat","spi "=>"dadynkej ","draaat"=>"dlaaat","czay "=>"czayiczek ","puuuydu"=>"pudu","boliii"=>"bolinkaaa","bill "=>"billiiishek ","bolest."=>"bebiiiczkooo.","bolest,"=>"bebiii,","bolest!"=>"bebiii!!!","bolestiw"=>"bebiiiczkow","ale "=>"ae ","ale "=>"aue ","wolat"=>"telefooonowat","kunda"=>"kundiczkaaa","czuuuraaak"=>"czuuulaaaczek","moye"=>"moe","twoye"=>"twoe","kamaraaad"=>"kaaamosh","tedy "=>"teda ","peysek"=>"pesaaaczek","aaaczci"=>"aaashci","trochu"=>"kapishtu","troshku"=>"kapishtu","trocha"=>"kapishta","troshka"=>"kapishta","polshtaaarz "=>"bucliiik ","polshtaaarzo"=>"bucliiiko","polshtaaarze "=>"bucliiiky ","polshtaaarzem "=>"bucliiikem ","polshtaaarzi"=>"bucliiiku","polshtaaarzema"=>"bucliiikama","polshtaaarzemi"=>"bucliiikama","perzin"=>"perzink"," ucho"=>" oushko"," ushi"=>" oushka"," ushat"=>" oushkat"," ucha"=>" ouszka","ruuuzhow"=>"ruuuzhowouck","slowniiik "=>"slowniiiczek ","slowniiiku"=>"slowniiiczku","slowniiikuuu"=>"slowniiiczkuuu","slowniiiky"=>"slowniiiczky","slowniiikem"=>"slowniiiczkem","slowniiikama"=>"slowniiiczkama","hezk"=>"klaaasnoushk","eugeot "=>"ezhotek ","rabant"=>"laaabik","kraaaw"=>"klawishk","yenom"=>"enom","pouze"=>"enom","zhaaarowk"=>"zhaaarowiczk","zhaaarziwk"=>"zhaaarziweczk","wyyyboyk"=>"wyyyboycziczk","ymenuyi se"=>"nadaaaway mi","ymenuyu se"=>"nadaaaway mi","ymenuyiii se"=>"nadaaaway yim","ymenuyou se"=>"nadaaaway yim","ahoy"=>"ayoy","hlawa"=>"hlawiczka","hlawo"=>"hlawiczko","hlawy"=>"hlawiczky","x"=>"xxx","hahaha"=>"hhh","ch"=>"x","to ye"=>"toe","nikdy"=>"nigdy","neniii"=>"neeeni","co ye"=>"coe","t "=>"th ");
						$smajl = Array(" :-***"," X_x"," =("," =)"," ;-*"," O_o"," ^_^"," <3"," xD"," :-/"," </3");
						$slint = Array(" *MuUuUcK*"," *LoWe*"," *KiSs*"," Emo Ye BeSt!!!!!"," UmIiIrAaAm, ZhiWOT jE Na hOwNo!!!"," ToKiO HoTeL RuLezZz!!!"," BiLlIiIsHeK Ye BeSt!"," FsHeCkY WaAaS LoWiIiSkUyU!"," YsEm UpE DaAaRk A IiIwL!!!"," Toe WoDWazZz Woe!!!"," WoE NeWiIiIiIiIiSh!!!!!!!!!!"," i hATe EWeRyOnE!!!"," NeMaAa NeKdO ZhIlEtKu?"," SeSh hUstEy!!! mEgA WoE!"," MrTe Te MuCiNkAaAm DiiiVenKooOoO!"," MTMMMMMR"," BoLiNkAaA Me SrDiIiIiIcZkOoOoO </3 :'("," <3 :-***"," loWiIisKuYu EmO!!! :-**"," YaAa Se PoDrZiIiZnU!!! :(((("," SmUtNiIiIiIiIiIiM!!!!!!!!!!!! :(((((((("," NiKdO Me NeMaAa LaAaAaAaD!!!!!! :((((((");
						$whata = strtr($whata, $slova);
						$whata .= " ".$smajl[mt_rand(0, count($smajl)-1)];
						$whata .= " ".$slint[mt_rand(0, count($slint)-1)];
						return $whata;
					}
					$whatHere = $_POST['cmd_add'];
					$partK = "";
					$haveLom = strpos($whatHere, "//", 2);
					if (is_int($haveLom) &&  $haveLom > 1) {
						$what = explode("//", $whatHere, 2);
						$partK = trim($what[1]);
						$partS = $what[0];
					}
					else {
						$partS = $whatHere;
					}
					$mess = trim(stripslashes(vypatlej(trim($partS)))." ".$partK);
					ajaxChatInsert($mess, $SEZENI['uid'], $SEZENI['login'], 0, 0, $rid, $color, $time, false, false);
					echo "ok";
				}
				else {
					echo "K vYpAtLaaNiiiCZku zadej text !P /-8";
				}
			break;
			case "find":
				if (isset($_POST['cmd_add']) && mb_strlen($_POST['cmd_add']) > 2) {
					$getWho = addslashes($_POST['cmd_add']);
					$addStr = "";
					$n = mysql_query("SELECT id, login, timestamp, last_login FROM 3_users WHERE login = '$getWho'");
					if ($n && mysql_num_rows($n)>0){
						$usr = mysql_fetch_object($n);
					  if ($usr->id != $SEZENI['uid']) {
							$clr = "#66FF66";
							if ($usr->timestamp == 0) {
								$clr = "#FF6666";
								if ($usr->last_login > 0) $str = "offline (poslední přihlášení ".sdh($usr->last_login).")";
								else $str = "offline (přihlášení zatím neproběhlo)";
							}
							else {
								$str = "online (poslední akce v ".date("H:i",$usr->timestamp).")";
								$chatS = mysql_query("SELECT r.nazev FROM 3_chat_users AS c, 3_chat_rooms AS r WHERE r.id = c.rid AND c.uid = $usr->id AND c.odesel = '0' ORDER BY r.type ASC, r.nazev ASC");
								if ($chatS && mysql_num_rows($chatS)>0) {
								  $chatRooms = array();
								  while($room = mysql_fetch_row($chatS)){
								    $chatRooms[] = $room[0];
									}
									$addStr .= " v <a href='/chat/'>místnost".((count($chatRooms)>1)?"ech:":"i")."</a> ".join(", ",$chatRooms);
								}
								$jeskS = mysql_query("SELECT c.nazev,c.nazev_rew FROM 3_cave_users AS u, 3_herna_all AS c WHERE c.id = u.cid AND u.uid = $usr->id AND u.pozice != 'g' ORDER BY c.nazev ASC");
								if ($jeskS && mysql_num_rows($jeskS)>0) {
								  $jeskRooms = array();
								  while($jesk = mysql_fetch_row($jeskS)){
								    $jeskRooms[] = "<a href='/herna/$jesk[1]/'>"._htmlspec($jesk[0])."</a>";
									}
									if (strlen($addStr)>5) $addStr .= " a na chatu jeskyn".((count($jeskRooms)>1)?"ní:":"ě")." ".join(", ",$jeskRooms).".";
									else $addStr .= " na chatu jeskyn".((count($jeskRooms)>1)?"ní:":"ě")." ".join(", ",$jeskRooms).".";
								}
							}
							$text = " <em style='color:$clr'>$usr->login je ".$str."</em> &nbsp;$addStr";
							ajaxChatInsertSystemWhisper($text, $_GET['id'], $SEZENI['uid'], $SEZENI['login']);
							echo "ok";
						}
						else {
						  echo "Hledáte sami sebe?";
						}
					}
					else echo "Uživatel nenalezen";
				}
				else echo "Zadejte nick (min. 3 znaky)";
			break;
			case "help":
				$rAdm = mysql_query("SELECT login, login_rew FROM 3_users WHERE level > 2 AND online = 1 AND timestamp > 0 AND id > 1 ORDER BY login ASC");
				$defText = "Pomoci mohou <a href='/admins/' title='Výpis Administrátorů serveru Aragorn.cz' target='_blank'>Administrátoři</a>";
				if ($rAdm && mysql_num_rows($rAdm)>0) {
					$admins = array();
					while($usr = mysql_fetch_row($rAdm)){
						$admins[] = "<a href='/uzivatele/$usr[1]/' target='_blank'>"._htmlspec($usr[0])."</a>";
					}
					$text = $defText." , z nichž ".((count($admins)>1)?"jsou:":"je")." právě online ".join(", ",$admins);
				}
				else {
					$text = $defText." , kteří však právě nejsou online.";
					$rSpr = mysql_query("SELECT u.login,u.login_rew FROM 3_users AS u, 3_chat_admin AS c WHERE u.online = 1 AND u.timestamp > 0 AND c.uid = u.id ORDER BY login ASC");
					if ($rSpr && mysql_num_rows($rAdm)>0) {
						$spr = array();
						while($usr = mysql_fetch_row($rSpr)){
							$spr[] = "<a href='/uzivatele/$usr[1]/' target='_blank'>"._htmlspec($usr[0])."</a>";
						}
						$text = $text." Zkuste napsat některému z přítomných Stálých správců: ".join(", ",$spr);
					}
					else $text = $text." Zkuste jim napsat interní poštou.";
				}
				ajaxChatInsertSystemWhisper($text, $_GET['id'], $SEZENI['uid'], $SEZENI['login']);
				echo "ok";
			break;
		}
	break;
	//ajax chat odeslani prispevku
	case "chat_sending":
		//	$isAdmin = ($pravaUser[0]==1&&$SEZENI['lvl']>2)?true:false;
		$isAdmin = $pravaUser[0];
		if ($_GET['to'] > 0){
		 	$to = addslashes($_GET['to']);
		 	$getName = $AragornCache->getVal("users-id2login-$to");
			if ($getName === false) {
				$getName = mysql_fetch_row( mysql_query ("select login from 3_users where id = '$to'") );
				$AragornCache->replaceVal("users-id2login-$to", $getName, 1800);
			}
			$toName = $getName[0];
			$mess = trim($_POST['message']);
		}else{
			$mess = trim($_POST['message']);
			//byla pouzita JS fce add_js()?
			$js_test = explode("#",mb_substr($mess,0,40),2);
			$test_nick = addslashes($js_test[0]);
			$to = addslashes($_GET['to']);
			$toName = 0;
			$sel_js = mysql_query ("SELECT u.id,u.login FROM 3_users AS u WHERE u.login='$test_nick' AND u.id != '$SEZENI[uid]'");
			if ($sel_js && mysql_num_rows($sel_js)>0 && count($js_test)>1){
				$komuItem = mysql_fetch_object($sel_js);
				$toName = $komuItem->login;
				$to = $komuItem->id;
				$mess = trim(mb_substr($mess,(mb_strlen($test_nick)+1)));
			}
		}
		$getColor = $AragornCache->getVal("users-id2color-$uid");
		if ($getColor === false) {
			$getColor = mysql_fetch_row( mysql_query ("SELECT chat_color FROM 3_users WHERE id = '$SEZENI[uid]'") );
			$color = $getColor[0];
			$AragornCache->replaceVal("users-id2color-$uid", $color, 900);
		}
		else {
			$color = $getColor;
		}

	 	if (mb_strlen($mess)>0) {
			$isSystem = false;
 			mysql_query ("UPDATE 3_chat_users SET timestamp = '$time', odesel='0' WHERE uid = '$SEZENI[uid]' and rid = $rid");
			mysql_query ("UPDATE 3_users SET online = 1, timestamp = '$time' WHERE id = '$SEZENI[uid]'");
			$cachedVal = $AragornCache->getVal("chat-room-".$rid.":users-".$uid);
			if ($cachedVal !== false) {
				$cachedVal['odesel'] = 0;
				$cachedVal['timestamp'] = $time;
				$AragornCache->replaceVal("chat-room-".$rid.":users-".$uid, $cachedVal, 900);
			}
			ajaxChatInsert(trim($mess), $SEZENI['uid'], $SEZENI['login'], $to, $toName, $rid, $color, $time, $isAdmin, $isSystem);
			$newSess = array();
			if (isset($SEZENI['updated'])){
				if ($SEZENI['updated'] + 1800 < $time ){
					session_regenerate_id();
					$SEZENI["updated"] = $time;
				}
			}
			else {
				session_regenerate_id();
				$SEZENI["updated"] = $time;
			}
			foreach ($SEZENI as $k => $v) {
				$newSess[$k] = $v;
			}
			$_SESSION = array();
			foreach ($newSess as $k => $v) {
				$_SESSION[$k] = $v;
			}
		}
		session_write_close();
	break;
	
	case "mess_delete":
		session_write_close();
		if ($pravaUser[0] > 0 && isset($_GET['mid']) && $_GET['mid']>0) {
			$mid = addslashes($_GET['mid']);
			mysql_query("DELETE FROM 3_ajax_chat WHERE id = '$mid' AND room = '$rid'");
			if (mysql_affected_rows()) {
				ajaxChatInsert("$mid","0","Systém","0","-",$rid,$mid,$time,false,true);
				@Header("Content-Type: text/plain; charset=utf-8");
				echo $mid;
				exit;
			}
			else {
				@Header("Content-Type: text/plain; charset=utf-8");
				echo $mid;
				exit;
			}
		}
		else {
			@Header("Content-Type: text/plain; charset=utf-8");
			echo "";
			exit;
		}
	break;
	//zpracovani zprav a jejich odeslani jako xml
	case "chat_refreshing":
		session_write_close();
		@Header("Content-Type: application/xml; charset=utf-8");
		$lid = 0;
		if (isset($_GET['last_id'])) {
			$lid = intval($_GET['last_id']);
		}
		$selMessages = mysql_query ("SELECT * FROM 3_ajax_chat WHERE id > $lid AND room = $rid AND (tid = 0 OR tid = '$SEZENI[uid]' OR fid = '$SEZENI[uid]') ORDER BY id ASC");
		echo "<ax>\n";
		$messCnt = 0;
		$old = array('chatName'=>'','toName'=>'','text'=>'');
		while ($item = mysql_fetch_object($selMessages)) {
			$uns = unserialize($item->serialized);
			$chatName = _htmlspec(base64_decode($uns['fname']));
			$toName = _htmlspec(base64_decode($uns['tname']));
			$text = base64_decode($uns['text']);
			if ($old['text'] == $text && $old['chatName'] == $chatName && $old['toName'] == $toName) {
				continue;
			}
			else {
				$old['text'] = $text;
				$old['chatName'] = $chatName;
				$old['toName'] = $toName;
			}
			$color = _htmlspec($uns['color']);
			$sys = ($chatName == '0')? 1 : 0;
			if (($item->fid == $SEZENI['uid'] || $item->tid == $SEZENI['uid']) && $item->tid > 0 ){ //soukroma
				$mode = "1";
			}else if( $item->fid == 0 && $item->tid == $SEZENI['uid'] ){ //system septa
				$mode = "2";
			}else if ($item->fid == 0 && $toName == '-') { // system pise zpravu o vymazani zpravy
				if ($lid < 1) {
					continue;
				}
				else {
					$toName = "-"; $text = "..."; $mode = "9";
					echo "<ms id=\"$item->id\" t=\"-\" c=\"$color\" m=\"$mode\"><![CDATA[$text]]></ms>";
					continue;
				}
			}else if( $item->fid == 0){ //system vsem
				$mode = "3";
			}else{ //verejna
				$mode= "4";
			}
			echo "<ms id=\"$item->id\" f=\"$chatName\" t=\"$toName\" s=\"$sys\" c=\"$color\" l=\"".date("H:i:s",$item->time)."\" m=\"$mode\"><![CDATA[$text]]></ms>\n";
			$messCnt++;
			if ($messCnt >= 50 && $lid > 0) {
				break;
			}
		}
		echo "</ax>";
	break;
	
	//refresh selectu u formulare
	case "select_filling":
		session_write_close();
		@Header("Content-Type: application/xml; charset=utf-8");
		$sel = mysql_query("SELECT u.id, u.login FROM 3_users AS u, 3_chat_users AS c WHERE c.rid = $rid AND c.uid = u.id AND u.id != '$SEZENI[uid]' AND c.odesel = '0' ORDER BY u.login_rew ASC");
		echo "<ax>";
		while($option = mysql_fetch_row($sel)){
			echo "<o id='$option[0]' name='"._htmlspec($option[1])."' />";
		}
		echo "</ax>";
	break;
	
	//refresh online negru v zahlavi
	case "occupants_refresh";
		@Header("Content-Type: application/xml; charset=utf-8");
		echo ajaxRefreshOccupants($rid, 0);
	break;
	
	//odchod
	case "chat_leave":
		session_write_close();
		if (mb_strlen(trim($_POST['message'])) > 0){
			$message = $_POST['message'];
			$add = " a vzkazuje : ".$message;
		}else{
			$add = ".";
		}
		if ($SEZENI['uid'] > 0){
			$text = $SEZENI['login']." odchází z místnosti".$add;
			terminateUser($SEZENI['uid'], $rid);
			ajaxChatInsertSystem($text, $rid);
		}
	break;
	
	// vyhazovani
	case "kick":
		session_write_close();
		if($pravaUser[0] > 0 && $_GET['who'] > 0){
			$getWho = addslashes($_GET['who']);
			$n = mysql_fetch_row( mysql_query("SELECT login, level, ip, id FROM 3_users WHERE id = '$getWho' AND id > 1 AND reg_code = 0") );
			if ($n[1] < 3 && $getWho != $SEZENI['uid']){
				$hasBan = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_ban WHERE uid = $n[3]"));
				if ($hasBan[0] > 0){
					echo "$n[0] již ban má";
				}
				else {
					$tAdmin = "Administrátor";
					if ($SEZENI['lvl']<3) {
						$tAdmin = "Správce ";
					}
					$text = "$tAdmin $SEZENI[login] vyhodil uživatele $n[0] z místnosti.";
					mysql_query ("DELETE FROM 3_ajax_chat WHERE fid = '$getWho' AND rid = '$rid'");
					mysql_query ("UPDATE 3_users SET online = 0, timestamp = 0 WHERE id = '$getWho'"); //odhlasi ho
					terminateUser($n[3], $rid);
					$bannedFor = 60*60*24;
					$reason = "Nevhodné chování na chatu";
					mysql_query ("INSERT INTO 3_ban (uid, fid, time, assignedin, reason, ipe) VALUES ('$n[3]', '$SEZENI[uid]', '$bannedFor', '$time', '$reason', '$n[2]')");
					ajaxChatInsertSystem($text, $_GET['id']);
					echo "ok";
				}
			}
			else echo "Nelze vyhodit";
		}
		else echo "Nemáte dostatečná práva.";
	break;

	case "check_activity":
		session_write_close();
		//vykopnuti neaktivnich z ajax_chatu

		$inactives = array();

    $cachedVal = $AragornCache->getVal("chat-room-".$rid.":activity-check");
		if ($cachedVal !== false && $cachedVal !== 0) {
		}
		else {
			$inactive = mysql_query ("SELECT u.login, u.timestamp, c.uid, c.rid, u.level, c.timestamp AS ctime FROM 3_chat_users AS c, 3_users AS u WHERE c.uid = u.id AND c.rid = $rid AND (c.timestamp < $limiter_activ OR u.online = 0) AND c.odesel = '0'");
			if ($inactive && mysql_num_rows($inactive)>0) {
				while($item = mysql_fetch_row($inactive)){
					$inactives[] = $item;
				}
			}
		}

		if (count($inactives) > 0) {
			foreach($inactives as $item) {
				if ($item[1] == 0 || $item[4] < 2 || ($item[4] >= 2 && $item[5] < $limiter_activB)) {
					$uk = $item[0];
					if ($item[1] > 0) {
						$text = "$uk vyhozen(a) z místnosti pro dlouhodobou neaktivitu.";
					}
					else {
						$text = "$uk se odhlásil(a) ze serveru.";
					}
					ajaxChatInsertSystem($text, $item[3]);
					terminateUser($item[2], $item[3]);
				}
			}
			sleep(1);
			$AragornCache->replaceVal("chat-room-".$rid.":activity-check", 0, 60);
		}
		//mysql_query("UNLOCK TABLES");

		@Header("Content-Type: application/xml; charset=utf-8");
		echo "<ax>";
		echo "<ac><![CDATA[$act]]></ac>";
		echo "</ax>";
	break;

}
}
else {
	@Header("Content-Type: application/xml; charset=utf-8");
	echo "<ax>";
	echo "<ac><![CDATA[$act]]></ac>";
	echo "</ax>";
}
?>