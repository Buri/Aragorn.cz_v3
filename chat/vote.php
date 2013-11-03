<?php

require "../db/conn.php";
$vote = $_GET['vote'];

if($vote != 'leave')
	mysql_query("INSERT INTO 3_chat_votes VALUES(0, ".$_SESSION['uid'].", " . $_GET['id'].", " . ($_GET['vote'] == 'yes' ? 1 : 0) . ")");

$votesq = mysql_query("SELECT (SELECT COUNT(*) FROM 3_chat_votes WHERE rid = " . $_GET['id'] . ") AS total,
(SELECT COUNT(*) FROM 3_chat_votes WHERE rid = " . $_GET['id'] . " AND vote = 1) AS 'yes',
(SELECT COUNT(*) FROM 3_chat_votes WHERE rid = " . $_GET['id'] . " AND vote = 0) AS 'no'");
$usersq = mysql_query("SELECT COUNT(*) AS c FROM 3_chat_users WHERE rid = " . $_GET['id']);
$users = mysql_fetch_object($usersq);
$votes = mysql_fetch_object($votesq);
$del = false;

if($users->c/2 < $votes->yes){
	# zmena	
	$nazevq = mysql_query("SELECT * FROM 3_roz_situace WHERE id = ( SELECT vote_situation FROM 3_chat_rooms WHERE id = " . $_GET["id"] . ")");
	$sit = mysql_fetch_object($nazevq);
	mysql_query("UPDATE 3_chat_rooms SET category = '$sit->category', popis = '".addslashes($sit->nazev)."', vote_uid = 0, vote_situation = NULL WHERE id = " . $_GET['id']);
	$del = true;

	$text = "<span class=\'vypravec\'>".addslashes($sit->nazev)."</span><br />".addslashes($sit->popis);
	mysql_query("INSERT INTO 3_chat_mess (uid, rid, time, text, special, type) VALUES (1, $_GET[id], ".time().", '$text', '$sit->id', 1)");
}

if($users->c/2 <= $votes->no){
	$del = true;
	mysql_query("UPDATE 3_chat_rooms SET vote_uid = 0, vote_situation = NULL WHERE id = " . $_GET['id']);
}

if($del) mysql_query("DELETE FROM 3_chat_votes WHERE rid = " . $_GET['id']);

if($vote == 'leave')
Header('Location: /chat/');

Header('Location: /chat/game.php?id=' . $_GET['id']);
