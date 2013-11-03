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

if (($hItem->uid == $_SESSION['uid'] || ($hItem->PJs && $allowsPJ['nastenka'])) && isSet($_POST['popis_edit']) && isSet($_POST['adminy_edit'])
	&& isSet($_POST['keywords_edit']) && isSet($_POST['hraci_edit']) && isSet($_POST['nastenka_edit']) && isSet($_POST['hleda_edit']) && isSet($_POST['notes_edit'])) {

	$keywords = addslashes(mb_strimwidth($_POST['keywords_edit'], 0, 255, "..."));
	$popis = addslashes(mb_strimwidth($_POST['popis_edit'], 0, 1024, "..."));
	$hleda = addslashes(mb_strimwidth($_POST['hleda_edit'], 0, 1024, "..."));
	$adminy = addslashes(mb_strimwidth($_POST['adminy_edit'], 0, 1024, "..."));
	$notes = addslashes(mb_strimwidth($_POST['notes_edit'], 0, 30000, "..."));
	$nastenka = addslashes(strtr(mb_strimwidth($_POST['nastenka_edit'], 0, 50000, "..."),$changeToXHTML));
	$hraci = $_POST['hraci_edit'];

	if (isSet($_POST['povol_prihlasky']) && $hItem->schvaleno == '1') {
		if ($_POST['povol_prihlasky'] == "ano") {
			$povol_reg = "1";
		}
		elseif ($_POST['povol_prihlasky'] == "ne") {
			$povol_reg = "0";
		}
		else {
			$povol_reg = $hItem->povolreg;
		}
	}
	else {
		$povol_reg = $hItem->povolreg;
	}
	if (ctype_digit($hraci)) {
		if($hraci<16 && $hraci>0) {
			$hraci = $hraci;
		}else {
			$hraci = $hItem->hraci_pocet;
		}
	}else {
		$hraci = $hItem->hraci_pocet;
	}
	if (mb_strlen($popis) < 1) {
		$popis = "";
	}
	if (mb_strlen($hleda) < 1) {
		$hleda = "";
	}
	if (mb_strlen($nastenka) < 1) {
		$nastenka = "";
	}
	if (mb_strlen($adminy) < 1) {
		$adminy = "";
	}
	if (mb_strlen($notes) < 1) {
		$notes = "";
	}
	if (mb_strlen($keywords) < 1) {
		$keywords = "";
	}

	$ok = 1;
	$finalEdit = "";
	if (isset($_POST['final_edit'])) {
		if ($_POST['final_edit'] == "on") {
			$finalEdit = " schvaleno = '0', ";
			$ok = 2;
		}
	}

	mysql_query("UPDATE 3_herna_all SET $finalEdit keywords = '$keywords', poznamky = '$notes', nastenka = '$nastenka', povolreg = '$povol_reg', popis = '$popis', pro_adminy = '$adminy', hraci_hleda = '$hleda', hraci_pocet = '$hraci' WHERE id = '$hItem->id' AND uid = '$hItem->uid'");
	header ("Location: $inc/herna/$slink/pj/?ok=$ok");
	exit;
}
else {
	header ("Location: $inc/herna/$slink/$sslink/");
	exit;
}

?>
