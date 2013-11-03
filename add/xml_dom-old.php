<?php

/*
-- par vysvetlivek pro uvod --
Atributy veci
h		= hands		... pocet rukou, pro vec (armor,itemy = 0, zbrane = 1-2)
cnt	= pocet		... kolikrat to je ve vyskytu v inventari
t		= typ			... co to ej za vec, Weapon, Armor, Shot, Item, Roll, Potion
s		= sila		... sila zbarne / kvalita zbroje
ob	= obrana	... obrana zbrane, u strelnych je vzdy -2
op	= oprava	... utocnost zbrane nebo popisek u lektvaru ci kouzel
o		= popis		... o co se jedna, nekolika malo slovy, strucne
jm	= name		... jmeno veci, nic vic, nic min

*/

function inv_item_see($pocetI, $nazevI, $typI, $silaI, $obranaI, $opravaI, $popisI = "", $hands = 0) {
	switch ($typI) {
		case "w": // WEAPONS = Zbrane tvari v tvar - silaI = SZ, ObranaI = OZ, opravaI = UZ
			$opravaI = intval($opravaI);
			$opravaI = $opravaI>0? "+$opravaI":$opravaI;
			$i = "\t\t<tr class='i-wp'><td>".$pocetI." </td><td><span onmouseover=\"ddrivetip('$popisI')\" onmouseout=\"hidedrivetip()\">$nazevI</span></td><td>SZ: $silaI | Útoč: $opravaI | OZ: $obranaI</td>";
		break;
		case "s": // SHOOTING = Strelne zbrane - silaI = SZ, ObranaI = OZ (vets. zaporna)
			$i = "\t\t<tr class='i-st'><td>".$pocetI." </td><td><span onmouseover=\"ddrivetip('$popisI')\" onmouseout=\"hidedrivetip()\">$nazevI</span></td><td>SZ: $silaI | Útoč: $opravaI | OZ: $obranaI</td>";
		break;
		case "z":
		case "a":
			$i = "\t\t<tr class='i-ar'><td>".$pocetI." </td><td><span onmouseover=\"ddrivetip('$popisI')\" onmouseout=\"hidedrivetip()\">$nazevI</span></td><td>KZ: $silaI</td>";
		break;
		case "i": // ITEM = Vybaveni, obyc.predmet - obycejne veci, jen popis, nekoukat na nic, jen ten popis
				$i = "\t\t<tr class='i-it'><td>".$pocetI." </td><td><span onmouseover=\"ddrivetip('$popisI')\" onmouseout=\"hidedrivetip()\">$nazevI</span></td><td>" . mb_strimwidth($popisI, 0, 20, "...") . "<//td>";
		break;
	}
	return $i;
}

function removeVec($d,$nazev,$id) {
	$root = $d->document_element();
	$veci = $root->get_elements_by_tagname($nazev);
	if (is_array($id)){
		while (list($i,$hodnota) = each($id)){
			$vec = $veci[$hodnota];
			if ($vec->has_attribute("cnt")) {
				$pocet = $vec->get_attribute("cnt");
				if ($pocet > 1) { $vec->set_attribute("cnt",floor($pocet-1)); }
				else { $vec->set_attribute("cnt",0); }
			}else { $vec->set_attribute("cnt",1); }
		}
	}
	else {
		$vec = $veci[$id];
		if ($vec->has_attribute("cnt")) {
			$pocet = $vec->get_attribute("cnt");
			if ($pocet > 1) {$vec->set_attribute("cnt",$pocet-1);
			} else { $vec->set_attribute("cnt",0); }
		} else { $vec->set_attribute("cnt",1); }
	}
	for ($i = 0; $i < count($veci); $i++) {
		$vec = $veci[$i];
		$pocet = $vec->get_attribute("cnt");
		if($pocet<1) {
			$root->remove_child($vec);
		}
	}
	return $d;
}

function addVec($d,$nazev,$id) {			// ID = array ('pocet'=>pocet, 'typ'=>typ, ... , 'kde'=>kde);
	$root = $d->document_element();
	if (isSet($id['id'])) {
		$isID = false;
		if ($root->has_child_nodes()) {
			$items = $root->get_elements_by_tagname($nazev);
			for ($i=0;$i<count($items);$i++) {
				$item = $items[$i];
				$itemId = $item->get_attribute('id');
				if ($itemId == $id['id']) {
					$node = $items[$i];
					$isID = true;
					break;
				}
			}
		}
		if ($isID) {
			$pocet = $node->get_attribute("cnt");
			$node->set_attribute("cnt",$pocet+$id['pocet']);
		}
		else {
			$node = $d->create_element($nazev);
			$node->set_attribute("id",$id['id']);
			$node->set_attribute("cnt",$id['pocet']);
			$doIt = $root->append_child($node);
		}
	}
	else {
		$node = $d->create_element($nazev);
		$typ	= $node->set_attribute("t",$id['typ']);
		$sila = $node->set_attribute("s",$id['sila']);
		$obra = $node->set_attribute("ob",$id['obrana']);
		$opra = $node->set_attribute("op",$id['oprava']);
		$opra = $node->set_attribute("h",$id['hands']);
		$name = $node->set_attribute("jm",$id['name']);
		$popis= $node->set_attribute("o",$id['popis']);
		$pocet = $node->set_attribute("cnt",$id['pocet']);
		$doIt = $root->append_child($node);
	}
	return $d;	
}

function deleteVec($d,$nazev,$id) {
	$root = $d->document_element();
	$items = $d->get_elements_by_tagname($nazev);
	$vec = $items[$id];
	$root->remove_child($vec);
	return $d;
}

function editVec($d,$nazev,$id) {			// ID = array (id->"u ceho", attr->"co zmenit", value->"hodnota")
	$root = $d->document_element();
	$items = $root->get_elements_by_tagname($nazev);
	$item = $items[$id['id']];
	switch ($id['attr']) {
		case "cnt":
			$item->set_attribute($id['attr'],$id['value']);
		break;
		case "s":
		case "ob":
		case "op":
		case "h":
		case "jm":
		case "o":
			if ($item->has_attribute("id")) {
			}
			else {
				$item->set_attribute($id['attr'],$id['value']);
			}
		break;
	}
	return $d;
}

function inventar_uprava($d,$doWhat,$ids) {
	$doc = $d;
	if ($doWhat == "remove") {
		$doc = removeVec($doc,'i',$ids);
	}
	elseif ($doWhat == "add") {
		$doc = addVec($doc,'i',$ids);
	}
	elseif ($doWhat == "delete") {
		$doc = deleteVec($doc,'i',$ids);
	}
	elseif ($doWhat == "zmena") {
		$doc = editVec($doc, 'i',$ids);
	}
	$text = $doc->dump_mem(false,"UTF-8");
	$text = preg_replace("'([\n\r])[\s]+'","\\1",$text);
	if (mb_strlen($text,"ISO-8859-1")>32000) {
		$returne = array();
		$returne[] = $d->dump_mem(false,"UTF-8");
		$returne[] = false;
		return $returne;
	}
	else {
		$returne = array();
		$returne[] = $text;
		$returne[] = true;
		return $returne;
	}
}

function inventar_save($d) {
	$text = $d->dump_mem(false,"UTF-8");
	$text = preg_replace("'([\n\r])[\s]+'","\\1",$text);
	return $text;
}

function inv_items_menu($i,$cnt) {
global $slink,$allow,$postava;
	$inve =	"<td class='i-menu'><a href='?akce=inv&amp;v=$i&amp;do=del' title='zahodit'>X</a>";
	if ($cnt>1) {
		$inve .= " <a href='?akce=inv&amp;v=$i&amp;do=rm1' title='zahodit jen jeden kus'>-1</a>";
	}
	if ($cnt>10) {
		$inve .= " <a href='?akce=inv&amp;v=$i&amp;do=rm10' title='zahodit 10 kusů'>-10</a>";
	}
	if ($cnt>100) {
		$inve .= " <a href='?akce=inv&amp;v=$i&amp;do=rm100' title='zahodit 100 kusů'>-100</a>";
	}
	if ($allow == "pj") {
		$inve .= " <a href='/herna/$slink/shop/?do=edit&amp;p=$postava->jmeno_rew&amp;v=$i'>Edit</a>";
	}
	$inve .= "</td></tr>\n";
	return $inve;
}

function inventar($f) {
	global $slink,$sslink,$allow;
	$inv = $invA = "";
	if (mb_strlen($f)>2) {
		$doc = domxml_open_mem(stripslashes($f));
		$root = $doc->document_element();
		if ($root->has_child_nodes()) {
			$inv1 = "<table class='inventar'>
	<tbody>\n";
			$items = $root->get_elements_by_tagname("i");
			$wp = $sh = $ar = $it = $i2db = $ids = array();
			for ($i = 0; $i < count($items); $i++) {
				$name = $popis = $typ = "";
				$vec  = $items[$i];
				$pocet = $vec->get_attribute("cnt");
				$inv = inv_items_menu($i,$pocet);
				if ($vec->has_attribute("id")) {
					$ids[] = $i;
					$i2db[$i] = $vec->get_attribute("id");
					$pocetIds[$i]=$pocet;
				}
				else {
					$nazev = $vec->get_attribute("jm");
					$popis = $vec->get_attribute("o");
					$typ   = $vec->get_attribute("t");
					switch ($typ){
						case "w":
							$hands= $vec->get_attribute("h"); $sila = $vec->get_attribute("s");
							$obra = $vec->get_attribute("ob"); $opra = $vec->get_attribute("op");
							$wp[$i] = inv_item_see($pocet,$nazev,$typ,$sila,$obra,$opra,$popis,$hands).$inv;
						break;
						case "s":
							$hands= $vec->get_attribute("h"); $sila = $vec->get_attribute("s");
							$obra = $vec->get_attribute("ob"); $opra = $vec->get_attribute("op");
							$sh[$i] = inv_item_see($pocet,$nazev,$typ,$sila,$obra,$opra,$popis,$hands).$inv;
						break;
						case "a":
						case "z":
							$hands= $vec->get_attribute("h"); $sila = $vec->get_attribute("s");
							$ar[$i] = inv_item_see($pocet,$nazev,$typ,$sila,"","",$popis,$hands).$inv;
						break;
						case "i":
							$it[$i] = inv_item_see($pocet,$nazev,$typ,"","","",$popis,0).$inv;
						break;
					}
				}
			}
			if (count($ids)>0) {
				$inv = "";
				$idsSrc = join(",",$i2db);
				$iSrc = mysql_query("SELECT * FROM 3_herna_items WHERE id IN ($idsSrc) ORDER BY id ASC");
				while ($iItem = mysql_fetch_object($iSrc)) {
					$dbItems[$iItem->id] = $iItem;
				}
				for ($i=0;$i<count($ids);$i++) {
					$inv = "";
					$ihere = $i2db[$ids[$i]];
					$ihere2= $ids[$i];
					$nazev = _htmlspec(stripslashes($dbItems[$ihere]->nazev));
					$popis = _htmlspec(stripslashes($dbItems[$ihere]->popis));
					$hands = $dbItems[$ihere]->hands;
					$sila  = $dbItems[$ihere]->sila;
					$obra  = $dbItems[$ihere]->obrana;
					$opra  = $dbItems[$ihere]->oprava;
					$pocet = $pocetIds[$ihere2];
					$typ   = $dbItems[$ihere]->typ;
					$inv   = inv_items_menu($ihere2,$pocet);
					switch ($typ){
						case "w":
							$wp[$ihere2] = inv_item_see($pocet,$nazev,$typ,$sila,$obra,$opra,$popis,$hands).$inv;
						break;
						case "s":
							$sh[$ihere2] = inv_item_see($pocet,$nazev,$typ,$sila,$obra,$opra,$popis,$hands).$inv;
						break;
						case "a":
						case "z":
							$ar[$ihere2] = inv_item_see($pocet,$nazev,$typ,$sila,"","",$popis,$hands).$inv;
						break;
						case "i":
							$it[$ihere2] = inv_item_see($pocet,$nazev,$typ,"","","",$popis,0).$inv;
						break;
					}
				}
			}
			$inv2 = "\t</tbody>\n\t</table>";
			$invA = $inv1.join("",$wp).join("",$sh).join("",$ar).join("",$it).$inv2;
		}
		else {
			$invA = "";
		}
	} else {
		$invA = "";
	}
	return $invA;
}

function inventar_read($d) {
	$doc = domxml_open_mem($d);
	return $doc;
}

function inventar_make() {
	$doc = domxml_new_doc("1.0");
	$root = $doc->create_element("vybava");
	$doc->append_child($root);
return $doc;
}

function returnVec($d,$id) {
	if ($d->has_child_nodes()) {
		$root = $d->document_element();
		$items = $root->get_elements_by_tagname("i");
		if ($id>=0 && $id<count($items) && count($items)>0 && $root->has_child_nodes()) {
			$item = $items[$id];
			if ($item->has_attribute("id")) {
				$arr = array(true,'id'=>$item->get_attribute("id"),'pocet'=>$item->get_attribute("cnt"));
			}
			else {
				$arr = array(
								true,
								'jmeno'=>$item->get_attribute("jm"),
								'popis'=>$item->get_attribute("o"),
								'pocet'=>$item->get_attribute("cnt"),
								'sila'=>$item->get_attribute("s"),
								'typ'=>$item->get_attribute("t"),
								'hands'=>$item->get_attribute("h"),
								'obrana'=>$item->get_attribute("ob"),
								'oprava'=>$item->get_attribute("op"),
							);
			}
			return $arr;
		}
		else {
			$arr = array(false);
			return $arr;
		}
	}
	else {
		$arr = array(false);
		return $arr;
	}
}
?>
