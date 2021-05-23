<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/includes/include.php");

$calories = new Calories();
$calorieTable = $calories->createCalorieTableHTML();
require_once(ROOTDIR."/views/header.html");
require_once(ROOTDIR."/views/calorie_table.html");
require_once(ROOTDIR."/views/footer.html");
?>