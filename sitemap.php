<?php
ob_start("ob_gzhandler");

include "db/conn.php";

$time = time();
mb_internal_encoding("UTF-8");

echo "<"."?xml version=\"1.0\" encoding=\"utf-8\">
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"cs\" xml:lang=\"cs\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<title>Mapa stránek - Aragorn.cz</title>
<style type=\"text/css\">
  body { text-align: left; background-color: #eee; color: #666; font: normal 1em 'Arial CE', 'Arial', 'Helvetica CE', Arial, helvetica, sans-serif; margin: 0; padding: 0;
	}
	a,a:visited,a:active { color: #666;
	}
	a:hover { color: #000;
	}
	.div1 { background-color: #e3e3e3; width: 45em; margin-left: 2em;
	}
	.div2 { background-color: #f2f2f2; width: 42em; padding: 0 0 15em 0; margin: 0;
	}
	h1 { font-size: 2em; font-variant: small-caps; text-decoration: none; font-weight: normal; margin: 0; padding: 0.5em 0 0 0; display: block; border-bottom: 0.25em solid #ececec; color: #444; text-align: center; background-color: #f9f9f9; font-family: 'Times', 'Arial CE', 'Lucida Grande CE', 'Helvetica CE', Verdana, Arial, lucida, sans-serif; letter-spacing: 5pt;
	}
	h2 { border-right: 1px solid #ccc; border-left: 1px solid #ccc; border-top: 1px solid #ccc; text-transform: capitalize; font-variant: small-caps; padding: 0.15em 1.5em; text-indent: 0.5em; font-size: 1.5em; margin: 1.5em 0 0 0; line-height: 1.5em; color: #333; letter-spacing: 0.1em; background-color: #ececec; font-weight: normal;
	}
	h3 { width: 80%; color: #666; text-decoration: none; font-variant: normal; font-size: 1em; font-weight: bold; margin: 1em 0 0 0; padding: 0.2em 0 0.2em 1.5em; border-bottom: 1px solid #e5e5e5; line-height: 1.0em;
	}
	ol, ul { padding: 0.5em; margin: 0.5em;
	}
	ol li, ul li { margin-left: 0; padding: 0.25em 0; color: #555; list-style-type: none;
	}
	ul { margin-top: 1em; list-style-type: square; padding-right: 0.5em; padding-left: 0em;
	}
	ol ul {
		border: none; margin: 0.25em 1em 0em 1em; font-size: 0.8em; padding: 0.25em; background: none;
	}
	ol div {
		border-width: 0 1px 1px 1px; border-style: solid; border-color: #ccc;
	}
	ol li div ul {
		margin-top: 0;
	}
	ol ul li {
		margin-left: 0.5em; list-style-type: none; font-size: 0.9em;
	}
	hr {
		color: #999; background-color: #999; height: 1px; border: none; width: 80%; text-align: left; margin-left: 0; margin-right: auto;
	}
	.galerie {
		background: #f2f2f2 url('graphic/map_galerie_bg.gif') top right repeat-y;
	}
	.clanky {
		background: #f2f2f2 url('graphic/map_clanky_bg.gif') top right repeat-y;
	}
	.diskuze {
		background: #f2f2f2 url('graphic/map_diskuze_bg.gif') top right repeat-y;
	}
	.herna {
		background: #f2f2f2 url('graphic/map_herna_bg.gif') top right repeat-y;
	}
</style>
</head>
<body>
<div class=\"div1\"><div class=\"div2\">
<h1>Aragorn.cz - Mapa stránek</h1>
<ol>\n\n";

echo "<li><h2>Obecné odkazy</h2><div>
<ul>
	<li><a href=\"/admins/\" title=\"Seznam administrátorů a jejich funkcí\">Administrátoři</a></li>
	<li><a href=\"/chat/\" title=\"Seznam chatovacích místností\">Chat</a></li>
	<li><a href=\"/napoveda/\" title=\"Nápověda k serveru a jeho možnostem\">Nápověda</a></li>
	<li><a href=\"/registrace/\" title=\"Registační formulář na Aragorn.cz\">Registrace</a></li>
	<li><a href=\"/uzivatele/\" title=\"Seznam uživatelů\">Uživatelé</a></li>
</ul>
</div></li>\n";

echo "<li><h2>Články</h2><div class='clanky'>\n";
	echo "<ul>\n";
		$clankyS = mysql_query("SELECT nazev,nazev_rew,anotace AS popis FROM 3_clanky WHERE schvaleno = '1' ORDER BY nazev ASC, nazev_rew ASC");
		while ($cItem = mysql_fetch_object($clankyS)) {
			echo "<li><a href=\"/clanky/$cItem->nazev_rew/\" title=\""._htmlspec(mb_strimwidth(stripslashes($cItem->popis), 0, 30, "..."))."\">"._htmlspec(stripslashes($cItem->nazev))."</a> - "._htmlspec(mb_strimwidth(stripslashes($cItem->popis), 0, 100, "..."))."</li>\n";
		}
	echo "</ul>\n";
echo "</div></li>\n\n";

echo "<li><h2>Diskuze</h2><div class='diskuze'>\n";
	echo "<ul>\n";
		$diskuzeS = mysql_query("SELECT o.nazev AS okruh_name,t.okruh,t.nazev,t.nazev_rew,t.popis FROM 3_diskuze_topics AS t, 3_diskuze_groups AS o WHERE t.okruh = o.id AND t.schvaleno = '1' ORDER BY o.nazev ASC, t.nazev_rew ASC");
		while ($dItem = mysql_fetch_object($diskuzeS)) {
			$new = $dItem->okruh;
			if ($new != $old) {
				$old = $new;
				echo "<hr /><h3>Diskuzní okruh: $dItem->okruh_name</h3>\n";
			}
			echo "<li><a href=\"/diskuze/$dItem->nazev_rew/\" title=\""._htmlspec(mb_strimwidth(stripslashes($dItem->popis), 0, 60, "..."))."\">"._htmlspec(stripslashes($dItem->nazev))."</a> - "._htmlspec(mb_strimwidth(stripslashes($dItem->popis), 0, 100, "..."))."</li>\n";
		}
	echo "</ul>\n";
echo "</div></li>\n\n";

echo "<li><h2>Herna</h2><div class='herna'>\n";
	echo "<ul>\n";
		$hernaS = mysql_query("SELECT nazev,nazev_rew,popis FROM 3_herna_all WHERE schvaleno = '1' ORDER BY nazev ASC, nazev_rew ASC");
		while ($hItem = mysql_fetch_object($hernaS)) {
			echo "<li><a href=\"/herna/$hItem->nazev_rew/\" title=\""._htmlspec(mb_strimwidth(stripslashes($hItem->popis), 0, 60, "..."))."\">"._htmlspec(stripslashes($hItem->nazev))."</a> - "._htmlspec(mb_strimwidth(stripslashes($hItem->popis), 0, 100, "..."))."</li>\n";
		}
	echo "</ul>\n";
echo "</div></li>\n\n";

echo "<li><h2>Galerie</h2><div class='galerie'>\n";
	echo "<ul>\n";
		$galerieS = mysql_query("SELECT nazev,nazev_rew,popis FROM 3_galerie WHERE schvaleno = '1' ORDER BY nazev ASC, nazev_rew ASC");
		while ($gItem = mysql_fetch_object($galerieS)) {
			echo "<li><a href=\"/galerie/$gItem->nazev_rew/\" title=\""._htmlspec(mb_strimwidth(stripslashes($gItem->popis), 0, 60, "..."))."\">"._htmlspec(stripslashes($gItem->nazev))."</a> - "._htmlspec(mb_strimwidth(stripslashes($gItem->popis), 0, 100, "..."))."</li>\n";
		}
	echo "</ul>\n";
echo "</div></li>\n\n";


echo "</ol>
	</div></div>
</body>
</html>";

?>
