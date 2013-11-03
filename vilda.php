<?php
ob_start("ob_gzhandler");
include "db/conn.php";

$sqli = "SELECT id, nazev AS title, anotace AS txt FROM vilda ORDER BY id ASC";
$only1 = false;

$title = "Obsah";

if (isset($_GET['kapitola'])) {
	if (($_GET['kapitola'] != "") && ($_GET['kapitola'] > 0) && ($_GET['kapitola'] < 12) && ctype_digit($_GET['kapitola'])) {
		$sqli = "SELECT id, nazev AS title, text AS txt, anotace FROM vilda WHERE id = '$_GET[kapitola]'";
		$only1 = true;
	}
}


$texts = mysql_query($sqli);
$t = "";
if ($only1) {
	$text = mysql_fetch_object($texts);
	$str = $text->txt;
  $str = ereg_replace("(\r\n){2,}","\r\r\n\n",$str);
  $str = ereg_replace("(\r\n)$","",$str);
  $text->txt = stripslashes(str_replace("\n", "<br />", $str));
  $title = $text->title;
	$t .= "<h2>".$text->title."</h2>
	<p><a href='vilda.php'>Zpět na seznam kapitol</a></p>\n";
	$t .= "<h3>Anotace</h3>\n	<p>".$text->anotace."</p>\n	<h3>Text kapitoly</h3>\n";
	$t .= "<div style='margin: 0 2em;'>".$text->txt."</div>";
}
else {
	$t .= "	<h2 class='noborder'>Obsah</h2>\n	<ol>\n";
	while ($text = mysql_fetch_object($texts)) {
		$t .= "	<li><a href='?kapitola=".$text->id."'>".$text->title."</a></li>\n";
	}
	$t .= "</ol>\n";
}

echo "<"."?xml ";
echo 'version="1.0" encoding="utf-8"';
echo "?".">";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title><?php echo $title;?> | Vildovy cesty</title>
  <style type="text/css">
  body { text-align: left; background: #eee url("graphic/fidlaci-bg.gif") repeat; color: #666; font: normal 1em 'Arial CE', 'Arial', 'Helvetica CE', Arial, helvetica, sans-serif; margin: 0; padding: 0;
	}
	a,a:visited,a:active { color: #666;
	}
	a:hover { color: #000;
	}
	.div1 { background-color: #e3e3e3; width: 45em; margin-left: 2em;
	}
	.div2 { background-color: #f2f2f2; width: 42em; padding: 0 0 15em 0; margin: 0;
	}
	h1 { font-size: 3em; font-variant: small-caps; text-decoration: none; font-weight: normal; margin: 0; padding: 0.5em 0 0 0; display: block; border-bottom: 0.25em solid #ececec; color: #444; text-align: center; background-color: #f9f9f9; font-family: 'Times', 'Arial CE', 'Lucida Grande CE', 'Helvetica CE', Verdana, Arial, lucida, sans-serif; letter-spacing: 5pt;
	}
	h2 { text-transform: capitalize; font-variant: small-caps; padding: 0.15em 1.5em; text-indent: 0.5em; font-size: 1.5em; margin: 1.5em 0 0 0; line-height: 1.5em; color: #333; letter-spacing: 0.1em; border-top: 1px solid #ddd; background-color: #ececec; font-weight: normal;
	}
	h3 { color: #666; text-decoration: none; font-variant: normal; font-size: 1em; font-weight: bold; margin: 1em 2em 0em 2em; padding: 0.2em 0 0.2em 1.5em; border-bottom: 1px solid #e5e5e5; line-height: 1.0em;
	}
	p { line-height: 1.5em; padding: 0 0.5em 0.5em 0.5em; margin: 1em 2.1em 0 2em; text-align: justify; color: #666; text-indent: 1em;
	}
	ol, ul { border: 1px solid #ccc; padding: 0.5em 1.5em; margin: 0.5em 10em 0.5em 4em; background-color: #f8f8f8;
	}
	ol li, ul li { margin-left: 1em; padding: 0.25em 0; color: #555;
	}
	ul { margin-top: 1em; list-style-type: square; padding-right: 0.5em; padding-left: 1em; background-color: #eaeaea;
	}
	ol ul {
		border: none; margin: 0.25em 1em 0em 1em; font-size: 0.8em; padding: 0.25em; background: none;
	}
	ol ul li {
		margin-left: 2em; list-style-type: disc;
	}
	.noborder {
		border: none; margin-top: 0.5em; background: none; color: #666;
	}
	</style>
 </head>
 <body>
 	<div class="div1"><div class="div2">
  <h1><?php echo $title;?></h1>
<?php
echo $t;
?>
	</div></div>
 </body>
</html>
