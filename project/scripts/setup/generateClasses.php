<?php
function generateClass($name)
{
    $nameArray = explode("-", $name);
    $camelCaseName = "";
    $singularName = "";
    foreach ($nameArray as $key => $part) {
        if ($key == 0) {
            $camelCaseName = strtolower($part);
        } else {
            $camelCaseName .= ucfirst($part);
        }
    }

    $singularName = rtrim($camelCaseName, 's');


    $filter = new stdClass;
    $filter->offset = 0;
    $filter->limit = 1;
    $response = eeGetEthosDataModelByFilter($name, $filter);
    $x = 1;
    $d = $response->dataObj[0];
    $properties = "";
    if (is_array($d)) {
        foreach ($d as $objKey => $item) {
            foreach ($item as $itemKey => $value) {
                $properties = "public $itemKey;" . PHP_EOL;
            }
        }
    }
    if (is_object($d)) {
        foreach ($d as $objKey => $item) {
            $properties .= "public " . '$' . "$objKey;" . PHP_EOL;
        }
    }



    $guid = $d->id;
    $class = <<<heredoc
<?php
class $singularName
{
    $properties
   function __construct(dol_guid)
    {
        dol_this->guid = dol_guid;
        dol_this->response = eeGetEthosDataModel("$name",dol_this->guid);
        dol_this->populateProperties();
    }

    protected function populateProperties(){
        if (is_array(dol_this->response->dataObj)) {
            foreach (dol_this->response->dataObj as dol_objKey => dol_item) {
                foreach (dol_item as dol_itemKey => dol_value) {
                    dol_this->dol_itemKey = dol_value;
                    }
            }
        }
        if (is_object(dol_this->response->dataObj)) {
            foreach (dol_this->response->dataObj as dol_key => dol_item) {
                            dol_this->dol_key = dol_item;

            }
        }

    }
}
heredoc;
    $class = str_replace("dol_", "$", $class);
    file_put_contents("/project/ethosClasses/$singularName.php", $class);
}

require_once("helperFunctions.php");
$names = "academic-periods,persons,student-academic-programs,student-advisor-relationships";

foreach (explode(",",$names) as $key => $name) {
    generateClass($name);
}
print "All Classes Generated";