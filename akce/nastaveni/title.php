<?php
// title

if (isset($_POST['activation']) && $_SESSION['lvl'] >= 2) {
	if ($_POST['activation'] == "0") {
		$titlees = 0;
	}
	else {
		$titlees = 1;
	}
	mysql_query("UPDATE 3_users SET set_titles = '$titlees' WHERE id = $_SESSION[uid]");

	$_SESSION['titles'] = $titlees;

	Header("Location:$inc/nastaveni/systemove/?ok=9");
	exit;
}

//uspesny redirect
Header ("Location:$inc/nastaveni/systemove/");
exit;
?>
