<?php
if (!$hFound || !$LogedIn) {
  Header ("Location: $inc/herna/");
	exit;
}

if ($hItem->schvaleno == '0') {
  Header ("Location: $inc/herna/$slink/");
	exit;
}

$okPostava = false;
$cid = $hItem->id;
$cUsrc = mysql_query("SELECT * FROM 3_cave_users WHERE uid = '$_SESSION[uid]' AND cid = '$cid'");
if (mysql_num_rows($cUsrc)>0) {
	for ($a=0;$a<count($jeskyneHraci);$a++) {
		if ($jeskyneHraci[$a]['objekt']->uid == $_SESSION['uid'] && $jeskyneHraci[$a]['objekt']->schvaleno == "1") {
			$postava = $jeskyneHraci[$a]['objekt'];
			$okPostava = true;
			mysql_query ("UPDATE 3_cave_users SET jmeno = '".addslashes($postava->jmeno)."', jmeno_rew = '".addslashes($postava->jmeno_rew)."', timestamp = $time, pozice = 'h' WHERE uid = '$_SESSION[uid]' AND cid = '$cid'");
			break;
		}
	}
	if ($hItem->uid == $_SESSION['uid'] || $allowsPJ['prispevky']) {
		mysql_query ("UPDATE 3_cave_users SET timestamp = $time, pozice = 'p' WHERE uid = '$_SESSION[uid]' AND cid = '$cid'");
	}
	elseif (!$okPostava) {
		mysql_query ("UPDATE 3_cave_users SET timestamp = $time, pozice = 'g' WHERE uid = '$_SESSION[uid]' AND cid = '$cid'");
	}
}
else {
	if ($_SESSION['uid'] == $hItem->uid || $allowsPJ['prispevky']) {
		mysql_query("INSERT INTO 3_cave_users (uid, cid, timestamp, pozice, login, login_rew) VALUES ($_SESSION[uid], $cid, $time, 'p', '$_SESSION[login]', '".do_seo($_SESSION['login'])."')");
		$text = "PJ ".addslashes($_SESSION['login']." přichází do místnosti.");
		if ($_SESSION['uid'] != $hItem->uid) {
			$text = "Pomocný ".$text;
		}
		mysql_query("INSERT INTO 3_cave_mess (uid, cid, time, text) VALUES (0, $cid, $time, '$text')");
	}
	else {
		for ($a=0;$a<count($jeskyneHraci);$a++) {
			if ($jeskyneHraci[$a]['objekt']->uid == $_SESSION['uid'] && $jeskyneHraci[$a]['objekt']->schvaleno == "1") {
				$postava = $jeskyneHraci[$a]['objekt'];
				$okPostava = true;
				mysql_query ("UPDATE 3_cave_users SET jmeno = '".addslashes($postava->jmeno)."', jmeno_rew = '".addslashes($postava->jmeno_rew)."', timestamp = $time, pozice = 'h' WHERE uid = '$_SESSION[uid]' AND cid = '$cid'");
				break;
			}
		}
		if ($okPostava) {
			mysql_query("INSERT INTO 3_cave_users (uid, cid, jmeno, jmeno_rew, timestamp, pozice, login, login_rew) VALUES ($_SESSION[uid], $cid, '".addslashes($postava->jmeno)."', '".addslashes($postava->jmeno_rew)."', $time, 'h', '$postava->login', '$postava->login_rew')");
			$text = "".addslashes($_SESSION['login']." přichází do místnosti.");
			mysql_query ("INSERT INTO 3_cave_mess (uid, cid, time, text) VALUES (0, $cid, $time, '$text')");
		}
		else {
		  mysql_query("INSERT INTO 3_cave_users (uid, cid, timestamp, login, login_rew) VALUES ($_SESSION[uid], $cid, $time, '$_SESSION[login]', '".do_seo($_SESSION['login'])."')");
		}
	}
}

Header ("Location:$inc/cave/$hItem->nazev_rew/");
exit;
?>
