<?php
require_once "helperFunctions.php";

// place any code prior to the triggerStart comment
// for example if the user would enter the dept code you can set the case variable here
$atat_departmentCode = "xyz";
$atat_userName = "emcque";
//triggerStart

    // Currently there are three 'similar to ellucian workflow' ethos functions.
    // This example excercises these functions and requires /academic-periods in data access and the api


    //Get the first page of records.  Page size is defined by each data model and is also returned in the response object
        $filter = ""; // an empty filter get's the first page of records
        $r_academicPeriods = eeGetEthosDataModelByFilter("academic-periods",$filter);

    //Use a filter to get first department by its code
        $filter = new stdClass;
        $filter->criteria = new stdClass;
        $filter->criteria->code = $r_academicPeriods->dataObj[0]->code;
        $r_academicPeriodsFiltered = eeGetEthosDataModelByFilter("academic-periods",$filter);

    //Use a guid to get the the first department
        $departmentGuid = $r_academicPeriods->dataObj[0]->id;
        $r_academicPeriod = eeGetEthosDataModel("academic-periods",$departmentGuid);

    // get departments with graphQl and data access
        $graphQuery = <<<heredoc
        {academicPeriods12
            ( limit:25
              sort:{id:DESC}
            )
            {
              edges
              {
                node
                {
                  id
                }
              }
            }
          }
        heredoc;
        $graphResponse = eeGetEthosDataByGraphQLQuery($graphQuery);

    // Use erpUser class

    $student = new erpUser($atat_APPLICATION,"bannerUserName",$atat_userName,"student");

    // update case variables
        $atat_someCaseVar = "example";
        $ateq_someCaseVarGrid = "example";
//triggerEnd
    // use this section to include extra code that is useful for intial development
    // but should not be included in trigger code

print "end";