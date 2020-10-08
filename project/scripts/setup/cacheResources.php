<?php
 require_once 'helperFunctions.php';
 require_once 'Cache Ethos Resources In Container.php';

$resources = array(
    "academic-periods",
    "academic-programs",
    "academic-disciplines");
cacheResources($resources);
print 'end of script';