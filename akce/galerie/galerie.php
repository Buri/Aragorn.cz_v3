<?php
//nahrani img na server do galerie

$width = $height = 140;
$nazev = "";
$popis = "";
$error = $ok = 0;

if (!$LogedIn || !isset($_FILES["sendfile"]) || !isset($_POST['nazev']) || !isset($_POST['popis'])) {
  Header ("Location: $inc/galerie/new");
  exit;
}

mb_internal_encoding("utf-8");

$nazev_rew = do_seo(trim($_POST['nazev']));
$nazev = trim($_POST['nazev']);
$nazev = addslashes(mb_strtoupper(mb_substr($nazev, 0, 1)).mb_substr($nazev, 1));
$popis = addslashes(trim($_POST["popis"]));

$filetype = mb_strtolower(ereg_replace("^.+\.(.+)$","\\1",$_FILES["sendfile"]["name"]));
$img_src = $nazev_rew.".".$filetype;

$galSrc = mysql_query ("SELECT count(*) FROM 3_galerie WHERE nazev = '$nazev' OR nazev_rew = '$nazev_rew' OR source = '$img_src' OR thumb = '$img_src'");
$gEx = mysql_fetch_row($galSrc);

$countSent = array_pop(mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM 3_galerie WHERE autor = $_SESSION[uid] AND schvaleno = 0")));

if ($gEx[0]>0) {
	$error = 1;
	Header ("Location: $inc/galerie/new/?error=$error");
	exit;
}
elseif ($countSent > 1) {
	$error = 10;
	Header ("Location: $inc/galerie/new/?error=$error");
	exit;
}

$cesta = "./galerie/$img_src";
move_uploaded_file ($_FILES["sendfile"]["tmp_name"], $cesta);
$size = getimagesize($cesta);
$width_orig = $size[0];
$height_orig = $size[1];

if ($gEx[0]>0) {
  $error = 1;
} elseif ((strlen($nazev_rew) < 3)){
  $error = 2;
} elseif (($size[2] != 1) && ($size[2] != 2) && ($size[2] != 3)){
  $error = 3;
} elseif ($_FILES["sendfile"]["size"] > (1024*500)){
  $error = 4;
} elseif (($width_orig < 150) || ($height_orig < 150) || ($width_orig > 1600) || ($height_orig > 1600)) {
  $error = 5;
} else{
  $error = 0;
}

if ($error>0){
  @unlink($cesta);
  Header ("Location: $inc/galerie/new/?error=$error");
}

else {
	if ($width && ($width_orig < $height_orig)) {
		$width = ($height / $height_orig) * $width_orig;
	} else {
		$height = ($width / $width_orig) * $height_orig;
	}
	
	if ($size[2] == 2) {
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($cesta);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		$thumbnail = $thumb_prefix.$nazev_rew.".jpg";
		imagejpeg($image_p, "./galerie/".$thumbnail, 80);
	}
	elseif ($size[2] == 3) {
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefrompng($cesta);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		$thumbnail = $thumb_prefix.$nazev_rew.".jpg";
		imagejpeg($image_p, "./galerie/".$thumbnail, 80);
	}
	else {
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromgif($cesta);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		$thumbnail = $thumb_prefix.$nazev_rew.".jpg";
		imagejpeg($image_p, "./galerie/".$thumbnail, 80);
	}
mysql_query("INSERT INTO 3_galerie (autor, nazev, nazev_rew, source, thumb, x, y, popis, schvalenotime, odeslanotime) 
		VALUES 
	('$_SESSION[uid]','$nazev','$nazev_rew', '$img_src', '$thumbnail', $size[0], $size[1], '$popis', $time, $time)");
  Header ("Location: $inc/galerie/new/?ok=1");
}
exit;
?>
