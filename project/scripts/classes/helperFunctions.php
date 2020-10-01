<?php

function GenerateEwfFiles($path) {
    $dir = new DirectoryIterator($path);
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            $fn = $fileinfo->getFilename();
            if (substr($fn,-4) == ".php" and substr($fn,0,9) != "Generator") {
                generateThisFile($fn,dirname($fn),$path);
            }
        }
    }
print "All files Generated to: ". $path;
}
function generateThisFile($fn,$dn,$path){
    $phpCode = file_get_contents($fn);
    $start = strpos($phpCode,("&*^EWFSTART:".PHP_EOL))+14;
    $end   = strpos($phpCode,":&*^EWFEND")-2;
    if($start != 14 and $end != -2) {
        $phpCode = substr($phpCode,$start,($end-$start));
        $phpCode = str_replace("\$atat_","@@",$phpCode);
        $phpCode = str_replace("\$ateq_","@=",$phpCode);
        $phpCode = str_replace("\$atpt_","@%",$phpCode);
        $saveFileName = str_replace(".php",".ewf",$fn);
        $path = $path."\\Trigger-Out\\";
        if (!is_dir($path)) {
            mkdir($path);
        }
        $saveFileName = $path.$saveFileName;
        file_put_contents($saveFileName,$phpCode);
    }
}