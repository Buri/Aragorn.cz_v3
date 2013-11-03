<?php

$menuLinks['upload'] = "Nahrávání obrázků";
$requireRights['upload'] = upload;

function upload_head($rub) {
	global $time;
	$info = 1;

	switch ($_GET['op']) {
		//nahrani obrazku
		case 1:
			if (isset($_POST['nazev']) && strlen($_POST['nazev']) > 2 && isset($_FILES['soubor']) && is_uploaded_file($_FILES['soubor']['tmp_name']) && $_FILES["soubor"]["size"] <= (1024*300)) {
				$tmp = 'graphic/content/' . $_SESSION['uid'].'-'.$time.'.tmp';
				if (move_uploaded_file($_FILES['soubor']['tmp_name'], $tmp)) {
					$size = getimagesize($tmp);
					if (($size[2] != 1) && ($size[2] != 2) && ($size[2] != 3)) {
						$info = 4;
					}
					else {
						$ext = (($size[2] == 2) ? 'jpg' : (($size[2] == 3) ? 'png' : 'gif'));
						$f = do_seo(trim($_POST['nazev'])) . '_by_' . $_SESSION['login_rew'];
						if (file_exists('graphic/content/'.$f.'.'.$ext)){
							$filenameAdd = 1;
							while (file_exists('graphic/content/'.$f.'_'.$filenameAdd.'.'.$ext)) {
								$filenameAdd += 1;
							}
							$filenameAdd = '_'.$filenameAdd;
						}
						$f = $f . $filenameAdd . '.' . $ext;
						if (!copy($tmp, 'graphic/content/' . $f)) {
							$info = 3;
						}
						else {
							$info = 2;
						}
						@unlink($tmp);
					}
				}
				else {
					$info = 5;
				}
			}
			else {
				$info = 6;
			}
		break;

		//smazani obrazku
		case 2:
			if (isset($_GET['id'])) {
				$f = 'graphic/content/' . str_replace('..', '', basename($_GET['id']));
				if (file_exists($f)) {
					if(unlink($f)) {
						$info = 1;
					}
					else {
						$info = 8;
					}
				}
				else {
					$info = 7;
				}
			}
			else {
				$info = 7;
			}
		break;

	}

	Header("Location: /rs/$rub/?info=$info");
	exit;

}

function upload_body() {

clearstatcache();

if (isset($_GET['info'])) {
		switch($_GET['info']){
			case 1:
				echo "<span class='ok'>Ok: Obrázek smazán</span>";
			break;
			case 2:
				echo "<span class='ok'>Ok: Obrázek nahrán a uložen</span>";
			break;
			case 3:
				echo "<span class='error'>Chyba: Nepodařilo se zkopírovat obrázek do nahrávané složky</span>";
			break;
			case 4:
				echo "<span class='error'>Chyba: Nahrávaný soubor není obrázek uložený ve formátu GIF, PNG ani JPG</span>";
			break;
			case 5:
				echo "<span class='error'>Chyba: Nepodařilo se přesunout obrázek do dočasného uložiště</span>";
			break;
			case 6:
				echo "<span class='error'>Chyba: Selhalo nahrávání. Nahrávejte jen soubor do velikosti 300 kB a udělte mu název</span>";
			break;
			case 7:
				echo "<span class='error'>Chyba: Požadovaný obrázek se nepodařilo nalézt</span>";
			break;
			case 8:
				echo "<span class='error'>Chyba: Obrázek se nepodařilo smazat</span>";
			break;
		}
}

if (isset($_GET['action'])) {
	echo "<p><a href='/rs/upload/'>Zpět na výpis souborů</a></p>";
	switch ($_GET['action']){
		case "new":
			echo "<h2>Nahrání nového obrázku</h2>";
			echo "<form action='/rs/upload/?op=1' method='post' enctype='multipart/form-data'>";
				echo "<table width='80%'>";
					echo "<tr><td width='20%'>Název</td><td><input type='name' value='' size='50' name='nazev' /> (bude převedeno na unikátní identifikátor)</td></tr>";
					echo "<tr><td>Soubor (jpg,gif,png) max. 300 kB</td><td><input type='file' name='soubor' /></td></tr>";
					echo "<tr><td colspan='2' align='center'><input type='submit' value='Nahrát' /> <input type='button' value='Zavřít' onClick=\"window.location.href='/rs/upload/'\" /></td></tr>";
				echo "</table>";
			echo "</form>";
		break;
	}
}
else {
	$dir = is_dir("./graphic/content");
	if ($dir) {
		echo "<p>Složka http://s1.aragorn.cz/g/content/</p>";
		echo "<p><a href='/rs/upload/?action=new'>Nahrát nový obrázek</a></p>\n";
		$dir = opendir("./graphic/content");
		if ($dir) {
			echo "<table class='list'>\n";
			echo "<tr><th>Obrázek</th><th>Velikost</th><th>Datum</th><th>Akce</th></tr>\n";

			while (($file = readdir($dir)) !== false) {
				if ($file != '.' && $file != '..') {
					echo "<tr><td><a rel='lightbox[galerka]' href='http://s1.aragorn.cz/g/content/$file'>http://s1.aragorn.cz/g/content/$file</a></td><td>".number_format((filesize("graphic/content/".$file)/1024), 2, ',', ' ')." kB</td><td>".date("Y-m-d H:i:s", filectime("graphic/content/".$file))."</td><td><a href='/rs/upload/?id=$file&amp;op=2' onclick=\"if(confirm('Smazat obrázek?')){return true;}else{return false;}\">smazat</a></td></tr>\n";
				}
			}
			echo "</table>";
		}
	}
}

}
?>