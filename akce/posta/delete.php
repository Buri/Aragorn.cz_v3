<?php
//vymaz vybranych zprav u posty
$ids = addslashes($_GET['ids']);

mysql_query ("DELETE FROM 3_post WHERE id IN($ids) AND oid = $_SESSION[uid]");

Header ("Location:$inc/$link/?ok=2");
exit;
?>
