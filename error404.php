<?php
$redirectToJS = "";

	if ($_SERVER['REDIRECT_QUERY_STRING'] != "") {
		$search = $_SERVER['REQUEST_URI'];
	}
	else {
		$search = substr($_SERVER['SCRIPT_URL'],1);
	}
	

	$haveWWW = strpos($search, "www");

	if ($haveWWW !== false){
		$redirectTo = substr($search,$haveWWW);
		$redirectToJS = "<script type=\"text/javascript\">window.location.href = 'http://$redirectTo';</script>\n";
		$mess = "			<li>Další možnost je, že odkaz <a rel='nofollow' href='http://$redirectTo'>http://$redirectTo</a> je přesně ten, který to měl být.</li>";
	}
	else {
		$haveExtA = explode(".", $search);
		$haveExt = mb_strtolower(array_pop($haveExtA));
		if ($haveExt == "jpg" || $haveExt == "gif" || $haveExt == "png" || $haveExt == "jpeg" || $haveExt == "tmp" || $haveExt == "bmp"){
			$mess = "			<li>Obrázek, který podle zadané adresy hledáte, na serveru nebyl nalezen, protože již byl smazán.</li>";
		}
		else {
			function hasNoDot($var){
				return ((strpos($var,".")!==false)?true:false);
			}
		
			$parser = explode("/",$search);
			$checkOne = join("/",array_filter($parser, "hasNoDot"));
			
			$parser = explode("/",$checkOne);
			$checkTwo = join("/",array_filter($parser, "hasNoDot"));
			if ($checkOne == $checkTwo) {
				$redirectTo = substr($search,strpos($search,$checkOne));
				$mess = "			<li>Další možnost je, že odkaz <a rel='nofollow' href='http://$redirectTo'>http://$redirectTo</a> je přesně ten, který to měl být.</li>";
			}
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>
      Error 404 - adresa "<? echo $search;?>" nebyla nalezena
    </title>
    <? echo $redirectToJS;?>

		<style type="text/css">
		/* <![CDATA[ */
			body,html{margin:0;padding:0;border:none;color:#444;}
			html{background:#f5f5f5 url('/graphic/nenalezeno.png') 95% bottom no-repeat;}
			body{font-family:Georgia, times, serif;background:none;font-size:100%;margin:20pt 30pt;}
			h1{font-size:220%;font-weight:bold;line-height:300%;margin:0;padding:0;color:#999;}
			h3{font-size:130%;font-weight:normal;line-height:200%;margin:10pt 0;padding:0;color:#111;}
			ul{margin:20pt 350px 20pt 20pt;line-height:1.6;font-size:100%;padding-top:10pt;padding-right:10pt;padding-bottom:10pt;border:1px solid #ddd;background-color:#eee;list-style:square;float:left;}
			li{line-height:1.6;font-size:100%;}
			em{text-decoration:underline;color:#f00;}
			small{clear:both;padding-top:80pt;display:block;}
		/* ]]> */
		</style>

  </head>
  <body>
    <h1>
      Error 404
    </h1>
    <h3 style="text-indent:2pt">adresa <em><? echo $search;?></em> nebyla nalezena</h3>
    <ul>
    	<li>Pokračovat můžete na domovskou stránku <a href="http://www.aragorn.cz/">Aragorn.cz</a></li>
    	<li><a href="http://www.aragorn.cz/sitemap.php">Mapa stránek</a> může taktéž pomoci.</li>
    	<li>Pokud se jedná o odkaz z diskuzí, nástěnek či komentářů, patrně autor odkazu chybně zapsal adresu nebo napsal jen její část.</li>
<?php echo $mess;?>
    </ul>
    <small>Aragorn.cz -	<?php echo date("j.n.Y - H:i:s");?></small>
  </body>
</html>