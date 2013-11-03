<?php
// zmena os. nastaveni

$name = trim(_htmlspec($_POST['name']));
$city = trim(_htmlspec($_POST['city']));
$icq = trim(_htmlspec($_POST['icq']));
$about_me = trim(_htmlspec($_POST['about_me']));

  mysql_query("UPDATE 3_users SET name = '$name', city = '$city', icq = '$icq' WHERE id = $_SESSION[uid]");
  if (strlen($about_me)>0) {
	  $hasAbout = mysql_query("SELECT uid FROM 3_users_about WHERE uid = $_SESSION[uid]");
	  if (mysql_num_rows($hasAbout)>0) {
		  mysql_query("UPDATE 3_users_about SET about_me = '$about_me' WHERE uid = $_SESSION[uid]");
		}
		else {
		  mysql_query("INSERT INTO 3_users_about (uid, about_me) VALUES ($_SESSION[uid], '$about_me')");
		}
	}
	else {
		mysql_query("DELETE FROM 3_users_about WHERE uid = $_SESSION[uid]");
	}

//uspesny redirect
    Header ("Location:$inc/nastaveni/osobni/?ok=4");
exit;
?>
