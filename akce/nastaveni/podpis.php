<?php
//zmena podpisu u diskuzi

$sign = trim(_htmlspec($_POST['sign']));

  mysql_query ("UPDATE 3_users SET signature = '$sign' WHERE id = $_SESSION[uid]");

//uspesny redirect
    Header ("Location:$inc/nastaveni/systemove/?ok=5");
exit;
?>
