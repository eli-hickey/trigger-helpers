<?php
require_once "./classes/ethosFunctions.php";
require_once "./classes/helperFunctions.php";
require_once "./classes/workflowFunctions.php";
//Start Trigger Code (Generator Instruction do not remove) &*^EWFSTART:


$response = eeGetEthosDataModelByFilter("buildings","");
print json_encode($response,JSON_PRETTY_PRINT);
$atat_someCaseVar = "foo";
$ateq_someCaseVarGrid = "foo";

//:&*^EWFEND End Trigger Code (Generator Instruction do not remove)
print "end";