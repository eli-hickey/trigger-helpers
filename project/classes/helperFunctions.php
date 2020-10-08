<?php
require_once "/project/vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createMutable("/project/scripts/setup");
$dotenv->load();
$dotenv->required(['API_KEY','ETHOS_REGION']);


require_once "similarToEthosFunctions.php";
require_once "Ellucian PS Functions and Classes V5 with Graph.php";
require_once "similarToProcessMakerFunctions.php";


define("DB_HOST","");
define("DB_USER","");
define("DB_PASS","");
define("DB_NAME","");
define("PATH_DOCUMENT","");
define("PATH_SEP","");
define("Cases","");


$GLOBALS["atat_SYS_LANG"]="en";
$GLOBALS["atat_SYS_SKIN"]="ellucianux";
$GLOBALS["atat_SYS_SYS"]= "triggerHelper"; 
$GLOBALS["atat_PROCESS"]= "";
$GLOBALS["atat_TASK"]= "";
$GLOBALS["atat_APPLICATION"]= "";
$GLOBALS["atat_APP_NUMBER"]= "";
$GLOBALS["atat_USER_LOGGED"]= "";
$GLOBALS["atat_USR_USERNAME"]= "";
$GLOBALS["atat_INDEX"]= "";
$GLOBALS["atat_PIN"]= "";
$GLOBALS["atat___ERROR__"]= "";




function GenerateEwfFiles($path, $generatorFileName)
{
    $dir = new DirectoryIterator($path);
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            $fn = $fileinfo->getFilename();
            $ext = $fileinfo->getExtension();
            if ($ext == "php" && $fn != $generatorFileName) {
                generateThisFile($fn, dirname($fn), $path);
            }
        }
    }
    print "All files Generated to: " . $path;
}
function generateThisFile($fn, $dn, $path)
{
    $matches = array();
    $phpCode = file_get_contents($fn);
    preg_match("~(?<=//triggerStart)(.+?)(?=//triggerEnd)~s", $phpCode, $matches,);
    $phpCode = $matches[0] ?? "";
    $findArray = array('$atat_', '$ateq_', '$atpt_', '$ateq_');
    $replaceArray = array("@@", "@=", "@%", "@#");
    $phpCode = str_replace($findArray, $replaceArray, trim($phpCode));
    $saveFileName = str_replace(".php", ".ewf", $fn);
    $path = $path . "/Trigger-Out/";
    if (!is_dir($path)) {
        mkdir($path);
    }
    $saveFileName = $path . $saveFileName;
    file_put_contents($saveFileName, $phpCode);
}
