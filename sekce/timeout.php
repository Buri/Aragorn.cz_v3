<h2 class='h2-head'><a href='/timeout/' title='Bezpečnostní odhlášení'>Bezpečnostní odhlášení</a></h2>
<h3><a href='/timeout/' title='Bezpečnostní odhlášení'>Bezpečnostní odhlášení</a></h3>
<div class='art'>
<p>
Z důvodu <acronym title='Byl(a) jste déle jak 30 min. neaktivní' xml:lang='cs'>neaktivity</acronym> Vás Aragorn.cz odhlásil ze systému.
</p>
<?php
if (isset($_SESSION['saved_array'])){
  $saved = $_SESSION['saved_array'];
	if (count($saved)>1) {
	  if ($saved["link"] == "herna") {
	    if (isset($saved["mess"])) {
			  echo "<p>";
		  	echo "Váš rozepsaný text...";
			  echo "</p>";
		  	echo "<p class='art-bord'>";
  			echo spit($saved['mess'], 1);
	  		echo "</p>";
			}
			else {
			  echo "<p>";
		  	echo "Váše odeslané texty...";
			  echo "</p>";
		  	echo "<p class='art-bord'>";
		  	unset($saved["mess"]);
		  	unset($saved["link"]);
  			echo spit(join(" \n ",$saved), 1);
	  		echo "</p>";
			}
		}
		elseif (isset($saved['mess'])) {
		  echo "<p>";
	  	echo "Váš rozepsaný text...";
		  echo "</p>";
		  echo "<p class='art-bord'>";
  		echo spit($saved['mess'], 1);
	  	echo "</p>";
		}

	  echo "<p>";
  	echo "Pokračovat na <a href='/' class='permalink' title='Úvodní stránka'>Úvodní stránku</a>";
  	echo "</p>";
	}
}
?>
</div>
