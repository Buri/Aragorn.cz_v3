<?php

//existuje jiz takova zalozka?
$cB = mysql_fetch_row( mysql_query ("SELECT bookmark FROM 3_visited_$sid WHERE uid = '$_SESSION[uid]' AND aid = '$id'") );
//limit zalozek
$cL1 = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_visited_1 WHERE uid = '$_SESSION[uid]' AND bookmark = '1'"));
$cL2 = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_visited_2 WHERE uid = '$_SESSION[uid]' AND bookmark = '1'"));
$cL3 = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_visited_3 WHERE uid = '$_SESSION[uid]' AND bookmark = '1'"));
$cL4 = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_visited_4 WHERE uid = '$_SESSION[uid]' AND bookmark = '1'"));

$cL = (int)$cL1[0] + (int)$cL2[0] + (int)$cL3[0] + (int)$cL4[0];

$cL = (int)$cL;

if($cL >= $zalozkyOmezeniCount && $_SESSION['lvl'] < 2){
  $error = 16;
}elseif ($cB[1] < 1){
  mysql_query ("UPDATE 3_visited_$sid SET bookmark = '1' WHERE uid = '$_SESSION[uid]' AND aid = '$id'");
  $ok = 15;
}else{
  $error = 15;
}


if (isSet($ok)){
Header ("Location:$inc/$link/$slink/?ok=$ok");
}else{
Header ("Location:$inc/$link/$slink/?error=$error");
}

exit;
?>
