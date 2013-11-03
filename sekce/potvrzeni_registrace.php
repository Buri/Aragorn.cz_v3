<?php

if(time() < 1314565774 + 8*3600)
die("<h2 style='color:red;'>Registrace je docasne pozastavena</h2>");


if (!isSet($_GET['reg_code'])){
$report = "Chybí registrační kód. Potrzovací proces <strong class='warning'>nemohl být dokončen</strong>. <br />V případě jakýchkoli problémů se prosím obraťte na někoho z <a href='/admins/' class='permalink' title='Administrátoři Aragorn.cz'>administrátorů</a>.";
}else{

	$reg_code = intval($_GET['reg_code']);

  //overeni mailem zaslaneho kodu
  $sel_potvrzeni = mysql_query ("SELECT id, login FROM 3_users WHERE reg_code = '$reg_code'");
  $out_potvrzeni = mysql_fetch_object($sel_potvrzeni);

  $cP = $out_potvrzeni->id;
  $user = $out_potvrzeni->login;

  //pokud kod odpovida, user se jiz muze prihlasit
  if ($reg_code > 0 && $cP > 0){
    mysql_query("UPDATE 3_users SET reg_code = 0 WHERE id = $cP");
    $text = 'Vítám Tě na serveru Aragorn.cz!

Zaregistroval(a) ses a povrdil(a) registraci na&nbsp;komunitní portál, jenž nabízí mnoho variant vyžití - a některé z nich - ty hlavní, Ti nyní v&nbsp;tomto krátkém textu vysvětlím.

Herna, diskuzní fóra, chat, galerie, články, srazy, hraní her na hrdiny naživo (LARP) - to a mnohem víc máš možnost nalézt na stránkách Aragornu.
O základech toho, jak fungují jednotlivé sekce, se můžeš dozvědět <a href="/napoveda/">v&nbsp;Nápovědě</a>. Doporučuji Ti, aby sis ji alespoň jednou pročetl(a), rozhodně to zatím nikomu neuškodilo, spíše naopak.

Já jsem Systém. Nemůžeš mi sice psát, ale <a href="/admins/">ochotných lidí</a>, kterým můžeš poslat poštolku, je docela dost. Nemusíš se bát, nekoušou (většinou).
Mimojiné mám funkce informátora všeholidu, pošťáka pro&nbsp;osobní <a href="/posta/">poštolku</a>, jsem náhodou při&nbsp;hodech kostkou v Herně i vyhazovačem dlouho neaktivních <a href="/uzivatele/">uživatelů</a>. Ale dost o mně, tento text je tu pro Tebe, aby ses dozvěděl(a) něco do&nbsp;začátku.

Tedy... přeji Ti příjemnou zábavu.';

    sysPost($cP,$text);
    $report = "Registrační kód pro uživatele <strong>$user</strong> byl úspěšně přijat. <br /> Nyní se můžete poprvé přihlásit do systému. <br /> Namísto přihlašovacího formuláře bude zobrazen odkaz na&nbsp;vlastní profil, poštu a odhlášení&nbsp;se. <br /><br />Příjemnou zábavu přejí <a href='/admins/' class='permalink' title='Administrátoři Aragorn.cz'>administrátoři</a> serveru <acronym title='Online herna RPG (Drd, Vampire a jiné)' xml:lang='cs'>Aragorn.cz</acronym>.";
  }else{
    $report = "Registrační kód <strong class='warning'>nebyl přijat</strong>. <br /> Zřejmě se jedná o neplatný kód anebo již byl potvrzen. V případě jakýchkoli problémů se prosím obraťte na&nbsp;někoho z&nbsp;<a href='/admins/' class='permalink' title='Administrátoři Aragorn.cz'>administrátorů</a>, převážně však na e-mail apophis@aragorn.cz.";
  }
}
?>
<h2 class='h2-head'><a href='/potvrzeni-registrace/' title='Registrace'>Registrace</a></h2>
<h3><a href='/potvrzeni-registrace/' title='Potvrzení registrace'>Potvrzení registrace</a></h3>

<div class='art'>
<p>
<?php
echo $report;
?>
</p>
</div>
