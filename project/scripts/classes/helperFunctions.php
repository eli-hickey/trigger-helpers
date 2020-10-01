<?php

function GenerateEwfFiles($path, $generatorFileName) {
    $dir = new DirectoryIterator($path);
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            $fn = $fileinfo->getFilename();
            $ext = $fileinfo->getExtension();
            if ($ext == "php" && $fn != $generatorFileName) {
                generateThisFile($fn,dirname($fn),$path);
            }
        }
    }
print "All files Generated to: ". $path;
}
function generateThisFile($fn,$dn,$path){
    $matches = array();
    $phpCode = file_get_contents($fn);
    preg_match("~(?<=//triggerStart)(.+?)(?=//triggerEnd)~s",$phpCode,$matches,);
    $phpCode = $matches[0] ?? "";
    $findArray = array('$atat_','$ateq_','$atpt_','$ateq_');
    $replaceArray = array("@@","@=","@%","@#");
    $phpCode = str_replace($findArray,$replaceArray,trim($phpCode));
    $saveFileName = str_replace(".php",".ewf",$fn);
    $path = $path."/Trigger-Out/";
    if (!is_dir($path)) {
        mkdir($path);
    }
    $saveFileName = $path.$saveFileName;
    file_put_contents($saveFileName,$phpCode);

}