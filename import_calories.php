<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/includes/include.php");

$calories = new Calories();
$calories->wipeData();
$calories->importData();

?>