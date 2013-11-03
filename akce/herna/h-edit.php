<?php

mb_internal_encoding("UTF-8");

$error = $ok = 0;
$chngJmeno = false;

if (!$LogedIn || !$hFound) {
	header ("Location: $inc/herna/");
	exit;
}
if ($pFound != true) {
	header ("Location: $inc/herna/$slink/");
	exit;
}
if ($postava->uid != $_SESSION['uid'] && $hItem->uid != $_SESSION['uid'] && !$allowsPJ['postavy']) {
	header ("Location: $inc/herna/$slink/$sslink/");
	exit;
}

if ($postava->uid == $_SESSION['uid']) {
	if ($jTypString == "drd") {	// ======================== Vlastnik DrD postavy edituje

		$promenne = array("jmeno", "presvedceni", "zivotopis", "popis", "poznamky_hrac");
		for ($i = 0; $i<count($promenne);$i++) {
			if (!isSet($_POST[$promenne[$i]."_edit"])) {
				header ("Location: $inc/herna/$slink/$sslink/");
				exit;
			}
		}
		switch ($_POST["presvedceni_edit"]) {
			case "0":	case "1":	case "2":	case "3":	case "4":
				$presvedceni = $_POST["presvedceni_edit"];
		 	break;
		  default:
		  	$error = 6;
				$presvedceni = $postava->presvedceni;
		  break;
		}
		$zivotopis = addslashes($_POST['zivotopis_edit']);
		$popis     = addslashes($_POST['popis_edit']);
		$jmeno     = addslashes(strip_tags($_POST['jmeno_edit']));
		$jmeno_rew = do_seo(strip_tags($_POST['jmeno_edit']));
		$poznamky_hrac = addslashes($_POST['poznamky_hrac_edit']);

		if (stripslashes($jmeno) == $postava->jmeno) {
			if ($error == 0) {
				mysql_query("UPDATE 3_herna_postava_drd SET popis = '$popis', presvedceni = '$presvedceni', zivotopis = '$zivotopis' WHERE id = '$postava->id' AND cid = '$hItem->id'");
				
        $postavaPE = mysql_query("SELECT id FROM 3_herna_poznamky WHERE id_postava = '$postava->id'");
        if (mysql_num_rows($postavaPE)>0) {
          mysql_query("UPDATE 3_herna_poznamky SET poznamka = '$poznamky_hrac' WHERE id_postava = '$postava->id'");    	
			  } else{
          mysql_query("INSERT INTO 3_herna_poznamky (id, id_postava, poznamka) VALUES (0, $postava->id, '$poznamky_hrac')");
        }
        
        
        
			}	else {
				mysql_query("UPDATE 3_herna_postava_drd SET popis = '$popis', zivotopis = '$zivotopis' WHERE id = '$postava->id' AND cid = '$hItem->id'");
			}
		}
		elseif ($error == 0) {
			if ($jmeno_rew == $postava->jmeno_rew && mb_strlen($jmeno_rew)>=2 && mb_strlen(stripslashes($jmeno))>=2 && !name_is_bad($jmeno_rew)) {
				mysql_query("UPDATE 3_herna_postava_drd SET popis = '$popis', jmeno = '$jmeno', presvedceni = '$presvedceni', zivotopis = '$zivotopis' WHERE id = '$postava->id' AND cid = '$hItem->id'");
			}
			elseif (mb_strlen($jmeno_rew)>=2 && !name_is_bad($jmeno_rew)) {
				$uzJe = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_drd WHERE jmeno_rew = '$jmeno_rew' AND cid = '$postava->cid' AND id != '$postava->id'"));
				if ($uzJe[0]>0) {
					$error = 1;
				}
				else {
					$chngJmeno = true;
					mysql_query("UPDATE 3_herna_postava_drd SET jmeno = '$jmeno', jmeno_rew = '$jmeno_rew' WHERE id = '$postava->id' AND cid = '$hItem->id'");
				}
				mysql_query("UPDATE 3_herna_postava_drd SET popis='$popis', presvedceni='$presvedceni', zivotopis='$zivotopis' WHERE id = '$postava->id' AND cid = '$hItem->id'");
			}
			else {
				$error=2;
			}
		}
		$doIco = false;
		if (is_uploaded_file($_FILES["ico"]["tmp_name"]) && mb_strlen($_FILES["ico"]["name"])>4 && $error == 0) {
		//nahrani ikonky na server
			$type = mb_strtolower(ereg_replace("^.+\.(.+)$","\\1",$_FILES["ico"]["name"]));
			if ($type != "jpg" && $type != "gif" && $type != "png") {
				$error = 3;
				$type = "tmp";
			}
			$ico_n = "c_".$_SESSION['uid']."_".$postava->cid."_".Rand(1,9).Rand(1,9).Rand(1,9).".".$type;
			$cesta = "./system/icos/$ico_n";
			move_uploaded_file($_FILES["ico"]["tmp_name"], $cesta);
			$size = getimagesize($cesta);
			$width = $size[0];
			$height = $size[1];
			if ((mb_strlen($_FILES["ico"]["name"]) < 5) || $error==3){
				$error = 3;
			} elseif (($size[2] != 1) && ($size[2] != 2) && ($size[2] != 3)){
				$error = 3;
			} elseif ($_FILES["ico"]["size"] > 16384){
				$error = 4;
			} elseif (($width > 50) || ($width < 40) || ($height < 50) || ($height > 70)) {
				$error = 5;
			} else {
				$doIco = true;
			}
			$uIco = mysql_fetch_object( mysql_query("SELECT ico FROM 3_herna_postava_$jTypString WHERE uid = '$_SESSION[uid]' AND id = '$postava->id'"));
			//neni-li ikona defaultni, smaze se stara
			if ($uIco->ico != "" && $doIco==true && $uIco->ico != "default.jpg"){
				@unlink("./system/icos/$uIco->ico");
			}
			if ($doIco==true) {
			  mysql_query ("UPDATE 3_herna_postava_$jTypString SET ico = '$ico_n' WHERE id = '$postava->id' AND uid = '$_SESSION[uid]'");
			}
		}
		//redirect pri chybe / uspesny redirect
		if ($chngJmeno == true) {
			$sslink = $jmeno_rew;
		}
		if ($error > 0){
			//smazani
			@unlink($cesta);
			Header ("Location:$inc/herna/$slink/$sslink/?error=$error");
			exit;
		}else{
		 	Header ("Location:$inc/herna/$slink/$sslink/?ok=1");
			exit;
		}
	}
	else {									// ======================== Vlastnik ORP postavy edituje

		$promenne = array("jmeno_edit", "specials_edit", "atributy_edit", "popis_edit", "zivotopis_edit", "inventar_edit", "poznamky_hrac_edit");
		$promenne2 = $typ2 = $nazvy = array();
	  $orp = array();
		if (mb_strlen($postava->by_pj)>4) {
			$attrs = explode($hCh,$postava->by_pj);
			foreach($attrs as $k=>$attr) {
				$att = explode(">",$attr);
				$orp[$k] = array();
				$orp[$k]['value']=$att[0];
				$orp[$k]['nazev']=$att[1];
				$orp[$k]['typ'] = $att[2];
				$orp[$k]['edv'] = $att[3];
				$orp[$k]['add'] = "";
				if ($att[2] == "r") {
					$orp[$k]['add'] = ">".$att[4].">".$att[5];
					$orp[$k]['min'] = $att[4];
					$orp[$k]['max'] = $att[5];
					continue;
				}
				elseif ($att[2] == "n") {
					$orp[$k]['add'] = ">".$att[4].">".$att[5];
					$orp[$k]['min'] = $att[4];
					$orp[$k]['max'] = $att[5];
				}
				elseif ($att[2] == "s") {
				  $t = array();
				  for($i=4;$i<count($att);$i++){
				    $t[] = $att[$i];
					}
					$orp[$k]['add'] = $t;
					$orp[$k]['opt'] = join(">",$t);
					$orp[$k]['def'] = $t[0];
				}
				else { // t-ext/a-rea
				}
				if ($att[3] == "v" || $att[3] == "n") {
				  continue;
				}
				$promenne[] = "orp-attr-$k";
			}
		}
		for ($i = 0; $i<count($promenne);$i++) {
			if (!isSet($_POST[$promenne[$i]])) {
				header ("Location: $inc/herna/$slink/$sslink/");
				exit;
			}
		}
		$zivotopis = addslashes($_POST['zivotopis_edit']);
		$popis = addslashes($_POST['popis_edit']);
		$atributy = addslashes($_POST['atributy_edit']);
		$specials = addslashes($_POST['specials_edit']);
		$inventar = addslashes($_POST['inventar_edit']);
		$jmeno = addslashes(trim(strip_tags($_POST['jmeno_edit'])));
		$jmeno_rew = do_seo(strip_tags(trim($_POST['jmeno_edit'])));
		$poznamky_hrac = addslashes($_POST['poznamky_hrac_edit']);

		if (stripslashes($jmeno) == $postava->jmeno && $jmeno_rew == $postava->jmeno_rew) {
		}
		elseif ($error == 0) {
			if ($jmeno_rew == $postava->jmeno_rew && mb_strlen($jmeno_rew)>=2 && mb_strlen(stripslashes($jmeno))>=2 && !name_is_bad($jmeno_rew)) {
				mysql_query("UPDATE 3_herna_postava_orp SET jmeno = '$jmeno' WHERE id = '$postava->id' AND cid = '$hItem->id'");
			}
			elseif (mb_strlen($jmeno_rew)>=2 && !name_is_bad($jmeno_rew)) {
				$uzJe = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_orp WHERE jmeno_rew = '$jmeno_rew' AND cid = '$postava->cid' AND id != '$postava->id'"));
				if ($uzJe[0]>0) {
					$error = 1;
				}
				else {
					$chngJmeno = true;
					mysql_query("UPDATE 3_herna_postava_orp SET jmeno = '$jmeno', jmeno_rew = '$jmeno_rew' WHERE id = '$postava->id' AND cid = '$hItem->id'");
				}
			}
			else {
				$error=2;
			}
		}

		$setts = array();
		foreach($orp as $k=>$or){
			$value = "";
			$add = $or['add'];
			switch ($or['typ']) {
				case "r":
				  if ($or['edv'] == "n" || $or['edv'] == "v") {
				    $add = $or['add'];
					}
					else {
					}
				  $add = $or['add'];
					$value = $or['value'];
				break;
				case "s":
				  if ($or['edv'] == "n" || $or['edv'] == "v") {
				    $value = $or['value'];
					}
					else {
						if (in_array(_htmlspec($_POST["orp-attr-$k"]),$or['add'])) $value = _htmlspec($_POST["orp-attr-$k"]);
						else $value = $or['def'];
					}
					$add = ">".$or['opt'];
				break;
				case "n":
				  $add = $or['add'];
				  if ($or['edv'] == "n" || $or['edv'] == "v") {
				    $value = $or['value'];
					}
					else {
						if (intval($_POST["orp-attr-$k"]) >= $or['min'] && intval($_POST["orp-attr-$k"]) <= $or['max']) $value = intval($_POST["orp-attr-$k"]);
						else $value = floor(($or['min']+$or['max'])/2);
					}
				break;
				case "t":
				case "a":

				  if ($or['edv'] == "n" || $or['edv'] == "v") {
				    $value = $or['value'];
					}
					else {
						$value = _htmlspec(str_replace($hCh,"-",trim($_POST["orp-attr-$k"])));
					}
				break;
			}
			if ($or['edv'] == "n" || $or['edv'] == "v") {
				$value = $or['value'];
			}
			if (mb_strlen(trim($value))<1) {
				$value = "nevyplněno";
			}
			$setts[] = $value.">".$or['nazev'].">".$or['typ'].">".$or['edv'].$add;
		}
		if (count($setts)>0) {
			$setts = addslashes(join($hCh,$setts));
		} else {
			$setts = "";
		}

		if ($error < 1) {
			mysql_query("UPDATE 3_herna_postava_orp SET popis = '$popis', inventar = '$inventar', zivotopis = '$zivotopis', atributy = '$atributy', kouzla = '$specials', by_pj = '$setts' WHERE id = $postava->id AND cid = $postava->cid");
			
      $postavaPE = mysql_query("SELECT id FROM 3_herna_poznamky WHERE id_postava = '$postava->id'");
      if (mysql_num_rows($postavaPE)>0) {
        mysql_query("UPDATE 3_herna_poznamky SET poznamka = '$poznamky_hrac' WHERE id_postava = '$postava->id'");    	
		  } else{
        mysql_query("INSERT INTO 3_herna_poznamky (id, id_postava, poznamka) VALUES (0, $postava->id, '$poznamky_hrac')");
      }
		}

		$doIco = false;
		if (is_uploaded_file($_FILES['ico']['tmp_name']) && mb_strlen($_FILES["ico"]["name"])>4 && $error == 0) {
		//nahrani ikonky na server
			$type = mb_strtolower(ereg_replace("^.+\.(.+)$","\\1",$_FILES["ico"]["name"]));
			if ($type != "jpg" && $type != "gif" && $type != "png") {
				$error = 3;
				$type = "tmp";
			}
			$ico_n = "c_".$_SESSION['uid']."_".$postava->cid."_".Rand(1,9).Rand(1,9).Rand(1,9).".".$type;
			$cesta = "./system/icos/$ico_n";
			move_uploaded_file ($_FILES["ico"]["tmp_name"], $cesta);
			$size = getimagesize($cesta);
			$width = $size[0];
			$height = $size[1];
			if ((mb_strlen($_FILES["ico"]["tmp_name"]) < 5) || ($error == 3)){
				$error = 3;
			} elseif (($size[2] != 1) && ($size[2] != 2) && ($size[2] != 3)){
				$error = 3;
			} elseif ($_FILES["ico"]["size"] > 16384){
				$error = 4;
			} elseif (($width > 50) || ($width < 40) || ($height < 50) || ($height > 70)) {
				$error = 5;
			} else {
				$doIco = true;
			}
			$uIco = mysql_fetch_object( mysql_query("SELECT ico FROM 3_herna_postava_$jTypString WHERE uid = '$_SESSION[uid]' AND id = '$postava->id'"));
			//neni-li ikona defaultni, smaze se stara
			if ($uIco->ico != "" && $doIco==true && $uIco->ico != "default.jpg"){
				@unlink("./system/icos/$uIco->ico");
			}
			if ($doIco==true) {
			  mysql_query ("UPDATE 3_herna_postava_$jTypString SET ico = '$ico_n' WHERE id = '$postava->id' AND uid = '$_SESSION[uid]'");
			}
		}
		//redirect pri chybe / uspesny redirect
		if ($chngJmeno == true) {
			$sslink = $jmeno_rew;
		}
		if ($error > 0){
			//smazani
			@unlink($cesta);
			Header ("Location:$inc/herna/$slink/$sslink/?error=$error");
			exit;
		}
		else{
			Header ("Location:$inc/herna/$slink/$sslink/?ok=1");
			exit;
		}
	}
}
elseif ($hItem->uid == $_SESSION['uid'] || $allowsPJ['postavy']) {
	if ($hItem->typ == '0') { // ======================== PJ DrD edituje postavu

		$promenne = array("popis_edit", "schopnosti_edit", "vaha_edit", "vyska_edit", "zkusenosti_edit", "sila_edit", "obratnost_edit", "odolnost_edit", "inteligence_edit", "charisma_edit", "zivoty_edit", "zivoty_max_edit", "zl_edit", "st_edit", "md_edit");
		$attrSet = array("sila", "obratnost", "odolnost", "inteligence", "charisma");
		$attrCisla = array("zivoty_edit", "zivoty_max_edit", "vyska_edit", "vaha_edit", "zkusenosti_edit");
		if (floor($postava->povolani/2) > 0 && floor($postava->povolani/2) < 4) {
			$promenne[] = "magy_edit";
			$attrCisla[] = "magy_edit";
			$promenne[] = "magy_max_edit";
			$attrCisla[] = "magy_max_edit";
			$magy = 0;
		}
		for ($i = 0; $i<count($promenne);$i++) {
			if (!isSet($_POST[$promenne[$i]])) {
				header ("Location: $inc/herna/$slink/$sslink/");
				exit;
			}
		}
		for ($i=0;$i<count($attrSet);$i++) {
			if (ctype_digit($_POST[$attrSet[$i]."_edit"]) && $error == 0) {
				if ($_POST[$attrSet[$i]."_edit"] > 22 || $_POST[$attrSet[$i]."_edit"] < 1) {
					$error = 7;
					break;
				}
			}
			else {
				$error = 7;
			}
		}
		if ($error == 0) {
			for ($i=0;$i<count($attrCisla);$i++) {
				if (ctype_digit($_POST[$attrCisla[$i]]) && $error == 0) {
					if ($_POST[$attrCisla[$i]] < 0) {
						$error = 8;
						break;
					}
				}
				else {
					$error = 8;
				}
			}
		}
		if ($error == 0) {
			if ($_POST['zivoty_edit'] > $_POST['zivoty_max_edit']) $zivoty = $_POST['zivoty_max_edit'];
			else $zivoty = $_POST['zivoty_edit'];

			$zkusenosti = $_POST['zkusenosti_edit'];
			$sila = $_POST['sila_edit']; $odolnost = $_POST['odolnost_edit']; $obratnost = $_POST['obratnost_edit'];
			$charisma = $_POST['charisma_edit']; $inteligence = $_POST['inteligence_edit'];  
			$zivoty_max = $_POST['zivoty_max_edit']; $vyska = $_POST['vyska_edit']; $vaha = $_POST['vaha_edit'];
			$schopnosti = addslashes($_POST['schopnosti_edit']);
			$popis = addslashes($_POST['popis_edit']);

			$magy_max = $_POST['magy_max_edit'];

			if ($magy_max > $_POST['magy_edit']) $magy = $_POST['magy_edit'];
			else $magy = $magy_max;

			$zl = abs(intval($_POST['zl_edit']));
			$st = abs(round(intval($_POST['st_edit'])/10,1));
			$md = abs(round(intval($_POST['md_edit'])/100,2));
			$penize = strval(0+$zl+$st+$md);

			mysql_query("UPDATE 3_herna_postava_drd SET popis = '$popis', sila = '$sila', obratnost = '$obratnost', odolnost = '$odolnost',
			charisma = '$charisma', penize = '$penize', inteligence = '$inteligence', schopnosti = '$schopnosti', zivoty = '$zivoty',
			zivoty_max = '$zivoty_max', magy = '$magy', magy_max = '$magy_max', xp = '$zkusenosti', vyska = '$vyska', vaha = '$vaha' WHERE id = '$postava->id' AND cid = '$hItem->id'");
			header("Location: $inc/herna/$slink/$sslink/?ok=1");
			exit;
		}
		else {
			header("Location: $inc/herna/$slink/$sslink/?error=$error");
			exit;
		}
	}
	else {									// ======================== PJ edituje ORP postavu

		$promenne = array("specials_edit", "atributy_edit", "popis_edit", "inventar_edit");
	  $orp = array();
		if (mb_strlen($postava->by_pj)>1) {
			$attrs = explode($hCh,$postava->by_pj);
			foreach($attrs as $k=>$attr) {
				$att = explode(">",$attr);
				$orp[$k] = array();
				$orp[$k]['value']=$att[0];
				$orp[$k]['nazev']=$att[1];
				$orp[$k]['typ'] = $att[2];
				$orp[$k]['edv'] = $att[3];
				$orp[$k]['add'] = "";
				if ($att[2] == "r") {
					$orp[$k]['add'] = ">".$att[4].">".$att[5];
					$orp[$k]['min'] = $att[4];
					$orp[$k]['max'] = $att[5];
				}
				elseif ($att[2] == "n") {
					$orp[$k]['add'] = ">".$att[4].">".$att[5];
					$orp[$k]['min'] = $att[4];
					$orp[$k]['max'] = $att[5];
				}
				elseif ($att[2] == "s") {
				  $t = array();
				  for($ii=4;$ii<count($att);$ii++){
				    $t[] = $att[$ii];
					}
					$orp[$k]['add'] = $t;
					$orp[$k]['def'] = $t[0];
					$orp[$k]['opt'] = join(">",$t);
				}
				else { // t-ext/a-rea
				}
				$promenne[] = "orp-attr-$k";
			}
		}
		for ($i = 0; $i<count($promenne);$i++) {
			if (!isSet($_POST[$promenne[$i]])) {
				header ("Location: $inc/herna/$slink/$sslink/");
				exit;
			}
		}

		$popis = addslashes($_POST['popis_edit']);
		$atributy = addslashes($_POST['atributy_edit']);
		$specials = addslashes($_POST['specials_edit']);
		$inventar = addslashes($_POST['inventar_edit']);

		$setts = array();
		foreach($orp as $k=>$or){
		  $value = "";
		  $add = $or['add'];
			switch ($or['typ']) {
				case "n":
				case "r":
					if (intval($_POST["orp-attr-$k"]) >= $or['min'] && intval($_POST["orp-attr-$k"]) <= $or['max']) $value = intval($_POST["orp-attr-$k"]);
					else $value = floor(($or['min']+$or['max'])/2);
				break;
				case "s":
					if (in_array(_htmlspec($_POST["orp-attr-$k"]),$or['add'])) $value = _htmlspec($_POST["orp-attr-$k"]);
					else $value = $or['def'];
					$add = ">".$or['opt'];
				break;
				case "t":
				case "a":
					$value = _htmlspec(str_replace($hCh,"-",trim($_POST["orp-attr-$k"])));
				break;
			}
			if (mb_strlen(trim($value))<1) {
				$value = "nevyplněno";
			}
			$setts[] = $value.">".$or['nazev'].">".$or['typ'].">".$or['edv'].$add;
		}
		if (count($setts)>0) {
			$setts = addslashes(join($hCh,$setts));
		} else {
			$setts = "";
		}

		mysql_query("UPDATE 3_herna_postava_orp SET popis = '$popis', atributy = '$atributy', kouzla = '$specials', by_pj = '$setts', inventar = '$inventar' WHERE id = $postava->id");
	  Header ("Location: $inc/herna/$slink/$sslink/?ok=1");
	  exit;
	}
}
else {
  Header ("Location: $inc/herna/");
  exit;
}
?>
