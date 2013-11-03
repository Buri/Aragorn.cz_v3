<?php

require_once './nette.min.php';
require_once './db/credentials.php';

$db = new Nette\Database\Connection("mysql:host=$se1;dbname=$db1", $us1, $pa1);

$email = $_GET['email'];

$password = \Nette\Utils\Strings::random(10);
$row = $db->table('3_users')->where(['mail' => $email])->fetch();
if(!$row)
    die('Uživatel nenalezen. <a href="javascript:history.back()">Zpět</a>.');
$row->update(['pass' => md5($password)]);

$mail = new \Nette\Mail\Message;
$mail->setFrom('aragorn.cz <system@aragorn.cz>')
        ->addTo($email)
        ->setSubject('Reset hesla')
        ->setBody("Vaše nové heslo je: $password");
$mail->send();

?>
Vaše heslo bylo resetováno. <a href="/">Pokračovat na hlavní stranu</a>.