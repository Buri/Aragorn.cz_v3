<?php

if (!$LogedIn || !isset($_POST['filter']) || $_SESSION['lvl'] < 2) {
	header("Location: $inc/herna/");
	exit;
}
else {

	$filter = trim($_POST['filter']);
	if (strlen($filter) > 1) {
		$f = explode(" ", $filter);
		foreach($f as $k) {
			$a = trim(trim($k), '*%_+-');
			if (strlen($a) < 4) {
				header("Location: $inc/herna/?error=15");
				exit;
			}
		}
	}

	$sets = mysql_query("SELECT serialized FROM 3_users_settings WHERE uid = '$_SESSION[uid]'");
	if ($sets && mysql_num_rows($sets) > 0) {
		$exist = 1;
		$s = mysql_fetch_object($sets);
		$json = json_decode($s->serialized, true);
	}
	else {
		$exist = 0;
		$json = array();
	}


	if (mb_strlen($filter) > 1) {
		$json['game-filter'] = array($filter);
	}
	else {
		unset($json['game-filter']);
	}

	$json = addslashes(json_encode($json));

  if ($exist) {
  	$sql = "UPDATE 3_users_settings SET serialized = '".$json."' WHERE uid = '$_SESSION[uid]'";
	}
	else {
  	$sql = "INSERT INTO 3_users_settings (uid, serialized) VALUES ($_SESSION[uid], '".$json."')";
	}

	mysql_query($sql);

}

header("Location: $inc/herna/");
exit;

?>