<?php

if (!isset($noOutputBuffer)) {
	ob_start();
//	ob_start("ob_gzhandler");
}
else {
	header("No-Output-Buffer:true");
}

	//start ses. - musi byt az po ob_start()
	session_cache_expire(300);
	session_set_cookie_params(4800, '/', 'www.aragorn.cz');
	session_start();

/*if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
  header("Content-type: application/xhtml+xml;charset=utf-8");
	$xmlHeader = "<"."?xml version=\"1.0\" encoding=\"utf-8\"?".">\n<!-- hi ie7 -->\n";
}
else {
*/
  header("Content-type: text/html;charset=utf-8");
	$xmlHeader = "";
/*}*/

header("Vary: Accept-Encoding");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()-3600*4) . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("X-UA-Compatible: chrome=1");

if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value) {
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}
	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}
set_magic_quotes_runtime(0);

include "credentials.php";

$spojeni = mysql_connect($se1,$us1,$pa1)/* or die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-language" content="cs" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="pragma" content="no-cache" />
<meta name="Description" content="Aragorn.cz - error DB connect" />
<meta name="Keywords" content="" />
<title>Aragorn.cz - Error DB server connect</title>
<link rel="icon" href="/favicon.gif" type="image/gif" />
<link rel="shortcut icon" href="/favicon.gif" />
</head>
<body>
<h1>Server error !!!</h1>
<b>Nepodarilo se pripojit k serveru s databazi, patrne neni funkcni spojeni k serveru, nebo na serveru nebezi DB.</b>
</body>
</html>')*/;

if ($spojeni === false) mysql_connect($se1,$us1,$pa1);

if ($spojeni !== false) {

mysql_select_db($db1) /* or die ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-language" content="cs" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="pragma" content="no-cache" />
<meta name="Description" content="Aragorn.cz - error DB connect" />
<meta name="Keywords" content="" />
<title>Aragorn.cz - Error DB connect</title>
<link rel="icon" href="/favicon.gif" type="image/gif" />
<link rel="shortcut icon" href="/favicon.gif" />
</head>
<body>
<h1>DB error !!!</h1>
<b>Nepodarilo se vybrat databazi, bud na serveru neni DB, nebo je spatne nastaveno prihlasovani k DB.</b>
</body>
</html>')*/;

mysql_query("SET NAMES 'utf8'");

function _htmlspec($s) {
	return htmlspecialchars($s,ENT_QUOTES,"UTF-8");
}

}

?>