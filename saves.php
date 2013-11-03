<?php
ob_start("ob_gzhandler");
session_start();
$time = time();
$LogedIn = false;
if (isset($_SESSION['uid']) && isset($_GET['id'])) {
	include "./db/conn.php";
	$LogMeIn = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_chat_admin WHERE uid = '$_SESSION[uid]' AND typ = '1'"));
	if ($LogMeIn[0]>0 || $_SESSION['lvl']>=3) {
		$LogedIn = true;
	}
	else {
		die("Pristup jen prihlasenym Spravcum Rozcesti nebo Administratorum Aragorn.cz!");
	}
	$rid = addslashes(intval($_GET['id']));
  $toShow = mysql_query("SELECT * FROM 3_chat_save_data WHERE aktivni = '0' AND id = '$rid'");
	if ($toShow && mysql_num_rows($toShow)>0) {
		$dataO = mysql_fetch_object($toShow);
		mysql_free_result($toShow);
	}
	else {
	  die("Error! Zaznam nenalezen!");
	}
}else {
	die("Error! Pristup jen prihlasenym uzivatelum, nebo spatny tvar adresy!");
}

$error = $ok = 0;

if (!$LogedIn || !$dataO) {
	header("Location: http://".$_SERVER['HTTP_HOST']);
	exit;
}

/* Export START */

	$sufix = "html";

		$message="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' />
<title>Export chatu Rozcestí</title>
<style>body { background-color: black; color: white; font: 85%/1.2 Tahoma, Arial, lucida, sans-serif; text-align: center; margin: 4em auto; }
p { margin: 0.7em; direction: ltr; text-align: justify; font-size: 1em; background-color: #151515; padding: 0.5em; border: 1px solid #272727; }
.centerize { margin: 0 auto; text-align: left; width: 700px; }
</style>
</head>
<body>
<div class='centerize'>
<h2>Export chatu Rozcestí ".date("d.m.Y H:i:s",$dataO->timeStart)." - ".date("d.m.Y H:i:s",$dataO->timeEnd)."</h2><hr />";

	//seznam nicku na septani
	$sw = mysql_query ("SELECT distinct(u.id), u.login FROM 3_chat_save_text AS m, 3_users AS u WHERE u.id = m.tid AND m.rid = '$dataO->rid' AND m.tid!=0 AND m.id>=$dataO->fromId AND m.id<=$dataO->toId LIMIT 150");
	$up = array();
	while ($cw = mysql_fetch_object($sw)){
		$up[$cw->id] = $cw->login;
	}

	$sm = mysql_query("SELECT u.login, u.chat_color, c.uid, c.tid, c.text, c.cas FROM 3_chat_save_text AS c, 3_users AS u WHERE u.id = c.uid AND c.rid = '$dataO->rid' AND c.id>=$dataO->fromId AND c.id<=$dataO->toId ORDER BY c.id ASC");
	if ($sm && mysql_num_rows($sm)>0) {
		while ($cm = mysql_fetch_object($sm)){
			$text = $cm->text;
			$aw = $cm->tid;
			$ax = $cm->uid;
			$t = "(".date("H:i", $cm->cas).") ";

			if($cm->tid > 0) {
				$chatName = "$cm->login -&gt; $up[$aw]";
			}
			else {
				$chatName = $cm->login;
			}
			$message .= "<p>".$t."<span style='color: ".$cm->chat_color."'><b>".$chatName."</b>: ".$text."</span></p>\n";
		}
	}
	else {
		$message .= "<big>žádné zprávy ... někde se stala chyba?</big>";
	}

	$message.="\n</div>\n</body>\n</html>\n";


	//zazipovani exportu
/*	include_once "./add/zip.lib.php";
	$zip=new zipfile();
	$zip->addFile($message, "chat_export_$time.html");
	header("Content-Type: application/x-zip");
	header("Content-disposition: attachment; filename=chat"."_export_$time.zip");
	echo $zip->file();
	exit;
*/
echo $message;
	/* Export  END  */
?>