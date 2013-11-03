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
		if (is_file("./js/".$joined.".joined.js")) {
			ob_start("ob_gzhandler");
			header("Content-type:text/javascript;charset=utf-8");
			//Caching
			$cacheTime = filemtime("./js/".$joined.".joined.js");
			header("Cache-Control: must-revalidate");
			header("Last-Modified: ".gmdate("D, d M Y H:i:s",$cacheTime)." GMT");
			header("Expires: " . gmdate("D, d M Y H:i:s", $cacheTime + 86400) . " GMT");
      header('Cache-Control: max-age=604800');
			readfile("./js/".$joined.".joined.js", false);
		}
		else {
			$files = explode("|", str_replace(".", "", $_GET['files']));
			$valid = array();

			foreach($files as $v){
				if (is_file("./js/".$v.".js")) $valid[] = $v;
			}
			if (count($valid)>0) {
				ob_start("ob_gzhandler");
				$joined = md5(join("|",$valid)).$ver;
				if (is_file("./js/".$joined.".joined.js")) {
					header("Content-type:text/javascript;charset=utf-8");
					readfile("./js/".$joined.".joined.js", false);
				}
				else {
					header("Content-type:text/javascript;charset=utf-8");
					$buffer = "";
					foreach($valid as $f) {
						readfile("./js/".$f.".js", false);
						echo "\n\n";
					}
					$fp = @fopen("./js/".$joined.".joined.js","w+");
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