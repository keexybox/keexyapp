<?php
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	echo "<b>".$_SERVER['HTTP_HOST']."</b> blocked by <a href='http://".$_SERVER['SERVER_ADDR'].":8001'>KeexyBox</a>."
?>
