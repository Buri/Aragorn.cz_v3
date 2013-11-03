<?php
session_start();
require "../db/conn.php";

$rid = addslashes($_GET['id']);
$PopisA = mysql_query ("SELECT popis FROM 3_chat_rooms WHERE id = '$rid' AND type = '1'");
if (mysql_num_rows($PopisA)>0){
	$popis = mysql_fetch_object($PopisA);		

	$sitS = mysql_query ("SELECT nazev,popis FROM 3_roz_situace WHERE nazev = '$popis->popis' AND nadrazena = '0'");
	if (mysql_num_rows($sitS)>0){
		$sit = mysql_fetch_object($sitS);
		$text = "<span class='vypravec'>".$sit->nazev."</span><br /><span style='color:white'>".$sit->popis."</span>";
	}
	else{
		$text = "Momentálně není aktivní žádná situace";
	}
}	
?>
<html>
<head>
<meta http-equiv='Content-language' content='cs' />
<meta http-equiv='Content-Type' content='text/xhtml; charset=utf-8' />
<meta http-equiv='pragma' content='no-cache' />
<meta name='description' content='Aragorn.cz, chat' />
<title>Arachat - Situace</title>
<link rel="stylesheet" type="text/css" href="./style/chat.css" />
</head>
<body>


<div style="margin:10px; font-size:12px;">
<?php
echo($text)

?>

</div>

</body>
</html>
