<?php

	if ($LogedIn && !$runBookmarks) {
		$runBookmarksList = true;
		include "./add/bookmarks.php";
?>

<h2 class='h2-head'><a href='<?php echo "/$link/"; ?>' title='Skiny a Záložky'>Skiny &amp; Záložky<?php echo $addZalCount;?></a></h2>

<?php
	if (isset($_GET['ok'])) {
		switch($ok) {
			case "1":
				info("Skin změněn.");
			break;
		}
	}
?>

<div class='highlight-top'></div>
<div class='highlight-mid'>
	<div class='diskuze-okruh' id="skiny">Změnit skin:</div>
	<ul>
		<li>
			<a href="/zalozky/?akce=nastaveni-style&amp;style=gallery" onclick="setStyleSheeter('Gallery');">Galéria et Vellum</a>
		</li>
		<li>
			<a href="/zalozky/?akce=nastaveni-style&amp;style=megadethpod" onclick="setStyleSheeter('Megadeth-PoD');">Megadeth: Prince of Darkness</a>
		</li>
		<li>
			<a href="/zalozky/?akce=nastaveni-style&amp;style=retro" onclick="setStyleSheeter('Retro');">Haterovo retro</a>
		</li>
		<li>
			<a href="/zalozky/?akce=nastaveni-style&amp;style=resizegray" onclick="setStyleSheeter('Resize-Gray');">RE:Size by apophis</a>
		</li>
		<li>
			<a href="/zalozky/?akce=nastaveni-style&amp;style=light" onclick="setStyleSheeter('Light');">Light Side</a>
		</li>
		<li>
			<a href="/zalozky/?akce=nastaveni-style&amp;style=jungletime" onclick="setStyleSheeter('Jungle-Time');">Jungle</a>
		</li>
		<li>
			<a href="/zalozky/?akce=nastaveni-style&amp;style=bluenight" onclick="setStyleSheeter('Blue-Night');">Blue Night</a>
		</li>
	</ul>
</div>
<div class='highlight-bot'></div>

<div class='highlight-top'></div>
<div class='highlight-mid'>
	<div class="zalozky">
		<?php echo $zFInPage;?>
	</div>
</div>
<div class='highlight-bot'></div>

<?php
	}
	else {
?>

<div class='f-top'></div>
<div class='f-middle'>
<form action='<?php echo $_SERVER['REQUEST_URI']; ?>' method='post' class='f' enctype='application/x-www-form-urlencoded'>
	<fieldset>
		<legend class='tglr'>Přihlašovací formulář</legend>
		<input type='hidden' name='log_process' value='1' />
		<label for="frm-zalozky-login">Login: <input id="frm-zalozky-login" class="textfield" type='text' name='login' /></label>
		<label for="frm-zalozky-passw">Heslo: <input id="frm-zalozky-passw" class="textfield" type='password' maxlength='40' name='pass' /></label>
		<input type='submit' class="textfield" value='Přihlásit' id='profile-button' />
	</fieldset>
</form>
</div>
<div class='f-bottom'></div>

<?
	}
?>