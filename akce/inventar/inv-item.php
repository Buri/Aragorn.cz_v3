<?php

mb_internal_encoding("UTF-8");

if (!$LogedIn || !$hFound || $sslink != "shop") {
	header ("Location: $inc/herna/");
	exit;
}

$okPostava = false;
$error = 0;
$ok = 0;

for ($a=0;$a<count($jeskyneHraci);$a++) {
	if ($jeskyneHraci[$a]['postava_rew'] == $_GET['p'] && $jeskyneHraci[$a]['objekt']->schvaleno == "1") {
		$postava = $jeskyneHraci[$a]['objekt'];
		$okPostava = true;
		break;
	}
}

if ($okPostava && $hItem->uid == $_SESSION['uid']) {

	$postava->inventar = stripslashes($postava->inventar);

	include_once("./add/xml_parser_func.php");
	if (mb_strlen($postava->inventar)>5) {
		$inventory = inventar_read($postava->inventar);
	}
	else {
		$inventory = inventar_make();
	}

	switch ($_GET['do']) {
		case "add":
			if (isset($_POST['item'],$_POST['item_pocet']) && ctype_digit($_POST['item']) && $_POST['item']!="" && ctype_digit($_POST['item_pocet']) && $_POST['item_pocet']!="" && $_POST['item_pocet']>0) { 	// standartni vec - z DB
				$item = $_POST['item']; $pocet = $_POST['item_pocet'];
				$isItem = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_herna_items WHERE id = '$item'"));
				if ($isItem[0] < 1) {
					Header("Location: $inc/herna/$slink/shop/?do=add&p=$postava->jmeno_rew");
					exit;
				}
				else {
					$item2add = array("id"=>$item,"pocet"=>$pocet);
					$inventory = inventar_uprava($inventory,"add",$item2add);
					if ($inventory[1] == true) {
						$inventory[0] = addslashes($inventory[0]);
						mysql_query("UPDATE 3_herna_postava_drd SET inventar = '$inventory[0]' WHERE id = $postava->id AND cid = $postava->cid");
						Header("Location: $inc/herna/$slink/shop/?do=add&p=$postava->jmeno_rew&ok=1");
						exit;
					}
				}
			}
			elseif (isset($_POST['item_nazev'],$_POST['item_popis'],$_POST['item_pocet'],$_POST['item_typ']) && ctype_digit($_POST['item_pocet']) && $_POST['item_pocet']!="" && $_POST['item_pocet']>0 && mb_strlen($_POST['item_nazev'])>0 && mb_strlen($_POST['item_popis'])>0) {		// vlastni item, neni z DB
				switch ($_POST['item_typ']) {
					case "s":
						$typ = "s";
						$attrCisla = array("item_sz","item_oz","item_utoc","item_hands");
					break;
					case "w":
						$typ = "w";
						$attrCisla = array("item_sz","item_oz","item_utoc","item_hands");
					break;
					case "z":
						$typ = "z";
						$attrCisla = array("item_kz");
					break;
					case "i":
						$typ = "i";
						$attrCisla = array();
					break;
					default:
						Header("Location: $inc/herna/$slink/shop/?do=add&p=$postava->jmeno_rew");
						exit;
					break;
				}
				for ($i=0;$i<count($attrCisla);$i++) {
					if (is_numeric($_POST[$attrCisla[$i]]) && $error==0) {
						if ($_POST[$attrCisla[$i]] > 100 || $_POST[$attrCisla[$i]] < -100 ) {
							$error = 8;
							break;
						}
					}
					else {
						$error = 8;
						break;
					}
				}
				$pocet = $_POST['item_pocet'];
				if ($pocet > 100000) {
					$pocet = 100000;
				}
				$hands = $_POST['item_hands'];
				if ($hands > 2 || $hands <= 0) {
					$hands = 1;
				}
				if ($error == 0) {
					if ($typ == "w" || $typ == "s") {
						$item2add = array("pocet"=>$pocet,"sila"=>$_POST['item_sz'],"hands"=>$hands,"obrana"=>$_POST['item_oz'],"typ"=>$_POST['item_typ'],"oprava"=>$_POST['item_utoc'],"name"=>_htmlspec(mb_strimwidth($_POST['item_nazev'],0,40,"...")),"popis"=>_htmlspec(mb_strimwidth($_POST['item_popis'],0,250,"...")));
					} elseif ($typ == "z") {
						$item2add = array("pocet"=>$pocet,"sila"=>$_POST['item_kz'],"hands"=>$hands,"obrana"=>$_POST['item_kz'],"typ"=>"z","oprava"=>'0',"name"=>_htmlspec(mb_strimwidth($_POST['item_nazev'],0,40,"...")),"popis"=>_htmlspec(mb_strimwidth($_POST['item_popis'],0,250,"...")));
					} elseif ($typ == "i") {
						$item2add = array("pocet"=>$pocet,"sila"=>0,"obrana"=>0,"oprava"=>0,"hands"=>0,"typ"=>"i","name"=>_htmlspec(mb_strimwidth($_POST['item_nazev'],0,40,"...")),"popis"=>_htmlspec(mb_strimwidth($_POST['item_popis'],0,250,"...")));
					}
					else {
						$error++;
					}
					if ($error == 0) {
						$inventory = inventar_uprava($inventory,"add",$item2add);
						if ($inventory[1] == true) {
							$inventory[0] = addslashes($inventory[0]);
							mysql_query("UPDATE 3_herna_postava_drd SET inventar = '$inventory[0]' WHERE id = $postava->id AND cid = $postava->cid");
							Header("Location: $inc/herna/$slink/shop/?do=add&p=$postava->jmeno_rew&ok=1");
							exit;
						}
					}
				}
			}
			else {
				Header("Location: $inc/herna/$slink/shop/?do=add&p=$postava->jmeno_rew");
				exit;
			}

		break;
		case "edit":
			if (isset($_GET['v']) && ctype_digit($_GET['v']) && $_GET['v']!="" && ctype_digit($_POST['item_pocet']) && $_POST['item_pocet']!="" && $_POST['item_pocet']>0) {
				$invItem = returnVec($inventory,$_GET['v']);
				if ($invItem[0] == true) {
					if (isset($invItem['id'])) {
						$item2ed = array('id'=>$_GET['v'],'attr'=>'cnt','value'=>$_POST['item_pocet']);
						$inventory = inventar_uprava($inventory,"zmena",$item2ed);
						if ($inventory[1] == true) {
							$inventory[0] = addslashes($inventory[0]);
							mysql_query("UPDATE 3_herna_postava_drd SET inventar = '$inventory[0]' WHERE id = $postava->id AND cid = $postava->cid");
							Header("Location: $inc/herna/$slink/shop/?do=add&p=$postava->jmeno_rew&ok=1");
							exit;
						}
					}
					$typ = $invItem['typ'];
					switch ($typ) {
						case "w":
							$attrCisla = array("item_sz","item_oz","item_utoc","item_hands");
						break;
						case "s":
							$attrCisla = array("item_sz","item_oz","item_utoc","item_hands");
						break;
						case "z":
							$attrCisla = array("item_kz");
						break;
						case "i":
							$attrCisla = array();
						break;
					}
					for ($i=0;$i<count($attrCisla);$i++) {
						if (is_numeric($_POST[$attrCisla[$i]]) && $error==0) {
							if ($_POST[$attrCisla[$i]] > 100 || $_POST[$attrCisla[$i]] < -100 ) {
								$error = 8;
								break;
							}
						}
						else {
							$error = 8;
							break;
						}
					}
					$pocet = $_POST['item_pocet'];
					if ($pocet > 100000) {
						$pocet = 100000;
					}
					$hands = $_POST['item_hands'];
					if ($hands > 2 || $hands <= 0) {
						$hands = 1;
					}
					if ($error > 0) {
						header ("Location: $inc/herna/$slink/shop/?do=add&p=$postava->jmeno_rew");
						exit;
					}
					if ($typ == "w" || $typ == "s") {
						if ($_POST['item_sz'] != $invItem['sila']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'s','value'=>$_POST['item_sz']);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						if ($_POST['item_oz'] != $invItem['obrana']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'ob','value'=>$_POST['item_oz']);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						if ($_POST['item_utoc'] != $invItem['oprava']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'op','value'=>$_POST['item_utoc']);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						$jmeno = _htmlspec($_POST['item_nazev']);
						if ($jmeno != $invItem['jmeno']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'jm','value'=>$jmeno);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						$popis = _htmlspec($_POST['item_popis']);
						if ($popis != $invItem['popis']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'o','value'=>$popis);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						if ($hands != $invItem['hands']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'h','value'=>$hands);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						if ($pocet != $invItem['pocet']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'cnt','value'=>$pocet);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						$inventory = inventar_save($inventory);
					}
					elseif ($typ == "z") {
						if ($_POST['item_kz'] != $invItem['sila']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'s','value'=>$_POST['item_kz']);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						$jmeno = _htmlspec($_POST['item_nazev']);
						if ($jmeno != $invItem['jmeno']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'jm','value'=>$jmeno);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						$popis = _htmlspec($_POST['item_popis']);
						if ($popis != $invItem['popis']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'o','value'=>$popis);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						if ($pocet != $invItem['pocet']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'cnt','value'=>$pocet);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						$inventory = inventar_save($inventory);
					} elseif ($typ == "i") {
						$jmeno = _htmlspec($_POST['item_nazev']);
						if ($jmeno != $invItem['jmeno']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'jm','value'=>$jmeno);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						$popis = _htmlspec($_POST['item_popis']);
						if ($popis != $invItem['popis']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'o','value'=>$popis);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						if ($pocet != $invItem['pocet']) {
							$item2ed = array('id'=>$_GET['v'],'attr'=>'cnt','value'=>$pocet);
							$inventar = editVec($inventory,'i',$item2ed);
						}
						$inventory = inventar_save($inventory);
					}
					else {
						$error++;
					}
					if ($error == 0) {
						if (mb_strlen($inventory,"ISO-8859-1")<32250) {
							$inventory = addslashes($inventory);
							mysql_query("UPDATE 3_herna_postava_drd SET inventar = '$inventory' WHERE id = $postava->id AND cid = $postava->cid");
							Header("Location: $inc/herna/$slink/shop/?do=edit&p=$postava->jmeno_rew&v=$_GET[v]");
							exit;
						}
					}
					else {
						Header("Location: $inc/herna/$slink/shop/?do=edit&p=$postava->jmeno_rew&v=$_GET[v]&error=1");
						exit;
					}
				}
			}
			else {
				Header("Location: $inc/herna/$slink/shop/?do=edit&p=$postava->jmeno_rew&v=$_GET[v]");
				exit;
			}
		break;
		default:
			header ("Location: $inc/herna/$slink/?error=no-action-set");
			exit;
		break;
	}
}
else {
	header ("Location: $inc/herna/$slink/?error=no-rights-to-edit-inventory");
	exit;
}

Header("Location: $inc/herna/$slink/");
exit;

?>
