<?php
mb_internal_encoding("UTF-8");

$error = $ok = 0;

if (!$LogedIn || $hFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}
if ($sslink != "pj") {
	header ("Location: $inc/herna/$slink/");
	exit;
}

if ($hItem->uid == $_SESSION['uid'] && isset($_POST['akce_ppj'])) {
	$subakce = $_POST['akce_ppj'];
	if ($subakce == 'update') {
		if (isset($_POST['uzivatel'])) {
			$uzivatel = $_POST['uzivatel'];
			if (is_array($uzivatel)) {
				$al = array('poznamky', 'obchod', 'mapy', 'prispevky', 'nastenka', 'postavy', 'schvaleno');
				$c = count($al);
				$us = array();

				$users = $_POST['uzivatel'];
				$users = array_map('addslashes', $users);
				$idLoginRewS = mysql_query("SELECT id, login_rew FROM 3_users WHERE login_rew IN ('".join($users, "', '")."')");
				while($q = mysql_fetch_row($idLoginRewS)) {
					$us[$q[1]] = $q[0];
				}

				foreach($uzivatel as $v) {
					$up = array();
					if (isset($_POST['p']) && isset($_POST['p'][$v]) && isset($us[$v])) {
						$updt = array();
						for ($a = 0; $a < $c; $a++) {
							if (isset($_POST['p'][$v][$al[$a]])) {
								$updt[] = "$al[$a] = '1'";
							}
							else {
								$updt[] = "$al[$a] = '0'";
							}
						}
						$updt = join($updt, ", ");
						$up[] = "UPDATE 3_herna_pj SET ".$updt." WHERE cid = $hItem->id AND uid = $us[$v];";
					}
				}

				if (count($up) > 0) {
					$sql = join($up, "\n\n");
					mysql_query($sql);
					header("Location: $inc/herna/$slink/pj/?ok=1");
					exit;
				}
			}
		}
	}
	elseif ($subakce == 'delete') {
		if ($hItem->PJs && count($hItem->PJs) > 0) {
			$uids = array();
			foreach($hItem->PJs as $k=>$v) {
				if (isset($_POST['uzivatel'][$v->login_rew])) {
					$uids[] = (int)$v->uid;
				}
			}
			if (count($uids) > 0) {
				$pjs_S = mysql_query("SELECT ico FROM 3_herna_pj WHERE cid = '$hItem->id' AND uid IN (".join($uids,",").")");
				if (mysql_num_rows($pjs_S)>0) {
					while ($pjs = mysql_fetch_row($pjs_S)) {
						if ($pjs[0] != 'default.jpg' && $pjs[0] == '') {
							@unlink("./system/icos/$pjs[0]");
						}
					}
				}
				mysql_query("DELETE FROM 3_herna_pj WHERE cid = $hItem->id AND uid IN (".join($uids,",").")");
				header("Location: $inc/herna/$slink/pj/?ok=1");
				exit;
			}
		}
	}
	elseif ($subakce == 'create') {
		if (isset($_POST['new_ppj'])) {
			$new_ppj = do_seo(trim($_POST['new_ppj']));
			$sqa = mysql_query("SELECT id, level FROM 3_users WHERE login_rew = '".$new_ppj."' OR login = '".addslashes($_POST['new_ppj'])."'");
			if ($sqa && mysql_num_rows($sqa) > 0) {
				$sq = mysql_fetch_row($sqa);

				if (herna_omezeni($sq[0],$sq[1]) < $herna_nebonus && !isset($uzivateleVeHre[$sq[0]])) {
					$counter = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_pj WHERE cid = $hItem->id"));
					$counter = $counter[0];
					if ($counter < 3) {
						mysql_query("INSERT INTO 3_herna_pj (cid, uid) VALUES ($hItem->id, $sq[0])");
						header("Location: $inc/herna/$slink/pj/?ok=1");
						exit;
					}
					else {
						header("Location: $inc/herna/$slink/pj/?error=26");
						exit;
					}
				}
				else {
					header("Location: $inc/herna/$slink/pj/?error=25");
					exit;
				}
			}
			else {
				header("Location: $inc/herna/$slink/pj/?error=24");
				exit;
			}
		}
	}

}

header("Location: $inc/herna/$slink/pj/");
exit;
?>