<?php
session_start();
$allowed = false;
if (isset($_SESSION['lvl']) && isset($_SESSION['login_rew'])){
	if ($_SESSION['login_rew'] == "apophis"){
		$allowed = true;
	}
}
if (!$allowed) {
	die("Error! Jen pro povolane lidi!");
	exit;
}

	$noOutputBuffer = 1;
	include "db/conn.php";

/*
	mysql_query("

	INSERT INTO `3_comm_4_texts` SELECT id AS text_id, text AS text_content, compressed AS text_compressed, whisText AS text_whisText FROM 3_comm_4;
	INSERT INTO `3_comm_3_texts` SELECT id AS text_id, text AS text_content, compressed AS text_compressed FROM 3_comm_3;
	INSERT INTO `3_comm_2_texts` SELECT id AS text_id, text AS text_content, compressed AS text_compressed FROM 3_comm_2;
	INSERT INTO `3_comm_1_texts` SELECT id AS text_id, text AS text_content, compressed AS text_compressed FROM 3_comm_1;

	UPDATE 3_comm_1 SET mid = id;
	UPDATE 3_comm_2 SET mid = id;
	UPDATE 3_comm_3 SET mid = id;
	UPDATE 3_comm_4 SET mid = id;


");
*/	

	exit;


	$doDecompress = 0;
	if (isset($_GET['w'])) $doDecompress = $_GET['w'];
	if ($doDecompress > 0) $doDecompress = 1;
	else $doDecompress = 0;

	if(isset($_GET['s']) && isset($_GET['i'])){
		if (ctype_digit($_GET['s']) && ctype_digit($_GET['i'])){
//			$cS = mysql_query("SELECT id,text,compressed FROM 3_comm_$_GET[s] WHERE compressed = ".$doDecompress." ORDER BY id ASC LIMIT $_GET[i],1000");
			$cS = mysql_query("SELECT id,text,compressed FROM 3_comm_$_GET[s] ORDER BY id ASC LIMIT $_GET[i],1000");
			$cnt=0;
			if ($doDecompress){
				while($c = mysql_fetch_row($cS)){
					if ($c[2]==0) continue;
		
					$binarka = addslashes(gzuncompress($c[1]));
					if (strlen($binarka) < 1) {
						echo "ERROR!!!";
						break;
					}
					mysql_query("UPDATE 3_comm_$_GET[s] SET text = '$binarka', compressed = 0 WHERE id = '$c[0]'");
					if (mysql_affected_rows()) $cnt++;
				}
			}
			else {
				while($c = mysql_fetch_row($cS)){
					if ($c[2]>0) continue;
	
					$binarka = gzcompress($c[1],9);
					if (strlen($binarka) < strlen($c[1])) {
						$binarka = bin2hex($binarka);
						mysql_query("UPDATE 3_comm_$_GET[s] SET text = 0x$binarka, compressed = 1 WHERE id = '$c[0]'");
						if (mysql_affected_rows()) $cnt++;
					}
				}
			}
			if ($cnt > 0) echo $cnt;
			else echo "nothing done";
		}
		else {
			$cS = mysql_query("SELECT id,text,compressed FROM 3_clanky ORDER BY id ASC LIMIT $_GET[i],100");
			$cnt=0;
			if ($doDecompress){
				while($c = mysql_fetch_row($cS)){
					if ($c[2]==0) continue;

					$binarka = addslashes(gzuncompress($c[1]));
					if (strlen($binarka) < 1) {
						echo "ERROR!!!";
						break;
					}
					mysql_query("UPDATE 3_clanky SET text = '$binarka', compressed = '0' WHERE id = '$c[0]'");
					if (mysql_affected_rows()) $cnt++;
				}
			}
			else {
				while($c = mysql_fetch_row($cS)){
					if ($c[2]>0) continue;

					$binarka = gzcompress($c[1],9);
					if (strlen($binarka) < strlen($c[1])) {
						$binarka = bin2hex($binarka);
						mysql_query("UPDATE 3_clanky SET text = 0x$binarka, compressed = '1' WHERE id = '$c[0]'");
						if (mysql_affected_rows()) $cnt++;
					}
				}
			}
			if ($cnt > 0) echo $cnt;
			else echo "nothing done";
		}
	}
	else echo "error";

?>