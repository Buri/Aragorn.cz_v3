<?php
// clear reset AjaxChat :)

session_start();

if (isset($_SESSION['lvl']) && isset($_SESSION['login'])) {
	if ($_SESSION['lvl'] > 3 || $_SESSION['login'] == "apophis" || $_SESSION['login'] == "hater") {
		if (!isset($_GET['doIt'])) {
	    header("Content-Type: text/html; charset=utf-8");
			echo "<h1>Seš si jistej?</h1>";
			echo "<p><a href='".$_SERVER['SCRIPT_NAME']."?doIt=1'>Jo, jsem si jistej...</a></p>";
			exit;
		}
		include "./db/conn.php";
    header("Content-Type: text/html; charset=utf-8");
		mysql_query("DELETE FROM 3_chat_users WHERE rid = '1'");
		mysql_query("TRUNCATE 3_ajax_chat");
		mysql_query("ALTER TABLE 3_ajax_chat AUTO_INCREMENT = 1");
		echo "Updated table with chat users, truncated ajax chat messages, altered auto increment :)\n";
		ob_end_flush();
		echo "Unlocked tables :)\n";
    sleep(1);
    echo "done";
    sleep(1);
    echo "<script type='text/javascript'>setTimeout(function(){window.close();},3000);</script>";
	}
	else {
		die(':P');
	}
}
else {
	die(':P');
}

?>
