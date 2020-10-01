<?php
require_once "ethosFunctions.php";
require_once "helperFunctions.php";
require_once "workflowFunctions.php";
//triggerStart


$response = eeGetEthosDataModelByFilter("buildings","");
$response = eeGetEthosDataModel("buildings","2ff7efdd-5ff4-4d90-80c1-407c2d2a2d96");
print json_encode($response,JSON_PRETTY_PRINT);
$atat_someCaseVar = "foo";
$ateq_someCaseVarGrid = "foo";

//triggerEnd
print "end";