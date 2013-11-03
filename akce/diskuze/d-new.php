<?php

$nazev = "";
$popis = "";
$nastenka = "";
$error = $ok = 0;

if (!$LogedIn || !isset($_POST['nazev']) || !isset($_POST['popis']) || !isset($_POST['oblast'])) {
  Header ("Location: $inc/galerie/new/");
  exit;
}

$nazev_rew = do_seo(trim($_POST['nazev']));
$nazev = addslashes(trim($_POST['nazev']));
$popis = addslashes(trim($_POST['popis']));
$okruh= addslashes($_POST['oblast']);

$haveSrc = mysql_query ("SELECT count(*) FROM 3_diskuze_topics WHERE owner = '$_SESSION[uid]' AND schvaleno = '0'");
$hEx = mysql_fetch_row($haveSrc);
$disSrc = mysql_query ("SELECT count(*) FROM 3_diskuze_topics WHERE nazev = '$nazev' OR nazev_rew = '$nazev_rew'");
$dEx = mysql_fetch_row($disSrc);
$okruhSrc = mysql_query ("SELECT count(*) FROM 3_diskuze_groups WHERE id = '$okruh'");
$oEx = mysql_fetch_row($okruhSrc);

if ($hEx[0] > 1 && $_SESSION['level'] < 2){
	$error = 5;
}elseif (strlen($nazev) <= 3){ // moc kratky nazev
  $error = 1;
}
elseif ($dEx[0] > 0) { // nazev jiz existuje
	$error = 2;
}
elseif ($oEx[0] != "1") { // spatna oblast
  $error = 3;
}
elseif(strlen($nazev) > 40 || strlen($nazev_rew) > 40) {
	$error = 4;
}
else{
  $error = 0;
}

if ($error>0){
  Header ("Location: $inc/diskuze/new/?error=$error");
}

else {
	mysql_query("INSERT INTO 3_diskuze_topics (okruh, nazev, nazev_rew, owner, popis, schvalenotime) VALUES ('$okruh', '$nazev', '$nazev_rew', '$_SESSION[uid]', '$popis', $time)");
  Header ("Location: $inc/diskuze/new/?ok=1");
}
exit;
?>
