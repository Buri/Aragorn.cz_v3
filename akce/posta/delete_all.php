<?php
//vymaz vybranych zprav u posty

mysql_query ("DELETE FROM 3_post WHERE oid = $_SESSION[uid]");

Header ("Location:$inc/$link/?ok=3");
exit;
?>
