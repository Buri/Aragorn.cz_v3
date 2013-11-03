<?php

switch ($_POST['subject']) {
  case "1":
  case "2":
  case "3":
  case "4":
    mysql_query ("DELETE FROM 3_visited_$_POST[subject] WHERE uid = $_SESSION[uid]");
  break;

  case "5":
    mysql_query ("DELETE FROM 3_visited_1 WHERE uid = $_SESSION[uid]");
    mysql_query ("DELETE FROM 3_visited_2 WHERE uid = $_SESSION[uid]");
    mysql_query ("DELETE FROM 3_visited_3 WHERE uid = $_SESSION[uid]");
    mysql_query ("DELETE FROM 3_visited_4 WHERE uid = $_SESSION[uid]");
  break;
}

Header ("Location:$inc/nastaveni/systemove/?ok=7");
exit;
?>
