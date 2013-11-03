<h2 class='h2-head'><a href='/registrace/' title='Registrace'>Registrace</a></h2>
<h3><a href='/registrace/' title='Registrace'>Registrace</a></h3>
<?php

if(time() < 1314565774 + 8*3600):
echo("<h2 style='color:red;'>Registrace je docasne pozastavena</h2>");
else:
//registrace vracena s chybou
if (isSet($_GET["error"])){

switch ($_GET["error"]){

case 1:
  $error = "Login musí mít minimálně 3 znaky.";
break;

case 2:
  $error = "Login obsahuje zakázané znaky (používejte pouze písmena a číslice).";
break;

case 3:
  $error = "Heslo musí mít minimálně 8 znaků.";
break;

case 4:
  $error = "Kontrola hesla musí mít minimálně 8 znaků.";
break;

case 5:
  $error = "Zadaná hesla se sobě nerovnají, proveďte prosím jejich kontrolu.";
break;

case 6:
  $error = "E-mail musí mít minimálně 9 znaků.";
break;

case 7:
  $error = "Zadaný login je bohužel již obsazen. Zvolte prosím jiný.";
break;

case 8:
  $error = "Zadaný E-mail již bohužel někdo používá. Zvolte prosím jiný.";
break;

case 9:
  $error = "Text pište bez diakritiky, číselné hodnoty číslicí (1, 2, 3, ...)";
break;

case 10:
  $error = "Nastala neznámá chyba při registraci. Prosím, obraťte se e-mailem na <a href='mailto:apophis@aragorn.cz'>apophis&#64;aragorn.cz</a>.";
break;

case 11:
  $error = "Pro aktivaci nového uživatelského účtu musí být použita skutečná emailová adresa.";
break;

case 12:
  $error = "Z tohoto počítače byla v nedávné době provedena registrace.";
break;

}

info($error);
}
?>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/registrace/?akce=reg' name='reg' method='post' class='f' onsubmit='return checkReg()'>
<fieldset>
<legend>Povinné údaje</legend>
<label><span>Login (nick)</span><input type='text' name='login' size='20' maxlength='20' /></label>
<label><span>Heslo</span><input type='password' name='pass' size='20' maxlength='20' /></label>
<label><span>Heslo znovu</span><input type='password' name='pass2' size='20' maxlength='20' /></label>
<label><span>E-mail</span><input type='text' name='mail' size='20' value='' maxlength='30' /></label>
<label><span>&#57; &ndash; &#51; &#61; </span><input type='text' name='rcheck' size='20' value='Sem napiš výsledek' maxlength='20' /></label>
<input class='button' type='submit' value='Registrovat' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
<?php

endif;
?>
<script language='JavaScript' type='text/javascript'>
/* <![CDATA[ */
function checkReg(){
end = 0
ver = new Array("login", "pass", "pass2", "mail")
ver2 = new Array("Login", "Heslo", "Heslo znovu", "E-mail")
ver_count = ver.length

for (i=0;i<ver_count;i++){
if(document.forms["reg"][ver[i]].value.length==0 && end < 1){
alert ("Nebylo vyplněno pole "+ver2[i])
end = 1
return false
}
}

if (!check_mail(document.forms["reg"]["mail"].value)){
alert ("E-mailová adresa je v chybném tvaru")
end = 1
return false
}

if (document.forms["reg"]["login"].value.length < 3){
alert ("Login musí mít alespoň 3 znaky")
return false
}

if (document.forms["reg"]["pass"].value.length < 8){
alert ("Heslo musí mít alespoň 8 znaků");
return false
}

if (!document.forms["reg"]["pass"].value.test(/[a-z]{1,}/) || !document.forms["reg"]["pass"].value.test(/[A-Z]{1,}/) || !document.forms["reg"]["pass"].value.test(/[0-9]{1,}/)){
alert ("Heslo musí mít obsahovat kombinaci malých a velkých písmen a číslic");
return false
}

if (document.forms["reg"]["pass"].value != document.forms["reg"]["pass2"].value){
alert ("Zadaná hesla se neshodují")
return false
}

return true
}
/* ]]> */
</script>
