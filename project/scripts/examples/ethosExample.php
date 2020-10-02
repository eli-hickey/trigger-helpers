<?php
require_once "similarToEthosFunctions.php";
require_once "helperFunctions.php";
require_once "workflowFunctions.php";
// place any code prior to the triggerStart comment
// for example if the user would enter the dept code you can set the case variable here
$atat_departmentCode = "xyz";
//triggerStart

    // Currently there are three 'similar to ellucian workflow' ethos functions.
    // This example excercises these functions and requires /employment-departments in data access and the api


    //Get the first page of records.  Page size is defined by each data model and is also returned in the response object
        $filter = ""; // an empty filter get's the first page of records
        $r_employmentDepartments = eeGetEthosDataModelByFilter("employment-departments",$filter);

    //Use a filter to get first department by its code
        $filter = new stdClass;
        $filter->criteria = new stdClass;
        $filter->criteria->code = $r_employmentDepartments->dataObj[0]->code;
        $r_employmentDepartmentsFiltered = eeGetEthosDataModelByFilter("employment-departments",$filter);

    //Use a guid to get the the first department
        $departmentGuid = $r_employmentDepartments->dataObj[0]->id;
        $r_employmentDepartments = eeGetEthosDataModel("employment-departments",$departmentGuid);

    // get departments with graphQl and data access
        $graphQuery = <<<heredoc
        {employmentDepartments12
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

    // use case variables
        $atat_someCaseVar = "foo";
        $ateq_someCaseVarGrid = "foo";
//triggerEnd
    // use this section to include extra code that is useful for intial development
    // but should not be included in trigger code

print "end";