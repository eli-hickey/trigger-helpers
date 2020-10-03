<?php
require_once "similarToEthosFunctions.php";
require_once "Ellucian PS Functions and Classes V5 with Graph.php";
define("DB_HOST","");
define("DB_USER","");
define("DB_PASS","");
define("DB_NAME","");
define("PATH_DOCUMENT","");
define("PATH_SEP","");
define("Cases","");

$atat_SYS_LANG="en";
$atat_SYS_SKIN="ellucianux";
$atat_SYS_SYS = "triggerHelper";
$atat_PROCESS = "";
$atat_TASK = "";
$atat_APPLICATION = "";
$atat_APP_NUMBER = "";
$atat_USER_LOGGED = "";
$atat_USR_USERNAME = "";
$atat_INDEX = "";
$atat_PIN = "";
$atat___ERROR__ = "";




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
