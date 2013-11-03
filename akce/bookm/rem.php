<?php

//existuje takova zalozka?
$cB = mysql_fetch_row( mysql_query ("SELECT bookmark FROM 3_visited_$sid WHERE uid = $_SESSION[uid] AND aid = $id AND bookmark = '1'") );

if ($cB[0] > 0){
  mysql_query ("UPDATE 3_visited_$sid SET bookmark = '0' WHERE uid = $_SESSION[uid] AND aid = $id");
  $ok = 16;
}else{
  $error = 17;
}


if (isSet($ok)){
Header ("Location:$inc/$link/$slink/?ok=$ok");
}else{
Header ("Location:$inc/$link/$slink/?error=$error");
}

exit;
?>
