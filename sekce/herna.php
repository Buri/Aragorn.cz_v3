<?php
$titleHerna = $itIsApril ? 'Xbox / PS3' : 'Herna';

if ($hFound && $hInc != "") {
	include ($hInc.".php");
}
elseif ($hFound == true) {
	include ("herna_one.php");
}
elseif($slink == "my") {
	include ("herna_my.php");
}
elseif($slink == "new") {
	include ("herna_new.php");
}
else {
	include ("herna_vypis.php");
}

?>
