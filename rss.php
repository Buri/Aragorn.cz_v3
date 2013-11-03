<?php
// include "./db/conn.php";
mb_internal_encoding("UTF-8");

$desc = "Od všeho trochu. (Další samostatné RSS kanály pro diskuze, články, jeskyně či galerii jsou odkazovány přímo v daných sekcích.)";

function querytize($section) {
  $prfx = $section;
  $ttl = "";
	switch ($section) {
	  case "clanky":
			$desc = "Nejnovější příspěvky v článcích";
	    $tbl = $section;
	    $ttl = "Články";
	    $sql = "SELECT concat(t.nazev,' (článek)') AS title, t.nazev_rew AS link, t.anotace, t.text, t.compressed, t.sekce, t.schvalenotime AS cas, u.login AS author, u.login_rew AS author_rew, u.level FROM 3_$tbl AS t, 3_users AS u WHERE t.schvaleno = '1' AND t.autor = u.id ORDER BY t.schvalenotime DESC";
	  break;
	  case "galerie":
			$desc = "Aktuální nové obrázky v galerii.";
	    $tbl = $section;
	    $ttl = "Galerie";
	    $sql = "SELECT concat(t.nazev,' (obrázek)') AS title, t.nazev_rew AS link, t.popis AS description, t.thumb, t.schvalenotime AS cas, u.login AS author, u.login_rew AS author_rew, u.level FROM 3_$tbl AS t, 3_users AS u WHERE t.schvaleno = '1' AND t.autor = u.id ORDER BY t.schvalenotime DESC";
	  break;
	  case "herna":
			$desc = "Naposledy založené a schválené jeskyně.";
	    $tbl = "herna_all";
	    $ttl = "Herna";
	    $sql = "SELECT concat(t.nazev,' (hra)') AS title, t.nazev_rew AS link, t.popis AS description, t.zalozeno AS cas, u.login AS author, u.login_rew AS author_rew, u.level FROM 3_$tbl AS t, 3_users AS u WHERE t.schvaleno = '1' AND t.uid = u.id ORDER BY zalozeno DESC";
	  break;
	  case "diskuze":
			$desc = "Právě čerstvá diskuzní témata.";
	    $tbl = "diskuze_topics";
	    $ttl = "Diskuze";
	    $sql = "SELECT concat(t.nazev,' (diskuze)') AS title, t.nazev_rew AS link, t.popis AS description, t.schvalenotime AS cas, u.login AS author, u.login_rew AS author_rew, u.level FROM 3_$tbl AS t, 3_users AS u WHERE t.schvaleno = '1' AND t.owner = u.id ORDER BY t.schvalenotime DESC";
	  break;
	  default:
	    $prfx = $tbl = $sql = false;
	  break;
	}
	$r = array("prefix"=>$prfx,"query"=>$sql,"title"=>$ttl,"desc"=>$desc);
  return $r;
}

$posts = array();
$doDefault = true;

if (isset($_GET['slink']) && $_GET['slink'] != "") {
	$a = querytize($_GET['slink']);
	$linkAdd = $_GET['slink']."/";
	if ($a["prefix"] != false) {
		$title = " - ".$a["title"];
		$desc = $a["desc"];
		include "./rs/add/funkce.php"; // potrebna funkce viewSekce() pro clanky :)
	  $doDefault = false;
		$aS = mysql_query($a['query']." LIMIT 15");
		while ($aI = mysql_fetch_object($aS)) {
		  $ax = array();
		  $ax["title"] = $aI->title;

		  if ($aI->level>2) $aI->author .= " *";

		  switch ($a["prefix"]) {
	  	  case "clanky":
	  	  	if ($aI->compressed) $aI->text = gzuncompress($aI->text);
	  	  	$aI->text = strip_tags($aI->text);
	  	  	$loc = mb_strpos($aI->text, " ", 200, "UTF-8");
	  	  	$aI->text = $aI->anotace."\n".mb_substr($aI->text, 0, $loc, "UTF-8");
					$ax["description"] = "Autor: <a href=\"$inc/uzivatele/".$aI->author_rew."/\">".$aI->author."</a> | Sekce: <a href=\"$inc/clanky/?sekce=".$aI->sekce."\">".viewSekce($aI->sekce)."</a> <br /><br />".nl2br(strip_tags($aI->text));
	    	break;
		    case "herna":
		    	$posss = mb_strpos($aI->description, " ", 200);
					if ($posss < 200) $posss = 220;
					$ax["description"] = "Pán Jeskyně: <a href=\"$inc/uzivatele/".$aI->author_rew."/\">".$aI->author."</a> <br /><br />".mb_substr($aI->description, 0, $posss);
		    break;
	  	  case "galerie":
					$ax["description"] = "<a href=\"$inc/galerie/$aI->link/\" style=\"float:left;margin:0 10px 10px 0\"><img style=\"border:0\" src=\"$inc/galerie/".$aI->thumb."\" /></a> Autor: <a href=\"$inc/uzivatele/".$aI->author_rew."/\">".$aI->author."</a> <br /><br />".$aI->description;
	    	break;
		    case "diskuze":
					$ax["description"] = "Majitel diskuzního tématu: <a href=\"$inc/uzivatele/".$aI->author_rew."/\">".$aI->author."</a> <br /><br />".$aI->description;
		    break;
	    	default:
					$ax["description"] = $aI->description;
	    	break;
			}
		  $ax["link"] = $a["prefix"]."/".$aI->link;
		  if (isset($posts[$aI->cas])) {
			  do {
		  	  $aI->cas += 1;
				} while (isset($posts[$aI->cas]));
				$posts[$aI->cas] = $ax;
			}
			else $posts[$aI->cas] = $ax;
		}
	}
}

if ($doDefault == true) {

	include "./rs/add/funkce.php"; // potrebna funkce viewSekce() pro clanky :)
	$title = "";
	$linkAdd = "";
	$a = querytize("clanky");
	$aS = mysql_query($a['query']." LIMIT 20");
	while ($aI = mysql_fetch_object($aS)) {
	  $ax = array();
	  $ax["title"] = $aI->title;

	  if ($aI->level>2) $aI->author .= " *";

  	if ($aI->compressed) $aI->text = gzuncompress($aI->text);
  	$aI->text = strip_tags($aI->text);
  	$loc = mb_strpos($aI->text, " ", 200, "UTF-8");
  	$aI->text = $aI->anotace."\n".mb_substr($aI->text, 0, $loc, "UTF-8");
		$ax["description"] = "Autor: <a href=\"$inc/uzivatele/".$aI->author_rew."/\">".$aI->author."</a> | Sekce: <a href=\"$inc/clanky/?sekce=".$aI->sekce."\">".viewSekce($aI->sekce)."</a> <br /><br />".nl2br(strip_tags($aI->text));

	  $ax["link"] = $a["prefix"]."/".$aI->link;
	  if (isset($posts[$aI->cas])) {
		  do {
		    $aI->cas += 1;
			} while (isset($posts[$aI->cas]));
			$posts[$aI->cas] = $ax;
		}
		else $posts[$aI->cas] = $ax;
	}

	$b = querytize("herna");
	$bS = mysql_query($b['query']." LIMIT 20");
	while ($bI = mysql_fetch_object($bS)) {
	  $bx = array();
	  $bx["title"] = $bI->title;

	  if ($bI->level>2) $bI->author .= " *";

		$posss = mb_strpos($bI->description, " ", 200);
		if ($posss < 200) $posss = 250;
		$bx["description"] = "Pán Jeskyně: <a href=\"$inc/uzivatele/".$bI->author_rew."/\">".$bI->author."</a> <br /><br />".mb_substr($bI->description, 0, $posss);
	  $bx["link"] = $b["prefix"]."/".$bI->link;
	  if (isset($posts[$bI->cas])) {
		  do {
		    $bI->cas += 1;
			} while (isset($posts[$bI->cas]));
			$posts[$bI->cas] = $bx;
		}
		else $posts[$bI->cas] = $bx;
	}

	$c = querytize("galerie");
	$cS = mysql_query($c['query']." LIMIT 20");
	while ($cI = mysql_fetch_object($cS)) {
	  $cx = array();
	  $cx["title"] = $cI->title;

	  if ($cI->level>2) $cI->author .= " *";

		$cx["description"] = "<a href=\"$inc/galerie/$cI->link/\" style=\"float:left;margin:0 10px 10px 0\"><img style=\"border:0\" src=\"$inc/galerie/".$cI->thumb."\" /></a> Autor: <a href=\"$inc/uzivatele/".$cI->author_rew."/\">".$cI->author."</a> <br /><br />".$cI->description;
	  $cx["link"] = $c["prefix"]."/".$cI->link;
	  if (isset($posts[$cI->cas])) {
		  do {
		    $cI->cas += 1;
			} while (isset($posts[$cI->cas]));
			$posts[$cI->cas] = $cx;
		}
		else $posts[$cI->cas] = $cx;
	}

	$d = querytize("diskuze");
	$dS = mysql_query($d['query']." LIMIT 20");
	while ($dI = mysql_fetch_object($dS)) {
	  $dx = array();
	  $dx["title"] = $dI->title;

	  if ($dI->level>2) $dI->author .= " *";

		$dx["description"] = "Majitel diskuzního tématu: <a href=\"$inc/uzivatele/".$dI->author_rew."/\">".$dI->author."</a> <br /><br />".$dI->description;
	  $dx["link"] = $d["prefix"]."/".$dI->link;
	  if (isset($posts[$dI->cas])) {
		  do {
		    $dI->cas += 1;
			} while (isset($posts[$dI->cas]));
			$posts[$dI->cas] = $dx;
		}
		else $posts[$dI->cas] = $dx;
	}
}

 krsort($posts); // array sort by KEYS ASC with preserving key=>value pairs
$keys = array_keys($posts);
$key = $keys[0];

header('Content-type: text/xml; charset=utf-8', true);
header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . " GMT");
echo '<'.'?xml version="1.0" encoding="utf-8" ?'.'>
<'.'?xml-stylesheet type="text/xsl" href="'.$inc.'/rss.xsl" ?'.'>
';
?>
<!-- generator="Aragorn.RSS.3.1" -->
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
	<title>Aragorn.cz RSS<?php echo $title; ?></title>
	<link><?php echo $inc;?>/<?php echo $linkAdd;?></link>
	<description>Nejen Dračí Doupě, RPG a fantasy online. <?php echo $desc; ?></description>
	<image><url><?php echo $inc;?>/aragorn-logo.png</url><link><?php echo $inc;?>/<?php echo $linkAdd;?></link><title>Aragorn.cz RSS<?php echo $title; ?></title><description>Nejen Dračí Doupě, RPG a fantasy online. <?php echo $desc; ?></description></image>
	<lastBuildDate><?php echo date("r",$key);?></lastBuildDate>
	<year><?php echo date("Y");?></year>
<?php
$cnt=0;
foreach ($posts as $k=>$post) {
$cnt++;
if ($cnt>20) break;
?>
	<item>
		<title><?php echo _htmlspec($post["title"]); ?></title>
<?php // echo "<!-- ".date("Y-m-d H:i:s",$k)." -->";?>
		<description><![CDATA[<?php echo $post["description"]; ?>]]></description>
		<link><?php echo $inc."/".$post["link"]; ?>/</link>
		<pubDate><?php echo date("r",$k); ?></pubDate>
	</item>
<?php
}
?>
</channel>
</rss>
