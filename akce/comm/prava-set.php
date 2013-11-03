<?php
//postnuti prispevku do diskuze/kommentare/herna
$AllowedTo = 0;

if ($cFound && $LogedIn && isset($_GET['d'])) {
	if ($_SESSION['lvl'] > 2 && $_GET['d'] != "") {
		$d = $_GET['d'];
		$aid = $id;
		$sid = $sid;
		if ($d == "delete" && isset($_POST['nick'])) {
			$str = addslashes(join(",",$_POST['nick']));
			mysql_query("DELETE FROM 3_sekce_prava WHERE uid IN ($str) AND aid = '$aid' AND sid = '$sid'");
		}
		else if ($d == "add") {
			$name = addslashes(do_seo($_POST['nickname']));
			$usrF = mysql_query("SELECT id FROM 3_users WHERE login_rew='$name' AND reg_code = '0' AND id > 1 AND level < 3");
			if ($usrF && mysql_num_rows($usrF)>0) {
				$uidf = mysql_fetch_row($usrF);
				$foundF = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_sekce_prava WHERE uid='$uidf[0]' AND sid='$sid' AND aid='$aid'"));
				if ($foundF[0] == '0') {
					mysql_query("INSERT INTO 3_sekce_prava (uid,aid,sid) VALUES ($uidf[0],$aid,$sid)");
				}
			}
		}
		header("Location: $inc/$link/$slink/ad/");
		exit;
	}
	header("Location: $inc/$link/$slink/");
	exit;
}
header("Location: $inc/");
exit;
?>
