<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/includes/include.php");

$calories = new Calories();
$calories->processPost();
$calorieTable = $calories->createCalorieTableHTML();
$dateFromValue = (isset($calories->tableFilter["dateFrom"]) ? $calories->tableFilter["dateFrom"] : "");
$dateToValue = (isset($calories->tableFilter["dateTo"]) ? $calories->tableFilter["dateTo"] : "");
$dataItemSelectField = $calories->createDataItemSelectField();

require_once(ROOTDIR."/views/header.html");
require_once(ROOTDIR."/views/calorie_table.html");
require_once(ROOTDIR."/views/footer.html");
?>