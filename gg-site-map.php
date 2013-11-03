<?php

ob_start("ob_gzhandler");

include "db/conn.php";

	$time = time();
	mb_internal_encoding("UTF-8");

	header( "Content-type: application/xml; charset=\""."UTF-8". "\"", true );
	header( 'Pragma: no-cache' );

echo "<"."?"."xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";

function make_my_url($link,$prio=0.75,$freq="",$last="") {
	if ($last > 1000000)
		$last = "<lastmod>".date("Y-m-d\TH:i:s+01:00", $last)."</lastmod>";
	if ($freq != "")
		$last .= "<changefreq>".$freq."</changefreq>";

	$last .= "<priority>".$prio."</priority>";
	echo "<url><loc>http://www.aragorn.cz/$link</loc>$last</url>\n";
}


if (isset($_GET['s'])) {
	switch ($_GET['s']) {
		case "clanky":
			echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
			make_my_url("clanky/",0.7);
			$clankyS = mysql_query("SELECT nazev_rew FROM 3_clanky WHERE schvaleno = '1' ORDER BY nazev_rew ASC");
			while ($cItem = mysql_fetch_row($clankyS)) {
				make_my_url("clanky/$cItem[0]/",0.5,"daily");
			}
			echo "</urlset>";
		break;
		case "diskuze":
			echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
			make_my_url("diskuze/",0.7);
			$diskuzeS = mysql_query("SELECT nazev_rew FROM 3_diskuze_topics WHERE schvaleno = '1' ORDER BY nazev_rew ASC");
			while ($dItem = mysql_fetch_row($diskuzeS)) {
				make_my_url("diskuze/$dItem[0]/",0.6,"daily");
			}
			echo "</urlset>";
		break;
		case "herna":
			echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
			make_my_url("herna/",0.7);
			$hernaS = mysql_query("SELECT nazev_rew, aktivita FROM 3_herna_all WHERE schvaleno = '1' ORDER BY nazev ASC, nazev_rew ASC");
			while ($hItem = mysql_fetch_row($hernaS)) {
				make_my_url("herna/$hItem[0]/",0.6,"daily",$hItem[1]);
			}
			echo "</urlset>";
		break;
		case "galerie":
			echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
			make_my_url("galerie/",0.7);
			$galerieS = mysql_query("SELECT nazev_rew FROM 3_galerie WHERE schvaleno = '1' ORDER BY nazev_rew ASC");
			while ($gItem = mysql_fetch_row($galerieS)) {
				make_my_url("galerie/$gItem[0]/",0.5,"daily");
			}
			echo "</urlset>";
		break;
		case "other":
		case "others":
			echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
			make_my_url("admins/",0.5);
			make_my_url("chat/",0.7);
			make_my_url("napoveda/",0.7);
			make_my_url("uzivatele/",0.7);
			echo "</urlset>";
		break;
		default:
			echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=clanky</loc></sitemap>
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=herna</loc></sitemap>
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=diskuze</loc></sitemap>
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=galerie</loc></sitemap>
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=other</loc></sitemap>
</sitemapindex>';
		break;
	}
}
else {
	echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=clanky</loc></sitemap>
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=herna</loc></sitemap>
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=diskuze</loc></sitemap>
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=galerie</loc></sitemap>
	<sitemap><loc>http://www.aragorn.cz/gg-site-map.php?s=other</loc></sitemap>
</sitemapindex>';
}

?>