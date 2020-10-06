<?php
file_put_contents("/project/ethosClasses/ethosDataModels.php", "<?php");
require_once "helperFunctions.php";
$availableResources = ethosGetAvailableResources();
foreach ($availableResources->dataObj as $appkey => $authoritativeApp) {
    $appNames = array("IntegrationApi", "studentApi");
    if (!empty($authoritativeApp->about[0]->applicationName) && in_array($authoritativeApp->about[0]->applicationName, $appNames)) {
        foreach ($authoritativeApp->resources as $reskey => $resource) {
            $name = getNameVariants($resource->name);
            $properties[$name->kebabCase] = "public $" . $name->camelCase . "='$name->kebabCase';";
            foreach ($resource->representations as $repkey => $representation) {
                if (!empty($representation->version)) {
                    $nameV = getNameVariants($resource->name . $representation->version);
                    if (!empty($representation->methods)) {
                        foreach ($representation->methods as $key => $method) {
                            $properties[$nameV->kebabCase] = "public $" . $method . "_" . $nameV->camelCase . "='" . $representation->{"X-Media-Type"} . "';";
                            switch ($method) {
                                case 'get':
                                    $validForGet[$name->camelCase] = $name->kebabCase;
                                    break;
                                
                                default:
                                    # code...
                                    break;
                            }
                        }

                    }
                }
                $x = 1;
            }
        }
    }
}

$class = "<?php " . PHP_EOL . " class ethosDataModels{" . PHP_EOL;
$class .= implode(PHP_EOL, $properties) . PHP_EOL . "}";

file_put_contents("/project/ethosClasses/ethosDataModels.php", $class);

function getNameVariants($name)
{
    $nameVariants = new stdClass;
    $nameVariants->original = $name;
    $nameVariants->kebabCase = str_replace(["_", "."], ["_", "_"], $name);
    $nameVariants->camelCase = "";
    $nameVariants->singularCamelCase = "";
    $nameArray = explode("-", $nameVariants->kebabCase);
    foreach ($nameArray as $key => $part) {
        if ($key == 0) {
            $nameVariants->camelCase = strtolower($part);
        } else {
            $nameVariants->camelCase .= ucfirst($part);
        }
    }

    $nameVariants->singularCamelCase = rtrim($nameVariants->camelCase, 's');
    return $nameVariants;
}

print "end";

// class myethosDataModels
//  {
//      var $studentAcademicPeriods = "student-Academic-periods";
//      var $persons = "sersons";
//      var $students = "students";
//  }
