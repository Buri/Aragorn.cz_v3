<?php
	function getCachedFile($dr="",$f=false,$v=0,$type="css"){
		$returnString = "";
		$ver = "";
		if ($v) {
			$v = str_replace(".", "", trim($v));
			if ($v != "" && ctype_alnum($v)) {
				$ver = ".v".$v;
			}
		}
	
		if ($f) {
			$joined = md5($f).$ver;

			include "./$dr/cache-is-ok.php";
			$ok = strtoupper($dr)."_cache_is_ok";
			if ($$ok) {
				return $returnString = "_".$joined.".joined.$type";
			}

			clearstatcache();

			if (is_file("./$dr/_".$joined.".joined.$type")) {
				$returnString = "_".$joined.".joined.$type";
			}
			else {
				$files = explode("|", str_replace(".", "", $f));
				$valid = array();
	
				foreach($files as $q){
					if (is_file("./$dr/".$q.".$type")) $valid[] = $q;
				}
				if (count($valid)>0) {
					$joined = md5(join("|",$valid)).$ver;
					$returnString = "_".$joined.".joined.$type";
					$buffer = "";
					foreach($valid as $f) {
						$buffer .= file_get_contents("./$dr/".$f.".$type", false);
						$buffer .= "\n\n";
					}
					$fp = @fopen("./$dr/_".$joined.".joined.$type","w+");
					@fwrite($fp,$buffer);
					@fclose($fp);
					unset($buffer);
				}
			}
		}
		return $returnString;
	}
?>