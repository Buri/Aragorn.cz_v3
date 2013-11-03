<?php

$nazev = $popis = $hleda = $adminy = "";
$error = $ok = $hraci_hleda = 0;

if (!$LogedIn) {
	header ("Location: $inc/herna/new/");
	exit;
}
$promenne = array("cave_hraci", "cave_hleda", "cave_nazev", "cave_popis", "cave_adminy", "cave_system");
for ($i = 0; $i<count($promenne);$i++) {
	if (!isSet($_POST[$promenne[$i]])) {
		header ("Location: $inc/herna/new/");
		exit;
	}
}

if (herna_omezeni($_SESSION['uid'],$_SESSION['lvl']) >= $herna_nebonus) {
	header ("Location: $inc/herna/new/?error=1");
	exit;
}

$nazev_rew = do_seo($_POST['cave_nazev']);
$nazev = trim($_POST['cave_nazev']);
$nazev = addslashes(mb_strtoupper(mb_substr($nazev, 0, 1)).mb_substr($nazev,1));
$popis = addslashes(trim($_POST['cave_popis']));
$keywords = addslashes(trim($_POST['cave_keywords']));
$hleda = addslashes(trim($_POST['cave_hleda']));
$hraci = (int)($_POST['cave_hraci']);
$adminy= addslashes(trim($_POST['cave_adminy']));

switch ($_POST["cave_system"]) {
	case "drd":
		$system = 0;
 	break;
 	case "orp":
 		$system = 1;
  break;
  default:
		header ("Location: $inc/herna/new/?error=2");
 		$system = -1;
		exit;
  break;
}

$herSrc = mysql_query ("SELECT count(*) FROM 3_herna_all WHERE nazev = '$nazev' OR nazev_rew = '$nazev_rew'");
$hEx = mysql_fetch_row($herSrc);

if (mb_strlen($nazev) <= 4 || strlen($nazev_rew) <= 4){ // moc kratky nazev
  $error = 3;
}
elseif ($hEx[0] > 0) { // nazev jiz existuje
	$error = 4;
}
elseif(mb_strlen(trim($_POST['cave_nazev'])) > 40 || mb_strlen($nazev_rew) > 40) {
	$error = 5;
}
elseif(mb_strlen($popis)==0) {
	$error = 6;
}
elseif(mb_strlen($adminy)==0) {
	$error = 7;
}
elseif(mb_strlen($hleda)==0) {
	$error = 8;
}
elseif (!is_numeric($_POST['cave_hraci'])) {
	$error = 9;
}
elseif ($hraci > 15 || $hraci < 1) {
	$error = 9;
}
else{
  $error = 0;
}

if ($error>0){
  Header ("Location: $inc/herna/new/?error=$error");
  exit;
}
else {
	if ($system == 0) {
		mysql_query("INSERT INTO 3_herna_all ( uid, typ, nazev, nazev_rew, popis, pro_adminy, hraci_pocet, hraci_hleda, aktivita, aktivitapj, zalozeno".($keywords ? ', keywords' : '').") 
VALUES ('$_SESSION[uid]', '$system', '$nazev', '$nazev_rew', '$popis', '$adminy', '$hraci', '$hleda', $time, $time, $time".($keywords ? ", '$keywords'" : "").")");
  	Header ("Location: $inc/herna/my/?ok=1");
  	exit;
	}
	elseif ($system == 1) {
	  if (isset($_POST['nazev_orp'])) {
	    $orp = array();
	    $_orp_nazev = $_POST['nazev_orp'];
	    $_orp_typ = $_POST['typ_orp'];
	    $_orp_help = $_POST['helping'];
	    $_orp_edit = array();
	    if (isset($_POST['edit'])){
		    $_orp_edit = $_POST['edit'];
			}
	    $_orp_view = array();
	    if (isset($_POST['view'])){
		    $_orp_view = $_POST['view'];
			}
	    $cc = 0;
	    for ($i=0;$i<count($_orp_nazev);$i++) {
				if ($_orp_typ[$i] == "") {
				  continue;
				}
	      elseif (mb_strlen(trim($_orp_nazev[$i])) > 0 && $_orp_typ[$i] != "") {
	        $th = array();
	        $th["add"] = "";
					switch ($_orp_typ[$i]) {
						case "a":
						case "t":
						  $th["typ"] = $_orp_typ[$i];
						break;
						case "n":
						case "r":
							if ($_orp_help[$i] != "") {
								$minmax = explode(":",$_orp_help[$i]);
                if (!is_numeric($minmax[0]) || !is_numeric($minmax[1])) {
                  $error = 10;
                  break;
								}
                sort($minmax);
							  $th["typ"] = $_orp_typ[$i];
							  $th["add"] = ">".$minmax[0].">".$minmax[1];
							}
							else {
								$error = 10;
								break;
							}
						break;
						case "s":
							if ($_orp_help[$i] != "") {
							  $opt = explode(",",$_orp_help[$i]);
							  for($a=0;$a<count($opt);$a++){
							    $opt[$a] = _htmlspec(str_replace($hCh, "-", trim(urldecode($opt[$a]))));
								}
								if (mb_strlen(join("",$opt))<1) {
								  $error = 11;
								}
								$th["typ"] = "s";
								$th["add"] = ">".join(">",$opt);
							}
							else $error = 11;
						break;
						default :
						  $error = 12;
						break;
					}
					if ($error == 0) {
						if (isset($_orp_edit[$i])) {
							if (isset($_orp_view[$i])) $edv = "a";
							else $edv = "e";
						}
						else {
							if (isset($_orp_view[$i])) $edv = "v";
							else $edv = "n";
						}
					  $orp[] = _htmlspec(str_replace($hCh, "-",trim($_orp_nazev[$i]))).">".$th["typ"].">".$edv.$th["add"];
					}
					else {
					  die("Chyba v ORP zadani");
					  break;
					}
				}
				if ($cc >= 15) {
				  break;
				}
				$cc++;
			}
			if ($error > 0) {
			  Header ("Location: $inc/herna/new/?error=$error");
				exit;
			}
			else {
				$orpT = addslashes(join($hCh,$orp));
				mysql_query("INSERT INTO 3_herna_all ( uid, typ, nazev, nazev_rew, popis, pro_adminy, hraci_pocet, hraci_hleda, aktivita, aktivitapj, zalozeno".($keywords ? ', keywords' : '').")
				VALUES ('$_SESSION[uid]', '$system', '$nazev', '$nazev_rew', '$popis', '$adminy', '$hraci', '$hleda', $time, $time, $time".($keywords ? ", '$keywords'" : "").")");
				$lastId = mysql_insert_id();
				if (strlen($orpT)>3) {
					mysql_query("INSERT INTO 3_herna_sets_open (cid,struktura) VALUES ($lastId, '$orpT')");
				}
				Header ("Location: $inc/herna/my/?ok=1");
				exit;
			}
		}
		else {
			mysql_query("INSERT INTO 3_herna_all ( uid, typ, nazev, nazev_rew, popis, pro_adminy, hraci_pocet, hraci_hleda, aktivita, aktivitapj, zalozeno".($keywords ? ', keywords' : '').")
			VALUES ('$_SESSION[uid]', '$system', '$nazev', '$nazev_rew', '$popis', '$adminy', '$hraci', '$hleda', $time, $time, $time".($keywords ? ", '$keywords'" : "").")");
			Header ("Location: $inc/herna/my/?ok=1");
			exit;
		}
	}
	else {
	  Header ("Location: $inc/herna/new/");
		exit;
	}
}
?>
