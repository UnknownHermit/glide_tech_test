<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/includes/include.php");

$calories = new Calories();
$calories->wipeData();
$calories->importData();
require_once(ROOTDIR."/views/header.html");
require_once(ROOTDIR."/views/import_view.html");
require_once(ROOTDIR."/views/footer.html");
?>