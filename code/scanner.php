<?php

include_once "config.php";
include_once "utils.php";
include_once "ui.php";
include_once "console.php";

function ScannerDetect(){
	global $ScanImage;
	$Command=$ScanImage." -f\"%d|%v %m%n\"";
	$scansResults=ExecCommand($Command);
	$devicesResults=explode("\n",$scansResults);
	list($ScannerDevice,$ScannerModel)=explode("|",$devicesResults[0]);
	
	$Scanner=array();
	$Scanner["ScanDevice"]=$ScannerDevice;
	$Scanner["ScanModel"]=$ScannerModel;

	return $Scanner;
}

function Scan($device,$resolution,$format,$size,$destFileBase){
	global $PreviewDir;
	global $ScanImage;
	global $PNMtoJPEG;
	global $PNMtoPNG;
	global $ImageMagik;

	$DestFile=$PreviewDir.$destFileBase;
	$Command=$ScanImage." -d ".$device.
		" --resolution ".$resolution."dpi";
		
	// Apply size
	if($size=="A4"){
		$Command.=" -x 210 -y 297";
	}
	if($size=="A5Port"){
		$Command.=" -x 148 -y 210";
	}
	if($size=="A5Land"){
		$Command.=" -x 210 -y 148";
	}
	if($size=="Letter"){
		$Command.=" -x 216 -y 279";
	}
	
	// Apply format
	if($format=="jpg"){
		$DestFile.=".jpg";
		$Command.=" | {$PNMtoJPEG} --quality=100 > ".$DestFile;
		$Scan=ExecCommand($Command);
	}
	if($format=="png"){
		$DestFile.=".png";
		$Command.=" | {$PNMtoPNG} > ".$DestFile;
		$Scan=ExecCommand($Command);
	}
	if($format=="pdf"){
		$DestFile2=$DestFile.".jpg";
		$Command.=" | {$PNMtoJPEG} --quality=100 > ".$DestFile2;
		$Scan=ExecCommand($Command);
		$DestFile.=".pdf";
		$Command=$ImageMagik." ".$DestFile2." ".$DestFile;
		$Scan=ExecCommand($Command);
	}
	return $DestFile;
}

function CleanUp(){
	global $PreviewDir;
	$Command="rm -rf "
		.$PreviewDir."*.pnm "
		.$PreviewDir."*.png "
		.$PreviewDir."*.jpg "
		.$PreviewDir."*.pdf ";
	$Delete=ExecCommand($Command);
}

function MoveToDest($origFile){
	global $FinalDestDir;
	$destFile=basename($origFile);
	$destFile=$FinalDestDir.$destFile;
	$Command="cp ".$origFile." ".$destFile;
	$Copy=ExecCommand($Command);
}

function CropImage($file){
	global $ImageMagik;
	global $CropFuzz;
	$Command=$ImageMagik." ".$file.' -fuzz '.$CropFuzz.'% -trim '.$file."\n";
	$Cropping=ExecCommand($Command);
}
