<?php
/*
clanky - 1
galerie - 2
diskuze - 3
herna - 4
*/

$s_id = 0;
$addUrl = array();

switch ($link) {
	case "clanky":
	  $s_id=1;
	  if (isset($_GET['c'])) {
	    if (ctype_digit($_GET['c'])) $addUrl[] = "sekce=".$_GET['c'];
		}
	break;
	case "galerie":
	  $s_id=2;
	break;
	case "diskuze":
	  $s_id=3;
	  if (isset($_GET['o'])) {
	    if (ctype_digit($_GET['o'])) $addUrl[] = "oblast=".$_GET['o'];
		}
	break;
	case "herna":
	  $s_id=4;
	  if (isset($_GET['c'])) {
	    if ($_GET['c'] != "") $addUrl[] = "sekce=".$_GET['c'];
		}
	  if (isset($_GET['p'])) {
	    if ($_GET['p'] != "") $addUrl[] = "podle=".$_GET['p'];
		}
	break;
	default: // uzivatelsky profil
	  if (isset($_GET['s'])) {
	    if (ctype_digit($_GET['s']) && $_GET['s'] <= 4 && $_GET['s'] >= 1) {
	      $s_id = $_GET['s'];
			}
		}
	break;
}

if ($s_id == 0) {
	Header ("Location:$inc/");
	exit;
}

if (!$LogedIn || !isset($_GET['a'])) {
	Header ("Location:$inc/$link");
	exit;
}

$a_id = addslashes($_GET['a']);

mysql_query("DELETE FROM 3_visited_$s_id WHERE uid = '$_SESSION[uid]' AND aid = '$a_id'");

if ($slink != "" && $link == "uzivatele") {
	Header ("Location:$inc/$link/$slink/");
	exit;
}

if (isset($_GET['i'])) {
	if (ctype_digit($_GET['i']) && $_GET['i'] != "" && $_GET['i'] != "0") {
	  $addUrl[] = "index=".$_GET['i'];
	}
}

if (count($addUrl)>0) {
	$addUrl = "?".join("&",$addUrl);
}
else $addUrl = "";

Header ("Location:$inc/$link/$addUrl");
exit;
?>
