Contributions are welcome and encouraged!  If you think you can enhance the developers experience with this tool or have a cool example please contribute!  
Please see the TO DO project tab for ideas or propose your own.  Feel free to propose improvements to this file or the reademe.md files.  

Guidlines for a good example
1. all variable names follow the camelCase.
2. Examples should be functional with minimal adjustments.  
3. If your example requires a value provided by the user or another routine please set it prior to the //triggerStart.  

An example Example:
```
<?php
 require_once 'helperFunctions.php';
//This trigger requires the termCode case variable to have a term code allready set typically on a dynaform 
// and will add case variables for the term guid and title
// Input
 $atat_termCode = "202020";
// Output
// case variables termGuid and termTitle
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