<?php
require_once "ethosFunctions.php";
require_once "helperFunctions.php";
require_once "workflowFunctions.php";
//triggerStart


$response = eeGetEthosDataModelByFilter("buildings","");
print json_encode($response,JSON_PRETTY_PRINT);
$atat_someCaseVar = "foo";
$ateq_someCaseVarGrid = "foo";

//triggerEnd
print "end";