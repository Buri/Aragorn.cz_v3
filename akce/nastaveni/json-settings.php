<?php
// chutovky

if (isset($_SESSION['login']) && isset($_POST['chutovky']) && isset($_POST['be-help'])) {

	if ($_POST['chutovky'] == "0") {
		$chut = 0;
	}
	else {
		$chut = 1;
	}

	if ($_POST['be-help'] == "0") {
		$help = 0;
		mysql_query("DELETE FROM 3_help WHERE uid = $_SESSION[uid]");
	}
	else {
		$help = 1;
		mysql_query("INSERT INTO 3_help (uid) VALUES ($_SESSION[uid])");
	}

	$s = mysql_query("SELECT uid, serialized FROM 3_users_settings WHERE uid = '$_SESSION[uid]'");

	if ($s && mysql_num_rows($s) > 0) {
		$s = mysql_fetch_object($s);
		$s = json_decode($s->serialized, true);
		$s['chut'] = $chut;
		$s['help'] = $help;
		$s = mysql_escape_string(json_encode($s));
		mysql_query("UPDATE 3_users_settings SET serialized = '".$s."' WHERE uid = '$_SESSION[uid]'");
	}
	else {
		$s = mysql_escape_string(json_encode(array('chut' => $chut, "help" => $help)));
		mysql_query("INSERT INTO 3_users_settings (uid, serialized) VALUES ($_SESSION[uid], '$s')");
	}

	$_SESSION['chut'] = $chut;
	$_SESSION['help'] = $help;

	Header("Location:$inc/nastaveni/systemove/?ok=10");
	exit;
}

//uspesny redirect
Header ("Location:$inc/nastaveni/systemove/");
exit;
?>
