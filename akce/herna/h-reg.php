<?php

mb_internal_encoding("UTF-8");

$jmeno = $zivotopis = $atributy = $popis = $specials = "";
$error = $ok = 0;

if (!$LogedIn || $hFound !== true) {
	header ("Location: $inc/herna/");
	exit;
}
elseif ($hItem->schvaleno != '1') {
	header ("Location: $inc/herna/");
	exit;
}
elseif ($hItem->uid == $_SESSION['uid']) {
	header ("Location: $inc/herna/$slink/");
	exit;
}

if (!in_array($hItem->id, herna_omezeni(0,0)) && herna_omezeni($_SESSION['uid'],$_SESSION['lvl']) >= $herna_nebonus) {
	header ("Location: $inc/herna/$slink/reg/?error=1");
	exit;
}
elseif ($hItem->povolreg == '0') {
	header ("Location: $inc/herna/$slink/reg/?error=9");
	exit;
}
$mamPostavicku = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE uid = '$_SESSION[uid]' AND cid = '$hItem->id'"));

if ($mamPostavicku[0] > 0 || isset($uzivateleVeHre[$_SESSION['uid']])) {
	header ("Location: $inc/herna/$slink/reg/?error=8");
	exit;
}


if ($hItem->typ == 0) {			// Postava do DrD Systemu
	$promenne = array("jmeno", "rasa", "povolani", "presvedceni", "popis", "zivotopis");
	for ($i=0;$i<count($promenne);$i++) {
		if (!isSet($_POST[$promenne[$i]."_postavy"])) {
			header("Location: $inc/herna/$slink/reg/");
			exit;
		}
	}
	switch($_POST["rasa_postavy"]){
		case "0":	case "1":	case "2":	case "3":	case "4":	case "5":	case "6":
			$rasa = $_POST["rasa_postavy"];
		break;
		default:
			$error = 2;
	  break;
	}
	switch ($_POST["povolani_postavy"]){
		case "0":	case "1":	case "2":	case "3":	case "4":	case "5":	case "6": case "7":	case "8":	case "9":
			$povolani = $_POST["povolani_postavy"];
	 	break;
	  default:
	  	$error = 7;
	  break;
	}
	switch($_POST["presvedceni_postavy"]){
		case "0":	case "1":	case "2":	case "3":	case "4":
			$presvedceni = $_POST["presvedceni_postavy"];
	 	break;
	  default:
	  	$error = 6;
	  break;
	}
}
else {											// Postava do ORP Systemu

	$promenne = array("jmeno_postavy", "specials_postavy", "atributy_postavy", "zivotopis_postavy", "inventar_postavy");
	$orp = array();
	$settsS = mysql_query("SELECT cid,struktura FROM 3_herna_sets_open WHERE cid = '$hItem->id'");
	if (mysql_num_rows($settsS)>0) {
		$setts = mysql_fetch_row($settsS);
		$attrs = explode($hCh,$setts[1]);
		foreach($attrs as $k=>$attr) {
			$att = explode(">",$attr);
			$orp[$k] = array();
			$orp[$k]['nazev']=$att[0];
			$orp[$k]['typ'] = $att[1];
			$orp[$k]['edv'] = $att[2];
			if ($att[1] == "r") {
				$orp[$k]['min'] = $att[3];
				$orp[$k]['max'] = $att[4];
				continue;
			}
			elseif ($att[1] == "n") {
				$orp[$k]['min'] = $att[3];
				$orp[$k]['max'] = $att[4];
			}
			elseif ($att[1] == "s") {
			  $t = array();
			  for($i=3;$i<count($att);$i++){
			    $t[] = $att[$i];
				}
				$orp[$k]['add'] = $t;
				$orp[$k]['opt'] = join(">",$t);
				$orp[$k]['def'] = $att[3];
			}
			else { // t-ext/a-rea
			}
			$promenne[] = "attr-$k-postavy";
		}
	}
	for ($i=0;$i<count($promenne);$i++) {
		if(!isSet($_POST[$promenne[$i]])) {
			header ("Location: $inc/herna/$slink/reg/");
			exit;
		}
	}
}

$jmeno_rew = addslashes(do_seo(strip_tags($_POST['jmeno_postavy'])));
$jmeno = addslashes(trim(strip_tags($_POST['jmeno_postavy'])));
$zivotopis = addslashes(trim(strip_tags($_POST['zivotopis_postavy'])));
$popis = addslashes(trim(strip_tags($_POST['popis_postavy'])));
$inventar = addslashes(trim(strip_tags($_POST['inventar_postavy'])));

$herSrc = mysql_query ("SELECT count(*) FROM 3_herna_postava_$jTypString WHERE (jmeno = '$jmeno' OR jmeno_rew = '$jmeno_rew') AND cid = $hItem->id");
$hEx = mysql_fetch_row($herSrc);

if (name_is_bad(stripslashes($jmeno_rew)) || mb_strlen(stripslashes($jmeno)) <= 1 || mb_strlen($jmeno_rew) < 2){ // moc kratky nazev
  $error = 2;
}
elseif ($hEx[0] > 0) { // nazev jiz existuje
	$error = 3;
}
elseif (mb_strlen(stripslashes($jmeno)) > 40 || mb_strlen($jmeno_rew) > 40) {
	$error = 4;
}

if ($error>0){
  Header ("Location: $inc/herna/$slink/reg/?error=$error");
  exit;
}
else {
	if ($hItem->typ == '0') {

	/* ----------------------------------------
						POSTAVA do Draciho Doupete
	   ----------------------------------------
	*/

		include_once "./add/drd-fce.php";
		$p = zakladni_atributy($povolani,$rasa);
		$p['magy'] = get_magy($povolani,1,$p['obratnost'],$p['inteligence']);

	mysql_query("INSERT INTO 3_herna_postava_drd
		(uid, cid, jmeno, jmeno_rew, rasa, povolani, presvedceni, uroven, xp, sila, obratnost, odolnost, inteligence, charisma, 
		vyska, vaha, zivoty, zivoty_max, magy, magy_max, schopnosti, popis, zivotopis, inventar, aktivita) VALUES
		('$_SESSION[uid]','$hItem->id','$jmeno','$jmeno_rew','$rasa','$povolani','$presvedceni', '1', '0','$p[sila]','$p[obratnost]','$p[odolnost]','$p[inteligence]','$p[charisma]',
		$p[vyska],$p[vaha],$p[zivoty],$p[zivoty],$p[magy],$p[magy],'$p[schopnost]','$popis', '$zivotopis', '$inventar', '$time')");

		$texte ="Uživatel ".$_SESSION['login']." se hlásí do vaší jeskyně <a href='/herna/$hItem->nazev_rew/'>".$hItem->nazev."</a>.<br />
	Schválit nebo odmítnout postavu můžete v <a href='/herna/$hItem->nazev_rew/pj/#hraci'>rozhraní pro Pána jeskyně</a>.";
		sysPost($hItem->uid,$texte);

	  Header ("Location: $inc/herna/my/?ok=2");
	  exit;

	}
	else {

	/* ----------------------------------------
						POSTAVA do Open Role Play
	   ----------------------------------------
	*/

		$zivotopis = addslashes(trim(strip_tags($_POST['zivotopis_postavy'])));
		$popis = addslashes(trim(strip_tags($_POST['popis_postavy'])));
		$inventar = addslashes(trim(strip_tags($_POST['inventar_postavy'])));
		$atributy = addslashes(trim(strip_tags($_POST['atributy_postavy'])));
		$specials = addslashes(trim(strip_tags($_POST['specials_postavy'])));

		$setts = array();
		foreach($orp as $k=>$or){
		  $add = $value = "";
		  switch ($or['typ']) {
				case "r":
		    	$value = mt_rand($or['min'], $or['max']);
					$add = ">".$or['min'].">".$or['max'];
				break;
				case "s":
				  if (in_array($_POST["attr-$k-postavy"],$or['add'])) $value = _htmlspec($_POST["attr-$k-postavy"]);
				  else $value = $or['def'];
					$add = ">".$or['opt'];
				break;
				case "n":
				  if (intval($_POST["attr-$k-postavy"]) >= $or['min'] && intval($_POST["attr-$k-postavy"]) <= $or['max']) $value = intval($_POST["attr-$k-postavy"]);
					$add = ">".$or['min'].">".$or['max'];
				break;
				case "t":
				case "a":
				  $value = _htmlspec(str_replace($hCh,"-",trim($_POST["attr-$k-postavy"])));
				  $add = "";
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

		mysql_query("INSERT INTO 3_herna_postava_orp ( uid, cid, jmeno, jmeno_rew, popis, zivotopis, inventar, atributy, kouzla, by_pj, aktivita)
		VALUES ('$_SESSION[uid]', '$hItem->id', '$jmeno', '$jmeno_rew', '$popis', '$zivotopis', '$inventar', '$atributy', '$specials', '$setts', '$time')");

		$texte ="Uživatel ".$_SESSION['login']." se hlásí do vaší jeskyně <a href='/herna/$hItem->nazev_rew/'>".$hItem->nazev."</a>.<br />
	Schválit nebo odmítnout postavu můžete v <a href='/herna/$hItem->nazev_rew/pj/#hraci'>rozhraní pro Pána jeskyně</a>.";
		sysPost($hItem->uid,$texte);

		Header ("Location: $inc/herna/my/?ok=2");
		exit;
	}
}
?>
