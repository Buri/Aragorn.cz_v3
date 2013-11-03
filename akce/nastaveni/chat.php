<?php
// zmena os. nastaveni

$color = _htmlspec(trim($_POST['color']));

$ref = addslashes($_POST['ref']);
if ($_POST['ref'] > 60 || $_POST['ref'] < 10){
  $ref = 15;
}

$AragornCache->delVal("users-id2color-$_SESSION[uid]");

$size = addslashes($_POST['size']);
if ($_POST['size'] > 20 || $_POST['size'] < 10){
  $size = 12;
}

if ($_POST['order'] == "desc") {
  $order = "desc";
}
else {
  $order = "asc";
}

if ($_POST['v_time'] == "1") {
  $v_time = 1;
}
else {
  $v_time = 0;
}

if ($_POST['sys_roz'] == "0" && $_SESSION['lvl'] >= 2) $sys_warn_roz = 0;
else $sys_warn_roz = 1;

if ($_POST['sys_other'] == "0" && $_SESSION['lvl'] >= 2) $sys_warn_other = 0;
else $sys_warn_other = 1;

if ($_POST['sys_ajax'] == "0" && $_SESSION['lvl'] >= 2) $sys_warn_ajax = 0;
else $sys_warn_ajax = 1;

if ($_POST['sys'] == "1") {
  $sys = 1;
}
else {
  $sys = 0;
}
  mysql_query ("UPDATE 3_users SET chat_warn_ajax = '$sys_warn_ajax', chat_warn_roz = '$sys_warn_roz', chat_warn_other = '$sys_warn_other', chat_color = '$color', chat_ref = '$ref', chat_font = '$size', chat_order = '$order', chat_time = '$v_time', chat_sys = '$sys' WHERE id = $_SESSION[uid]");

//uspesny redirect
    Header ("Location:$inc/nastaveni/chat/?ok=6");
exit;
?>
