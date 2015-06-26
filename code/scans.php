<?php

include_once "config.php";
include_once "utils.php";
include_once "ui.php";

$Commands=array();

function ExecCommand($command){
	global $Commands;
	$loggedCommand=array();
	$loggedCommand["Command"]=$command;
	list($returnCode, $stdout, $stderr)=ExecFull($command);
	$loggedCommand["Result"]=$stdout;
	$loggedCommand["Error"]=$stderr;
	$loggedCommand["ReturnCode"]=$returnCode;
	$Commands[]=$loggedCommand;
	return $stdout;
}

function ShowCommandLog(){
	global $Commands;
	foreach($Commands as $loggedCommand){
		echo RenderParagraph($loggedCommand["Command"],"font-weight: bold;");
		echo RenderParagraph($loggedCommand["Result"]);
		echo RenderParagraph($loggedCommand["Error"],"color: red;");
	}
}


function Scan($device,$resolution,$format,$destFileBase){
	global $PreviewDir;
	global $ScanImage;
	global $PNMtoJPEG;
	global $PNMtoPNG;

	$DestFile=$PreviewDir.$destFileBase;
	$Command=$ScanImage." -d ".$device.
		" --resolution ".$resolution."dpi ";
	if($format=="jpg"){
		$DestFile.=".jpg";
		$Command.=" | {$PNMtoJPEG} --quality=100 > ".$DestFile;
	}
	if($format=="png"){
		$DestFile.=".png";
		$Command.=" | {$PNMtoPNG} > ".$DestFile;
	}
	$Scan=ExecCommand($Command);
	return $DestFile;
}

function CleanUp(){
	global $Commands;
	global $PreviewDir;
	$Command="rm -rf ".$PreviewDir."*.png";
	$Delete=ExecCommand($Command);
	$Command="rm -rf ".$PreviewDir."*.jpg";
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


// Detect scanner
$CMD=$ScanImage." --list-devices | grep device";
$SaneScanner = ExecCommand($CMD);
unset($cmd);
$start=strpos($SaneScanner,"`")+1;
$laenge=strpos($SaneScanner,"'")-$start;
$Scanner = "\"".substr($SaneScanner,$start,$laenge)."\"";
unset($start);
unset($laenge);


// Override config
$Resolution=RequestParm("ddlResolution",$Resolution);
$Format=RequestParm("ddlFormat",$Format);
$Crop=RequestParm("chkCrop",$Crop)!=false;
$CropFuzz=RequestParm("txtCropFuzz",$CropFuzz);

// Preprocess
$DestFile=null;
if(RequestParm("btnScan",false)){
	CleanUp();
	if($Crop){
		$baseName="Scan-".date("Y-m-d_H_i_s");
		$DestFile=Scan($Scanner,$Resolution,$Format,$baseName);
		CropImage($DestFile);
		CropImage($DestFile);
	}else{
		$baseName="Scan-".date("Y-m-d_H_i_s");
		$DestFile=Scan($Scanner,$Resolution,$Format,$baseName);
	}
	MoveToDest($DestFile);
}


echo '<form id="frmMain" method="GET" action="index.php">'."\n";

// Render header info
echo RenderFieldInfo("Scanner",$SaneScanner);
echo RenderFieldCombo("Resolution","ddlResolution",$Resolutions,$Resolution);
echo RenderFieldCombo("Format","ddlFormat",$Formats,$Format);
echo RenderFieldCheckText("Cropping","chkCrop",$Crop,"txtCropFuzz",$CropFuzz);
echo RenderFieldButton("","btnScan","Scan");
if($DestFile!=null){
	$DestFileFixed=htmlentities($DestFile,ENT_HTML5, "UTF-8");
	echo '<div><a href="'.$DestFileFixed.'">'.
		'Download '.$DestFileFixed.'</a></div>'."\n";
	echo '<div><img alt="preview" src="'.$DestFileFixed.'" '.
		'class="previewImage" /></div>';
}

echo "</form>\n";
ShowCommandLog();

