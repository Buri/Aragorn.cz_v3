<?php
	$ver = "";
	if (isset($_GET['v'])) {
		$v = str_replace(".", "", trim($_GET['v']));
		if ($v != "" && ctype_alnum($v)) {
			$ver = ".v".$v;
		}
	}

	if (isset($_GET['files'])) {

		$joined = md5($_GET['files']).$ver;
		if (is_file("./css/".$joined.".joined.css")) {
			ob_start("ob_gzhandler");
			header("Content-type:text/css;charset=utf-8");
			$cacheTime = filemtime("./css/".$joined.".joined.css");
			header("Cache-Control: must-revalidate");
			header("Last-Modified: ".gmdate("D, d M Y H:i:s",$cacheTime)." GMT");
			header("Expires: " . gmdate("D, d M Y H:i:s", $cacheTime + 86400) . " GMT");
      header('Cache-Control: max-age=604800');
			readfile("./css/".$joined.".joined.css", false);
		}
		else {
			$files = explode("|", str_replace(".", "", $_GET['files']));
			$valid = array();

			foreach($files as $v){
				if (is_file("./css/".$v.".css")) $valid[] = $v;
			}
			if (count($valid)>0) {
				ob_start("ob_gzhandler");
				$joined = md5(join("|",$valid)).$ver;
				if (is_file("./css/".$joined.".joined.css")) {
					header("Content-type:text/css;charset=utf-8");
					readfile("./css/".$joined.".joined.css", false);
				}
				else {
					header("Content-type:text/css;charset=utf-8");
					foreach($valid as $f) {
						readfile("./css/".$f.".css", false);
						echo "\n\n";
					}
					$fp = @fopen("./css/".$joined.".joined.css","w+");
					@fwrite($fp,ob_get_contents());
					@fclose($fp);
				}
			}
			else {
				die("/"."* nothing to write *"."/");
			}
		}
	}
?>