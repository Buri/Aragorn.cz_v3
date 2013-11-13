<?php

session_start();

$time=time();

include("/home/domeny/aragorn.cz/web/subdomeny/www/db/conn.php");

include("/home/domeny/aragorn.cz/web/subdomeny/www/add/memcache.php");

include("/home/domeny/aragorn.cz/web/subdomeny/www/add/funkce.php");

header("content-type:text/plain;charset=utf-8");

mysql_query("DELETE FROM 3_challenges WHERE created < NOW()-3600");
mysql_query("DELETE FROM 3_ajax_chat WHERE time < ($time - 3600)");

mysql_query ("DELETE FROM 3_cave_mess WHERE time < $time-21600");
mysql_query ("DELETE FROM 3_cave_users WHERE timestamp < $time-7200");
mysql_query ("DELETE FROM aragorncz01.3_herna_pj WHERE cid NOT IN ( SELECT id FROM 3_herna_all )");

$minuta = date("i",$time);
$hodina = date("H",$time);

if (($minuta < 50 && $minuta >= 45) || ($minuta < 20 && $minuta >= 15) || ($minuta < 35 && $minuta >= 30) || ($minuta < 5 && $minuta >= 0)) {
	$ajaxPeople = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_users WHERE rid=1 AND odesel = 0"));
	if ($ajaxPeople[0]>0) {
		// Pridani reklamy na ajax chat jen kdyz je nekdo online a aktivni
		write_advert();
	}
}

$apophis = false;
if (isset($_SESSION['uid'])) {
	if ($_SESSION['uid'] == 2)
		$apophis = true;
}

if (($minuta >= 5 && $minuta < 10 && $hodina > 2 && $hodina <= 4) || $apophis) {

	$msg = "";
	$fast = false;
	$verbose = true;
	$tcount = 0;

	if ($verbose) echo "Connected to server: $server\n";

	$rs_tables = mysql_query("SHOW TABLES");
	if (!$rs_tables || (($num_tables = mysql_num_rows($rs_tables)) <= 0) ) {
		echo "Could not iterate database tables\n";
	}
	else {
		if ($verbose) echo "Number of tables: $num_tables\n";

		$bOk = true;
		$checktype = "";

		if ($fast) $checktype = "FAST";

		while (list($tname) = mysql_fetch_row($rs_tables)) {
			if (substr($tname, 0, 2) != "3_")
				continue;

	    $query = "CHECK TABLE `$tname` $checktype";
	    if ($verbose) printf("%3d. $query:\n", ++$tcount);

			$rs_status = mysql_query( $query );
			if (!$rs_status || mysql_num_rows($rs_status) <= 0 ) {
				$msg .= "Could not get status for table $tname\n";
				$bOk = false;
				continue;
			}

			// seek to last row
			mysql_data_seek($rs_status, mysql_num_rows($rs_status)-1);
			$row_status = mysql_fetch_assoc($rs_status);

			if ($row_status['Msg_type'] != "status") {
				$msg .= "Table {$row_status['Table']}: ";
				$msg .= "{$row_status['Msg_type']} = {$row_status['Msg_text']}\n";
				$bOk = false;
				if ($verbose) echo "  ** Check failed!!\n";
			}
			if ($verbose) {
				echo "       {$row_status['Msg_type']} -> {$row_status['Msg_text']}\n";
			}

		}

		if ( ! $bOk ) echo "Check failed: \n\n" . $msg;
	}
}

$limiter_zprav = $time-(60*60*2);
$limiter_chatu = $time-(60*7);
$limiter_login = $time-(60*30);
$limiter_loginB = $time-(60*60);
$limiter_activ = $time-(60*15);
$limiter_activB = $time-(60*25);

	$sql1 = $sql2 = $sql3 = "";
$delCache = false;

mysql_query ("UPDATE 3_users SET last_login = timestamp, timestamp = '0', online = '0' WHERE online='1' AND timestamp < $limiter_login AND level < 2");
if (mysql_affected_rows() > 0) {
	$delCache = true;
}
//mysql_query ("UPDATE 3_users SET timestamp = 0, online = 0 WHERE timestamp < $limiter_login AND timestamp > 0 AND level < 2");

mysql_query ("UPDATE 3_users SET last_login = timestamp, timestamp = '0', online = '0' WHERE online='1' AND timestamp < $limiter_loginB AND level >= 2");
if (mysql_affected_rows() > 0) {
	$delCache = true;
}
//mysql_query ("UPDATE 3_users SET timestamp = 0, online = 0 WHERE timestamp < $limiter_loginB AND timestamp > 0 AND level >= 2");

mysql_query("UPDATE 3_users SET timestamp = '0' WHERE online = '0'");
if (mysql_affected_rows() > 0) {
	$delCache = true;
}

if ($delCache) {
//	$AragornCache->delVal("users-online:count");
}

mysql_query ("DELETE FROM 3_chat_mess WHERE time < $limiter_zprav");
mysql_query ("DELETE FROM 3_chat_users WHERE odesel='1' AND timestamp < $limiter_chatu");

//vyfakuje uzivatele z chatu
function terminateUser($uid, $rid){
global $time, $sql3, $AragornCache;
	if (isset($AragornCache)) {
		$cachedVal = $AragornCache->getVal("chat-room-".$rid.":users-".$uid);
		if ($cachedVal !== false && $cachedVal['odesel'] != 1) {
			$cachedVal['odesel'] = 1;
			$cachedVal['timestamp'] = $time;
			$AragornCache->replaceVal("chat-room-".$rid.":users-".$uid, $cachedVal, 120);
		}
	}
	$sql3 .= "UPDATE 3_chat_users SET odesel = '1', timestamp = '$time' WHERE uid = $uid AND rid = $rid;
";
  mysql_query ("UPDATE 3_chat_users SET odesel = '1', timestamp = '$time' WHERE uid = $uid AND rid = $rid");
  
}

//vykopnuti neaktivnich z ajax_chatu
$inactives = $sql = $terminate = array();
$rid = 1;

$cachedVal = $AragornCache->getVal("chat-room-".$rid.":activity-check");
if ($cachedVal !== false && $cachedVal !== 0) {
}
else {
	$inactive = mysql_query ("SELECT u.login, u.timestamp, c.uid, u.level, c.timestamp AS ctime FROM 3_chat_users AS c, 3_users AS u WHERE c.uid = u.id AND c.rid = 1 AND (c.timestamp < $limiter_activ OR u.online = 0) AND c.odesel = '0'");
	if ($inactive && mysql_num_rows($inactive)>0) {
		while($item = mysql_fetch_row($inactive)){
			$inactives[] = $item;
		}
	}
}

if (count($inactives) > 0) {
	foreach($inactives as $item) {
		if ($item[1] == 0 || $item[3] < 2 || ($item[3] >= 2 && $item[4] < $limiter_activB)) {
			$uk = $item[0];
			if ($item[1] > 0) {
				$text = addslashes("$uk vyhozen(a) z místnosti pro dlouhodobou neaktivitu.");
			}
			else {
				$text = addslashes("$uk se odhlásil(a) ze serveru.");
			}
			$sql3 .= $text." >> ";

			$sql[] = $text;
			$terminate[] = $item[2];

//			ajaxChatInsertSystem($text, 1);
//			terminateUser($item[2], 1);
		}
	}
	$AragornCache->replaceVal("chat-room-".$rid.":activity-check", 0, 60);
}


foreach($sql as $k => $v) {
	ajaxChatInsertSystem($v, 1);
}
foreach($terminate as $k => $v) {
	terminateUser($v ,1);
}

//vykopnuti neaktivnich z ostatnich mistnosti
$chatKick = mysql_query ("SELECT u.login, u.timestamp, c.rid, c.uid, u.level FROM 3_users AS u, 3_chat_users AS c WHERE u.id = c.uid AND c.rid != 1 AND c.odesel = '0' AND c.timestamp < $limiter_activ ORDER BY c.timestamp ASC");

$insert = $update = array();

if ($chatKick && mysql_num_rows($chatKick) > 0) {
	while ($chItem = mysql_fetch_object($chatKick)){
		if ($chItem->timestamp == 0 || $chItem->level < 2 || ($chItem->level >= 2 && $chItem->timestamp < $limiter_activB)) {
			$uk = $chItem->login;
			if ($chItem->timestamp > 0) {
				$text = addslashes("$uk vyhozen(a) z místnosti pro dlouhodobou neaktivitu.");
			}
			else {
				$text = addslashes("$uk se odhlásil(a) ze serveru.");
			}
//			mysql_query("UPDATE 3_chat_users SET odesel='1' WHERE uid = $chItem->uid AND rid=$chItem->rid");
//			mysql_query("INSERT INTO 3_chat_mess (uid, rid, wh, time, text) VALUES (0, $chItem->rid, 0, $time, '$text')");

			$insert[] = "UPDATE 3_chat_users SET odesel='1' WHERE uid = $chItem->uid AND rid=$chItem->rid";
			$update[] = "INSERT INTO 3_chat_mess (uid, rid, wh, time, text) VALUES (0, $chItem->rid, 0, $time, '$text')";

			$sql2 .= "UPDATE 3_chat_users SET odesel='1' WHERE uid = $chItem->uid AND rid=$chItem->rid
";
			$sql1 .= "INSERT INTO 3_chat_mess (uid, rid, wh, time, text) VALUES (0, $chItem->rid, 0, $time, '$text')
";
		}
	}
	mysql_free_result($chatKick);
}

foreach($update as $k=>$v){
	mysql_query($v);
}
foreach($insert as $k=>$v){
	mysql_query($v);
}

//vykopnuti neaktivnich z chatu jeskyne
mysql_query("DELETE FROM 3_cave_users WHERE timestamp < $limiter_loginB");


$file="check_cron.txt";
$input = gmdate("D, d M Y H:i:s")."\n ----- \n"."sql1:\n"."\"".$sql1."\"\n"."sql2:\n\"".$sql2."\"\n"."sql3:\n\"".$sql3."\"";
if (is_writable($file)) {
   if (!$handle = fopen($file, 'w')) {
//         echo "<!-- Cannot open file ($file)-->\n";
         exit;
   }

   if (fwrite($handle, $input) === FALSE) {
//       echo "<!-- Cannot write to file ($file)-->\n";
       exit;
   }
  
//   echo "<!-- Success, wrote RSS content to file ($file)-->\n";
  
   fclose($handle);

} else {
   echo "<!-- The file $file is not writable -->\n";
}
//echo "OK";
// echo $input;
?>
