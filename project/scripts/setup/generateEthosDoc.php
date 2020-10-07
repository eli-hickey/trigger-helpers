<?php
require_once "helperFunctions.php";
$availableResources = ethosGetAvailableResources();
$validForGet = array();


foreach ($availableResources->dataObj as $appkey => $authoritativeApp) {
    $appNames = array("IntegrationApi", "StudentApi", "Colleague Web Api");
    switch (1) {
        case !empty($authoritativeApp->about[0]->name):
            $name = $authoritativeApp->about[0]->name;
            break;
        case !empty($authoritativeApp->about[0]->applicationName):
            $name = $authoritativeApp->about[0]->applicationName;
            break;
        default:
            $name = "";
            break;
    }
    if (!empty($name) && in_array($name, $appNames)) {
        $fileName = "raw_".str_replace(" ","_",$authoritativeApp->name).".json";
        file_put_contents("/project/ethosDoc/$fileName",json_encode($authoritativeApp,JSON_PRETTY_PRINT));
        foreach ($authoritativeApp->resources as $reskey => $resource) {
            $name = getNameVariants($resource->name);
            $properties[$name->kebabCase] = "public $" . $name->camelCase . "='$name->kebabCase';";
            foreach ($resource->representations as $repkey => $representation) {
                $filters = "";
                if (!empty($representation->filters)) {
                    $filters = implode(", ",$representation->filters);
                }
                if (!empty($representation->version)) {
                    $nameV = getNameVariants($resource->name . $representation->version);
                    if (!empty($representation->methods)) {
                        foreach ($representation->methods as $key => $method) {
                            $properties[$nameV->kebabCase] = "public $" . $method . "_" . $nameV->camelCase . "='" . $representation->{"X-Media-Type"} . "';";
                            $output[$method.$nameV->kebabCase] = array("name"=>$name->kebabCase,"method"=>$method,"version"=>$representation->version,"xMediaType"=>$representation->{"X-Media-Type"},"filters"=>$filters);
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
$csvHeaders = "resourceName,Method,Version,ContentType,filters";
$file = fopen("/project/ethosDoc/availableResources.csv","w");
$i = 0;
foreach ($output as $key => $line) {
    $i++;
    if ($i == 1) {
        fputcsv($file,explode(",",$csvHeaders));
        fputcsv($file,$line);
    } else {
        fputcsv($file,$line);
    }
}
fclose($file);
file_put_contents("/project/ethosDoc/availableResources.json",json_encode($output,JSON_PRETTY_PRINT));

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


