<?php
file_put_contents("/project/ethosClasses/ethosDataModels.php", "<?php");
require_once "helperFunctions.php";
$availableResources = ethosGetAvailableResources();
$validForGet = array();
foreach ($availableResources->dataObj as $appkey => $authoritativeApp) {
    $appNames = array("IntegrationApi", "StudentApi");
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
$response = '$response';
$ethosOptions = implode(",",$validForGet);
$snippet = <<<'nowdoc'
{
	"eeGetEthosDataModelByFilter":{
		"scope": "php",
		"prefix":["$$response = ee","Ethos","eeGet","ethos","filter"],
		"body":["$$response = eeGetEthosDataModelByFilter('${1| ,swapWithEthosOptions|}',${2:$$filter});"],
		"description": "Get Ethos Data by Filter"
		
	}
}
nowdoc;

file_put_contents("/project/.vscode/generatedEthosSnippets.code-snippets",str_replace("swapWithEthosOptions",$ethosOptions,$snippet));

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


