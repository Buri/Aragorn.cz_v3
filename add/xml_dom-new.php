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
		case "z": // ARMOR = Zbroje - silaI = KZ
		case "a": // ARMOR = Zbroje - silaI = KZ
			$i = "\t\t<tr class='i-ar'><td>".$pocetI." </td><td><span onmouseover=\"ddrivetip('$popisI')\" onmouseout=\"hidedrivetip()\">$nazevI</span></td><td>KZ: $silaI</td>";
		break;
		case "i": // ITEM = Vybaveni, obyc.predmet - obycejne veci, jen popis, nekoukat na nic, jen ten popis
				$i = "\t\t<tr class='i-it'><td>".$pocetI." </td><td><span onmouseover=\"ddrivetip('$popisI')\" onmouseout=\"hidedrivetip()\">$nazevI</span></td><td>" . mb_strimwidth($popisI, 0, 20, "...") . "<//td>";
		break;
	}
	return $i;
}

function removeVec($d,$nazev,$id) {
	$root = $d->documentElement;
	$veci = $root->getElementsByTagName($nazev);
	if (is_array($id)){
		while (list($i,$hodnota) = each($id)){
			$vec = $veci->item($hodnota);
			if ($vec->hasAttribute("cnt")) {
				$pocet = $vec->getAttribute("cnt");
				if ($pocet > 1) { $vec->setAttribute("cnt",floor($pocet-1)); }
				else { $vec->setAttribute("cnt",0); }
			}else { $vec->setAttribute("cnt",1); }
		}
	}
	else {
		$vec = $veci->item($id);
		if ($vec->hasAttribute("cnt")) {
			$pocet = $vec->getAttribute("cnt");
			if ($pocet > 1) {$vec->setAttribute("cnt",$pocet-1);
			} else { $vec->setAttribute("cnt",0); }
		} else { $vec->setAttribute("cnt",1); }
	}
	for ($i = 0; $i < $veci->length; $i++) {
		$vec = $veci->item($i);
		$pocet = $vec->getAttribute("cnt");
		if($pocet<1) {
			$root->removeChild($vec);
		}
	}
	return $d;
}

function addVec($d,$nazev,$id) {			// ID = array ('pocet'=>pocet, 'typ'=>typ, ... , 'kde'=>kde);
	$root = $d->documentElement;
	if (isSet($id['id'])) {
		$isID = false;
		if ($root->hasChildNodes()) {
			$items = $root->getElementsByTagName($nazev);
			for ($i=0;$i<$items->length;$i++) {
				$item = $items->item($i);
				$itemId = $item->getAttribute('id');
				if ($itemId == $id['id']) {
					$node = $items->item($i);
					$isID = true;
					break;
				}
			}
		}
		if ($isID) {
			$pocet = $node->getAttribute("cnt");
			$node->setAttribute("cnt",$pocet+$id['pocet']);
		}
		else {
			$node = $d->createElement($nazev);
			$node->setAttribute("id",$id['id']);
			$node->setAttribute("cnt",$id['pocet']);
			$doIt = $root->appendChild($node);
		}
	}
	else {
		$node = $d->createElement($nazev);
		$typ	= $node->setAttribute("t",$id['typ']);
		$sila = $node->setAttribute("s",$id['sila']);
		$obra = $node->setAttribute("ob",$id['obrana']);
		$opra = $node->setAttribute("op",$id['oprava']);
		$opra = $node->setAttribute("h",$id['hands']);
		$name = $node->setAttribute("jm",$id['name']);
		$popis= $node->setAttribute("o",$id['popis']);
		$pocet = $node->setAttribute("cnt",$id['pocet']);
		$doIt = $root->appendChild($node);
	}
	return $d;	
}

function deleteVec($d,$nazev,$id) {
	$root = $d->documentElement;
	$items = $d->getElementsByTagName($nazev);
	$vec = $items->item($id);
	$root->removeChild($vec);
	return $d;
}

function editVec($d,$nazev,$id) {			// ID = array (id->"u ceho", attr->"co zmenit", value->"hodnota")
	$root = $d->documentElement;
	$items = $root->getElementsByTagName($nazev);
	$item = $items->item($id['id']);
	switch ($id['attr']) {
		case "cnt":
			$item->setAttribute($id['attr'],$id['value']);
		break;
		case "s":
		case "ob":
		case "op":
		case "h":
		case "jm":
		case "o":
			if ($item->hasAttribute("id")) {
			}
			else {
				$item->setAttribute($id['attr'],$id['value']);
			}
		break;
	}
	return $d;
}

function inventar_uprava($d,$doWhat,$ids) {
	$doc = $d;
	$doc->formatOutput = false;
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
	$doc->preserveWhiteSpace = false;
	$text = $doc->saveXML();
	$text = preg_replace("'([\n\r])[\s]+'","\\1",$text);
	if (mb_strlen($text,"ISO-8859-1")>32000) {
		$returne = array();
		$returne[] = $d->saveXML();
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
	$text = $d->saveXML();
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
		$doc = new DOMDocument("1.0", "UTF-8");
		$doc->formatOutput = false;
		$doc->preserveWhiteSpace = false;
		$doc->loadXML(stripslashes($f));
		$root = $doc->documentElement;
		if ($root->hasChildNodes()) {
			$inv1 = "<table class='inventar'>
	<tbody>\n";
			$items = $root->getElementsByTagName("i");
			$wp = $sh = $ar = $it = $i2db = $ids = array();
			for ($i = 0; $i < $items->length; $i++) {
				$name = $popis = $typ = "";
				$vec  = $items->item($i);
				$pocet = $vec->getAttribute("cnt");
				$inv = inv_items_menu($i,$pocet);
				if ($vec->hasAttribute("id")) {
					$ids[] = $i;
					$i2db[$i] = $vec->getAttribute("id");
					$pocetIds[$i]=$pocet;
				}
				else {
					$nazev = $vec->getAttribute("jm");
					$popis = $vec->getAttribute("o");
					$typ   = $vec->getAttribute("t");
					switch ($typ){
						case "w":
							$hands= $vec->getAttribute("h"); $sila = $vec->getAttribute("s");
							$obra = $vec->getAttribute("ob"); $opra = $vec->getAttribute("op");
							$wp[$i] = inv_item_see($pocet,$nazev,$typ,$sila,$obra,$opra,$popis,$hands).$inv;
						break;
						case "s":
							$hands= $vec->getAttribute("h"); $sila = $vec->getAttribute("s");
							$obra = $vec->getAttribute("ob"); $opra = $vec->getAttribute("op");
							$sh[$i] = inv_item_see($pocet,$nazev,$typ,$sila,$obra,$opra,$popis,$hands).$inv;
						break;
						case "z":
						case "a":
							$hands= $vec->getAttribute("h"); $sila = $vec->getAttribute("s");
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
//				$removeItems = array_fill_keys(array_values($i2db),"1");
//				print_r($removeItems);
				$iSrc = mysql_query("SELECT * FROM 3_herna_items WHERE id IN ($idsSrc) ORDER BY id ASC");
				while ($iItem = mysql_fetch_object($iSrc)) {
					$dbItems[$iItem->id] = $iItem;
//					unset($removeItems[$iItem->id]);
				}
//				if (count($removeItems)>0) {
//				}
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
						case "z":
						case "a":
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
	$doc = new DOMDocument("1.0", "UTF-8");
	$doc->LoadXML($d);
	return $doc;
}

function inventar_make() {
	$doc = new DOMDocument("1.0", "UTF-8");
	$element = $doc->createElement("vybava");
	$doc->appendChild($element);
return $doc;
}

function returnVec($d,$id) {
	if ($d->hasChildNodes()) {
		$root = $d->documentElement;
		$items = $root->getElementsByTagname("i");
		if ($id>=0 && $id<$items->length && $root->hasChildNodes()) {
			$item = $items->item($id);
			if ($item->hasAttribute("id")) {
				$arr = array(true,'id'=>$item->getAttribute("id"),'pocet'=>$item->getAttribute("cnt"));
			}
			else {
				$arr = array(
								true,
								'jmeno'=>$item->getAttribute("jm"),
								'popis'=>$item->getAttribute("o"),
								'pocet'=>$item->getAttribute("cnt"),
								'sila'=>$item->getAttribute("s"),
								'typ'=>$item->getAttribute("t"),
								'hands'=>$item->getAttribute("h"),
								'obrana'=>$item->getAttribute("ob"),
								'oprava'=>$item->getAttribute("op"),
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
