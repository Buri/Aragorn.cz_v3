<?php
	$errors = array();
	$fl = $dr = $tp = "";
	if (isset($_GET['f'])){
		$fl = $_GET['f'];
	}
	if (isset($_GET['dr'])) $dr = $_GET['dr'];
	if (isset($_GET['tp'])) $tp = $_GET['tp'];

	$fl = str_replace("..", "", $fl);
	$fl = str_replace("/", "", $fl);
	$fl = str_replace("\\", "", $fl);
	$fl = str_replace(":", "", $fl);
	$fl = str_replace("~", "", $fl);

	if (file_exists("./$dr/".$fl)) {
		$md = md5($dr."/".$fl);
		$cch = "./cache/".$md;
		$fl = "./$dr/".$fl;
		if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") !== false) {
			$cchTest = $cch.".gzip.gz";
			$enc = "gzip";
		}
		elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "deflate") !== false) {
			$cchTest = $cch.".deflate.gz";
			$enc = "deflate";
		}
		else {
			$cchTest = "x";
		}

		if (strlen($cchTest)>2 && !file_exists($cchTest)) {

			$data = file_get_contents($fl);
			ob_start("ob_gzhandler");
			echo $data;
			$data = ob_get_contents();
			ob_flush();

			if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") !== false) {
				$gzdata = gzencode($data, 9);
				$fl = $cchTest;
				$fp = fopen($fl, "w");
				fwrite($fp, $gzdata);
				fclose($fp);
			}
			elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "deflate") !== false) {
				$gzdata = gzdeflate($data, 9);
				$fl = $cchTest;
				$fp = fopen($fl, "w");
				fwrite($fp, $gzdata);
				fclose($fp);
			}
		}
		else {
			if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") !== false || strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "deflate") !== false) {
				$fl = $cchTest;
				header("Content-Encoding: $enc");
				header("Vary: Accept-Encoding");
				$size = filesize($fl);
				header("Last-Modified: " .gmdate("D, d M Y H:i:s", filemtime($fl)). " GMT");
				header("Content-Length: $size");
				header("Content-disposition: inline; filename=$_GET[f]");
				header("Content-Type: text/$tp; charset=utf-8");
				header("X-Powered-By: PHP (AragornPreparedFile)");
				echo file_get_contents($fl, 'FILE_BINARY');
			}
			else {
				header("Last-Modified: " .gmdate("D, d M Y H:i:s", filemtime($fl)). " GMT");
				header("Content-disposition: inline; filename=$_GET[f]");
				header("Content-Type: text/$tp; charset=utf-8");
				header("X-Powered-By: PHP (AragornDirectFile)");
				include($fl);
			}
		}
	}
	else {
		include("./error404.php");
		exit;
	}
?>