<?php

$module = new Module("streamrider","1.0","http://www.minitiddlyserver.com/viewtopic.php?f=1&t=135");

$module->addEventPHP("MainSaveEvent","SaveBackup.php");
$module->addScript("SaveBackup.js");


?>
