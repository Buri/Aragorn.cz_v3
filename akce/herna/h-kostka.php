<?php
if (!$LogedIn || $hFound != true) {
	header ("Location: $inc/herna/");
	exit;
}

$jsemIn = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE cid = $hItem->id AND uid = $_SESSION[uid] AND schvaleno = '1'"));

if ($hItem->uid == $_SESSION['uid'] || $allowsPJ['prispevky'] || $jsemIn[0] > 0) {
	switch ($akce) {
		case "k6":
			$hozeno = "k6$hCh" . mt_rand(1,6);
		break;
		case "k10":
			$hozeno = "k10$hCh" . mt_rand(1,10);
		break;
		case "k20":
			$hozeno = "k20$hCh" . mt_rand(1,20);
		break;
		case "4k6":
			$hozeno = "4k6$hCh";
			$d = array();
			for($a=0;$a<4;$a++) {
				$b = mt_rand(1, 6);
				$hozeno .= "$b, ";
				$d[] = ($b > 4 ? '+' : ($b < 3 ? '-' : '0'));
			}
			$hozeno .= " (" . join(", ", $d) . ")";
		break;
		case "k100":
			$hozeno = "k%$hCh" . mt_rand(1,100);
		break;
		case "2k6plus":
		  $a = mt_rand(1,6);
		  $b = mt_rand(1,6);
		  $c = 0;
		  $d = array();
		  $hozeno = "kP$hCh";
			if (($a+$b) == 12) { // padly 2 sestky
			  $c = mt_rand(1,6);
			  while ($c >= 4) {
			    $d[] = $c."(+1)";
				  $c = mt_rand(1,6);
				}
			}
			elseif (($a+$b) == 2) { // padly 2 jednicky
			  $c = mt_rand(1,6);
			  while ($c <= 3) {
			    $d[] = $c."(-1)";
				  $c = mt_rand(1,6);
				}
			}
			if (count($d)>0) {
			  if ($a+$b == 2) {
			    $hozeno .= (($a+$b)-count($d)) . $hCh . $a . $hCh . $b . $hCh . join(", ",$d) . ", " . $c."(0)";
				}
				else {
			    $hozeno .= (($a+$b)+count($d)) . $hCh . $a . $hCh . $b . $hCh . join(", ",$d) . ", " . $c."(0)";
				}
			}
			elseif ($c > 0) {
			  $hozeno .= ($a+$b) . $hCh . $a . $hCh . $b . $hCh . $c."(0)";
			}
			else {
			  $hozeno .= ($a+$b) . $hCh . $a . $hCh . $b;
			}
		break;
		case "XkY":
		  if ( isset($_GET['x']) && isset($_GET['y']) ) {
		    $x = $_GET['x'];
		    $y = $_GET['y'];
		    if (ctype_digit($x) && ($x > 0) && ctype_digit($y) && ($y > 0) && ($x <= 30) && ($y <= 10000)) {
		      $hozeno = "kX$hCh";
		      $d = array();
		      for ($i = 0; $i < $x; $i++) {
						$d[] = mt_rand(1,$y);
					}
					$hozeno .= $x . $hCh . $y . $hCh . join(", ",$d);
				}
				else {
				  header("Location: $inc/herna/$slink/?jjj#kom");
				  exit;
				}
			}
			else {
			  header("Location: $inc/herna/$slink/?aaa#kom");
			  exit;
			}
		break;
	}
	if ($hItem->uid == $_SESSION['uid'] || $allowsPJ['prispevky']) {
		$kk = array();
		foreach ($hItem->PJs as $k) {
			$kk[] = $k->uid;
		}
		if ($hItem->uid == $_SESSION['uid']) {
			array_unshift($kk, $hItem->uid);
		}
		else {
			$kk[] = $hItem->uid;
		}
		$komu = join('#', $kk);
	}
	else {
		$kk = array();
		$kk[] = $_SESSION['uid'];
		$kk[] = $hItem->uid;
		foreach($hItem->PJs as $k) {
			$kk[] = $k->uid;
		}
		$komu = join("#", $kk);
	}
	$text = $hozeno;
	$text = addslashes($text);
	mysql_query("INSERT INTO 3_comm_4_texts (text_content) VALUES ('$text')");
	$lid = mysql_insert_id();
	mysql_query("INSERT INTO 3_comm_4 (mid, uid, whispering, time, aid) VALUES ('$lid', '0', '#$komu#', '$time', '$hItem->id')");
	mysql_query("UPDATE 3_herna_all SET ohrozeni='0', aktivita = $time WHERE id = $hItem->id");
	header ("Location: $inc/herna/$slink/#kom");
	exit;
}
else {
	header ("Location: $inc/herna/$slink/");
	exit;
}

?>
