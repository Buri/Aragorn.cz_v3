<?php
	if (isset($_GET['style'])) {
		switch($_GET['style']){
			case "megadeth-pod":case "Megadeth-PoD":case "megadethPod":case "megadethpod":
				$cookieStyle = "megadethpod";
			break;
			case "retro":case "Retro":
				$cookieStyle = "retro";
			break;
			case "resize":case "Resize-Gray":case "resizeGray":case "resizegray":
				$cookieStyle = "resizegray";
			break;
			case "jungle":case "Jungle-Time":case "jungleTime":case "jungletime":
				$cookieStyle = "jungletime";
			break;
			case "light":case "Light":
				$cookieStyle = "light";
			break;
			case "blueNight":case "Blue-Night":case "blue-night":case "bluenight":
				$cookieStyle = "bluenight";
			break;
			case "gallery":case "Gallery":
			default:
				$cookieStyle = "gallery";
			break;
		}
	}
	else {
		$cookieStyle = "gallery";
	}

	setcookie("style", $cookieStyle, ($time + (180 * 24 * 3600)), "/", $inc);
	$_COOKIE['style'] = $cookieStyle;
	ob_flush();

	header("Location: $inc/zalozky/?ok=1");
	exit;

?>