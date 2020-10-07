Contributions are welcome and encouraged!  If you think you can enhance the developers experience with this tool or have a cool example please contribute!  
Please see the [To Do project](https://github.com/eli-hickey/trigger-helpers/projects/1) for ideas and propose your own.  Feel free to improve to this file or the reademe.md files.  

Guidelines for a good example
1. Examples should be added under /project/scripts/examples
2. File names should descriptive and in the format of verbNoun.php
3. All variable names should be camelCase.
4. Examples should be functional with minimal adjustments.  
5. If your example requires a value provided by the user on a dynaform or another trigger please set it prior to the //triggerStart.  

An example Example:
filename translateAcademicPeriodCode.php
```
<?php
 require_once 'helperFunctions.php';
//This trigger requires the termCode case variable to have a term code allready set typically on a dynaform 
// and will add case variables for the term guid and title
// Input
    $atat_termCode = "202020";
// Output
//  case variables termGuid and termTitle
//triggerStart
    $filter = new stdClass;
    $filter->criteria = new stdClass;
    $filter->criteria->code = $atat_termCode;
    $response = eeGetEthosDataModelByFilter('academic-periods',$filter);
    $atat_termGuid = $response->dataObj[0]->id;
    $atat_termTitle = $response->dataObj[0]->title;
 
//triggerEnd
    print 'end of script';


```